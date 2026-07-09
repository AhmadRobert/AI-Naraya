<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AiNarayaGeminiService
{
    public function generateText(string $prompt, ?string $model = null): array
    {
        $model = $model ?: $this->textModel();
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $response = Http::timeout(60)
            ->retry(2, 1000)
            ->withHeaders($this->requestHeaders())
            ->post($endpoint, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ]);

        $data = $response->json() ?? [];

        if (! $response->successful()) {
            Log::error('GEMINI TEXT API ERROR', [
                'status' => $response->status(),
                'model' => $model,
                'response' => $data,
            ]);

            $message = data_get($data, 'error.message', 'Gemini text API gagal tanpa pesan detail.');
            throw new Exception("Gemini text gagal. Status {$response->status()}: {$message}");
        }

        return $data;
    }

    public function extractText(array $responseData): string
    {
        $texts = [];
        $candidates = $responseData['candidates'] ?? [];

        foreach ($candidates as $candidate) {
            $parts = data_get($candidate, 'content.parts', []);

            foreach ($parts as $part) {
                if (! empty($part['text'])) {
                    $texts[] = $part['text'];
                }
            }
        }

        return trim(implode("\n", $texts));
    }

    public function makeImages(array $baseParts, int $count, string $ratio, string $folder, string $titlePrefix): array
    {
        $count = min(max($count, 1), 10);
        $ratio = $this->normalizeRatio($ratio);
        $results = [];

        for ($i = 1; $i <= $count; $i += 1) {
            $parts = $baseParts;
            $parts[] = [
                'text' => "\n\nBuat variasi ke-{$i}. Hasil harus berupa gambar final, bukan deskripsi teks. Pertahankan proporsi natural, detail tajam, pencahayaan premium, tidak distorsi, dan siap dipakai sebagai konten profesional.",
            ];

            $responseData = $this->generateImage($parts, $ratio);
            $images = $this->saveImages($responseData, $folder, "{$titlePrefix} {$i}");

            foreach ($images as $image) {
                $results[] = [
                    'url' => $image['url'],
                    'title' => "{$titlePrefix} {$i}",
                ];
            }
        }

        if (! count($results)) {
            throw new Exception('Gemini berhasil merespons, tetapi tidak mengirim gambar. Coba prompt lebih jelas atau pakai model image lain seperti gemini-3.1-flash-image.');
        }

        return array_slice($results, 0, $count);
    }

    public function filePart(UploadedFile $file): array
    {
        $mimeType = $file->getMimeType()
            ?: $file->getClientMimeType()
            ?: 'application/octet-stream';

        return [
            'inline_data' => [
                'mime_type' => $mimeType,
                'data' => base64_encode(file_get_contents($file->getRealPath())),
            ],
        ];
    }

    public function normalizeRatio(?string $ratio): string
    {
        $allowed = ['1:1', '16:9', '9:16', '4:5', '3:2', '5:4', '2:3', '3:4'];

        return in_array($ratio, $allowed, true) ? $ratio : '1:1';
    }

    private function generateImage(array $parts, string $ratio = '1:1', ?string $model = null): array
    {
        $model = $model ?: $this->imageModel();
        $ratio = $this->normalizeRatio($ratio);
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $response = Http::timeout(180)
            ->withHeaders($this->requestHeaders())
            ->post($endpoint, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => $parts,
                    ],
                ],
                'generationConfig' => [
                    'responseModalities' => ['TEXT', 'IMAGE'],
                    'imageConfig' => [
                        'aspectRatio' => $ratio,
                    ],
                ],
            ]);

        $data = $response->json() ?? [];

        if (! $response->successful()) {
            Log::error('GEMINI IMAGE API ERROR', [
                'status' => $response->status(),
                'model' => $model,
                'ratio' => $ratio,
                'response' => $data,
            ]);

            if ($response->status() === 429) {
                throw new Exception('Kuota Gemini image untuk project ini habis atau belum aktif. Aktifkan billing, periksa quota Generative Language API di Google Cloud, lalu coba lagi.');
            }

            $message = data_get($data, 'error.message', 'Gemini image API gagal tanpa pesan detail.');
            throw new Exception("Gemini image gagal. Status {$response->status()}: {$message}");
        }

        return $data;
    }

    private function saveImages(array $responseData, string $folder, string $titlePrefix = 'AI-Naraya'): array
    {
        $savedImages = [];
        $candidates = $responseData['candidates'] ?? [];

        foreach ($candidates as $candidate) {
            $parts = data_get($candidate, 'content.parts', []);

            foreach ($parts as $part) {
                $inlineData = $part['inlineData'] ?? $part['inline_data'] ?? null;

                if (! $inlineData || empty($inlineData['data'])) {
                    continue;
                }

                $mimeType = $inlineData['mimeType'] ?? $inlineData['mime_type'] ?? 'image/png';
                $extension = match ($mimeType) {
                    'image/jpeg', 'image/jpg' => 'jpg',
                    'image/webp' => 'webp',
                    default => 'png',
                };

                $path = trim($folder, '/') . '/' . Str::uuid() . '.' . $extension;

                Storage::disk('public')->put($path, base64_decode($inlineData['data']));

                $savedImages[] = [
                    'url' => asset('storage/' . $path),
                    'title' => $titlePrefix,
                ];
            }
        }

        return $savedImages;
    }

    private function requestHeaders(): array
    {
        return [
            'x-goog-api-key' => $this->apiKey(),
            'Content-Type' => 'application/json',
        ];
    }

    private function apiKey(): string
    {
        $apiKey = env('GEMINI_API_KEY');

        if (! $apiKey) {
            throw new Exception('GEMINI_API_KEY belum diisi di file .env.');
        }

        return $apiKey;
    }

    private function imageModel(): string
    {
        return env('GEMINI_IMAGE_MODEL', 'gemini-2.5-flash-image');
    }

    private function textModel(): string
    {
        return env('GEMINI_TEXT_MODEL', 'gemini-2.5-flash');
    }
}
