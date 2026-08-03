<?php

namespace App\Services\Providers;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GrokImageService
{
    protected string $apiKey;

    protected string $model;

    protected string $textModel;

    protected string $baseUrl = 'https://api.x.ai/v1';

    protected int $maxSourceImages = 3;

    protected int $maxScenes = 30;

    public function __construct()
    {
        $this->apiKey = config('services.grok.api_key');

        $this->model = config('services.grok.image_model', 'grok-imagine-image');

        // FIX: sebelumnya baris ini baca config('services.grok.model')
        // (model teks biasa untuk Caption/Storyboard), padahal seharusnya
        // baca config('services.grok.file_model') yang sudah ditambahkan
        // khusus untuk analisis PDF storyboard di Carousel. Akibatnya
        // GROK_FILE_MODEL di .env tidak pernah benar-benar dipakai.
        $this->textModel = config('services.grok.file_model', 'grok-4.3');
    }

    /*
    |--------------------------------------------------------------------------
    | Primitif render gambar - dipakai ImageService untuk Produk Artist
    | dan render per-scene Carousel.
    |--------------------------------------------------------------------------
    */

    public function generate(array $imageFiles, string $prompt, string $ratio, int $count = 1): array
    {
        if (! $this->apiKey) {
            throw new Exception('GROK_API_KEY belum diisi di .env.');
        }

        $count = max(1, min($count, 8));

        $imageFiles = array_values(array_filter(
            $imageFiles,
            fn($file) => $file instanceof UploadedFile
        ));

        if (count($imageFiles) > $this->maxSourceImages) {
            throw new Exception(
                "Grok Image Edit hanya mendukung maksimal {$this->maxSourceImages} gambar sekaligus."
            );
        }

        $images = [];
        $lastError = null;

        for ($i = 0; $i < $count; $i++) {

            if ($i > 0) {
                sleep(3);
            }

            try {

                $json = empty($imageFiles)
                    ? $this->requestTextToImage($prompt, $ratio)
                    : $this->requestImageEdit($imageFiles, $prompt, $ratio);

                $images = array_merge($images, $this->extractImages($json));
            } catch (Throwable $e) {

                $lastError = $e;

                if (empty($images)) {
                    throw $e;
                }

                break;
            }
        }

        if (empty($images)) {
            throw $lastError ?? new Exception('Grok tidak mengembalikan gambar.');
        }

        return [
            'provider' => 'grok',
            'images' => $images,
        ];
    }

    protected function requestTextToImage(string $prompt, string $ratio = ''): array
    {
        $payload = [
            'model' => $this->model,
            'prompt' => $prompt,
        ];

        if ($ratio !== '') {
            $payload['aspect_ratio'] = $ratio;
        }

        $response = Http::timeout(180)
            ->connectTimeout(15)
            ->withToken($this->apiKey)
            ->asJson()
            ->post("{$this->baseUrl}/images/generations", $payload);

        return $this->handleResponse($response, 'text-to-image');
    }

    protected function requestImageEdit(array $imageFiles, string $prompt, string $ratio = ''): array
    {
        $payload = [
            'model' => $this->model,
            'prompt' => $prompt,
        ];

        if ($ratio !== '') {
            $payload['aspect_ratio'] = $ratio;
        }

        if (count($imageFiles) === 1) {

            $payload['image'] = [
                'url' => $this->toDataUri($imageFiles[0]),
            ];
        } else {

            $payload['images'] = array_map(
                fn(UploadedFile $file) => [
                    'type' => 'image_url',
                    'url' => $this->toDataUri($file),
                ],
                $imageFiles
            );
        }

        $response = Http::timeout(180)
            ->connectTimeout(15)
            ->withToken($this->apiKey)
            ->asJson()
            ->post("{$this->baseUrl}/images/edits", $payload);

        return $this->handleResponse($response, 'image-edit (' . count($imageFiles) . ' gambar)');
    }

    protected function toDataUri(UploadedFile $file): string
    {
        return sprintf(
            'data:%s;base64,%s',
            $file->getMimeType(),
            base64_encode(file_get_contents($file->getRealPath()))
        );
    }

    protected function extractImages(array $json): array
    {
        $images = [];

        foreach (data_get($json, 'data', []) as $item) {

            $b64 = data_get($item, 'b64_json');

            if ($b64) {
                $images[] = [
                    'mime_type' => 'image/jpeg',
                    'base64' => $b64,
                ];
                continue;
            }

            $url = data_get($item, 'url');

            if ($url) {
                $downloaded = $this->downloadAsBase64($url);

                if ($downloaded) {
                    $images[] = $downloaded;
                }
            }
        }

        return $images;
    }

    protected function downloadAsBase64(string $url): ?array
    {
        try {

            $response = Http::timeout(60)->get($url);

            if (! $response->successful()) {
                return null;
            }

            $mime = $response->header('Content-Type') ?: 'image/jpeg';

            return [
                'mime_type' => $mime,
                'base64' => base64_encode($response->body()),
            ];
        } catch (Throwable $e) {

            Log::warning('Grok: gagal download hasil dari url', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * FIX: sebelumnya kalau xAI balikin error dalam bentuk yang tidak
     * sesuai dugaan kode ({"error":{"message":...}} atau {"message":...}),
     * exception yang dilempar cuma teks generik "Grok Image Error." -
     * user (dan kita) sama sekali tidak tahu penyebab aslinya. Sekarang
     * body mentah (dipotong 500 karakter) ikut disertakan di pesan error
     * dan di log, supaya penyebab sebenarnya selalu kelihatan.
     */
    protected function handleResponse(Response $response, string $context = ''): array
    {
        $json = $response->json();

        if ($response->successful() && is_array($json)) {
            return $json;
        }

        $rawBody = $response->body();
        $rawBodyTrimmed = mb_substr($rawBody, 0, 500);

        Log::error("Grok Image Error [{$context}]", [
            'status' => $response->status(),
            'body' => $rawBody,
        ]);

        $message = data_get($json, 'error.message')
            ?? data_get($json, 'message')
            ?? (is_string(data_get($json, 'error')) ? $json['error'] : null);

        if (! $message) {
            // Body bukan JSON yang kita duga (bisa HTML 404, bisa shape
            // error lain) - tampilkan mentah-mentah supaya kelihatan.
            $message = sprintf(
                'Grok Image Error [%s] - HTTP %d: %s',
                $context,
                $response->status(),
                $rawBodyTrimmed !== '' ? $rawBodyTrimmed : '(response kosong)'
            );
        }

        throw new Exception($message);
    }

    public function analyzeScenes(UploadedFile $storyboardPdf): array
    {
        $instruction = <<<PROMPT
Kamu akan membaca sebuah PDF storyboard iklan (terlampir).

PDF ini bisa berisi:
(a) SATU storyboard iklan (satu ide/reel) dengan beberapa Scene berurutan
    (Scene 1, Scene 2, dst) - ini kasus PALING UMUM, atau
(b) BEBERAPA storyboard/ide reel terpisah yang digabung dalam satu PDF,
    di mana masing-masing ide/reel punya penomoran Scene sendiri yang
    dimulai ulang dari Scene 1.

Identifikasi mana dari kedua kasus di atas berdasarkan isi PDF apa adanya.
Untuk SETIAP scene, tentukan scene tersebut adalah bagian dari reel/ide
keberapa lewat field "reel". Kalau PDF hanya berisi SATU storyboard/ide
(kasus (a)), maka SEMUA scene wajib punya "reel": 1.

JANGAN membatasi atau menambah-nambah jumlah scene maupun jumlah reel -
ikuti persis apa yang benar-benar tertulis di PDF.

Balas HANYA dalam format JSON array (tanpa teks lain, tanpa markdown code fence):
[
  {"reel": 1, "scene": 1, "visual": "deskripsi visual", "camera": "sudut kamera", "mood": "suasana"}
]
PROMPT;

        $fileId = null;

        try {

            $fileId = $this->uploadFile($storyboardPdf);

            $response = Http::timeout(180)
                ->connectTimeout(15)
                ->withToken($this->apiKey)
                ->asJson()
                ->post("{$this->baseUrl}/responses", [
                    'model' => $this->textModel,
                    'input' => [[
                        'role' => 'user',
                        'content' => [
                            ['type' => 'input_text', 'text' => $instruction],
                            ['type' => 'input_file', 'file_id' => $fileId],
                        ],
                    ]],
                ]);

            $json = $this->handleResponse($response, 'analyze-storyboard');

            $text = $this->extractResponseText($json);

            $scenes = $this->parseScenesJson($text);

            if (! empty($scenes)) {
                return array_slice($scenes, 0, $this->maxScenes);
            }
        } catch (Throwable $e) {

            Log::warning('Grok gagal analisis storyboard PDF, fallback ke 1 scene', [
                'error' => $e->getMessage(),
            ]);
        } finally {

            if ($fileId) {
                $this->deleteFile($fileId);
            }
        }

        return [$this->fallbackSingleScene()];
    }

    protected function uploadFile(UploadedFile $file): string
    {
        $response = Http::timeout(120)
            ->connectTimeout(15)
            ->withToken($this->apiKey)
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post("{$this->baseUrl}/files", [
                'purpose' => 'assistants',
            ]);

        $json = $this->handleResponse($response, 'upload-file');

        $fileId = data_get($json, 'id');

        if (! $fileId) {
            throw new Exception('Gagal upload storyboard PDF ke xAI Files API (response tidak berisi id file).');
        }

        return $fileId;
    }

    protected function deleteFile(string $fileId): void
    {
        try {

            Http::timeout(30)
                ->withToken($this->apiKey)
                ->delete("{$this->baseUrl}/files/{$fileId}");
        } catch (Throwable $e) {

            Log::warning('Grok: gagal menghapus file sementara di Files API', [
                'file_id' => $fileId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function extractResponseText(array $json): string
    {
        foreach (data_get($json, 'output', []) as $item) {

            if (($item['type'] ?? null) === 'message') {

                foreach ($item['content'] ?? [] as $content) {

                    if (($content['type'] ?? null) === 'output_text') {
                        return $content['text'] ?? '';
                    }
                }
            }
        }

        return (string) data_get($json, 'output_text', '');
    }

    protected function fallbackSingleScene(): array
    {
        return [
            'scene' => 1,
            'visual' => 'Render sesuai isi storyboard secara keseluruhan.',
            'camera' => 'medium shot',
            'mood' => 'sesuai storyboard',
        ];
    }

    protected function parseScenesJson(string $text): array
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?/i', '', $text);
        $text = preg_replace('/```$/', '', $text);
        $text = trim($text);

        $decoded = json_decode($text, true);

        if (! is_array($decoded)) {
            return [];
        }

        $scenes = [];
        $i = 1;

        foreach ($decoded as $item) {

            if (! is_array($item)) {
                continue;
            }

            $scenes[] = [
                'scene' => (int) ($item['scene'] ?? $i),
                'visual' => (string) ($item['visual'] ?? ''),
                'camera' => (string) ($item['camera'] ?? ''),
                'mood' => (string) ($item['mood'] ?? ''),
            ];

            $i++;
        }

        return $scenes;
    }
}