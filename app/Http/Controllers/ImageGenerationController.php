<?php

namespace App\Http\Controllers;

use App\Services\AiNarayaGeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImageGenerationController extends Controller
{
    public function __construct(private readonly AiNarayaGeminiService $gemini)
    {
    }

    public function geminiTest()
    {
        try {
            $responseData = $this->gemini->generateText('Balas singkat dalam bahasa Indonesia: Gemini API AI-Naraya sudah tersambung.');
            $text = $this->gemini->extractText($responseData) ?: 'Gemini berhasil merespons, tetapi format teks tidak terbaca.';

            return response()->json([
                'success' => true,
                'message' => $text,
                'raw' => $responseData,
            ]);
        } catch (\Throwable $error) {
            return $this->errorResponse('GEMINI TEST ERROR', 'Request ke Gemini gagal. Cek API key, koneksi internet, model, atau billing/free tier.', $error, true);
        }
    }

    public function generateGabung(Request $request)
    {
        $request->validate([
            'images' => ['required', 'array', 'min:2', 'max:5'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp'],
            'prompt' => ['nullable', 'string'],
            'ratio' => ['required', 'string'],
            'count' => ['required', 'integer', 'min:1', 'max:8'],
        ]);

        $count = (int) $request->input('count', 1);
        $ratio = $this->gemini->normalizeRatio($request->input('ratio', '1:1'));
        $prompt = trim((string) $request->input('prompt'));

        if ($prompt === '') {
            $prompt = 'Gabungkan semua gambar menjadi satu komposisi visual yang natural, realistis, cinematic, pencahayaan soft premium, warna harmonis, detail tajam, dan background bersih. Hasil tidak boleh terlihat seperti tempelan.';
        }

        try {
            $parts = [];

            foreach ($request->file('images') as $image) {
                $parts[] = $this->gemini->filePart($image);
            }

            $parts[] = [
                'text' => "Instruksi: {$prompt}\nGunakan semua gambar referensi yang diberikan. Buat satu gambar final dengan rasio {$ratio}.",
            ];

            $images = $this->gemini->makeImages($parts, $count, $ratio, 'generations/gabung', 'Hasil Gabung');

            return response()->json([
                'success' => true,
                'images' => $images,
                'message' => 'Generate gabung foto berhasil.',
            ]);
        } catch (\Throwable $error) {
            return $this->errorResponse('GABUNG GENERATE ERROR', $error->getMessage(), $error);
        }
    }

    public function generateEdit(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp'],
            'prompt' => ['nullable', 'string'],
            'ratio' => ['required', 'string'],
            'count' => ['required', 'integer', 'min:1', 'max:8'],
        ]);

        $count = (int) $request->input('count', 1);
        $ratio = $this->gemini->normalizeRatio($request->input('ratio', '1:1'));
        $prompt = trim((string) $request->input('prompt'));

        if ($prompt === '') {
            $prompt = 'Edit foto ini agar terlihat profesional, realistis, natural, clean, high detail, pencahayaan lebih rapi, warna harmonis, dan siap dipakai untuk konten visual.';
        }

        try {
            $parts = [
                $this->gemini->filePart($request->file('image')),
                [
                    'text' => "Instruksi edit: {$prompt}\nPertahankan identitas subjek utama. Jangan merusak wajah, tangan, produk, logo, atau bentuk utama. Buat gambar final dengan rasio {$ratio}.",
                ],
            ];

            $images = $this->gemini->makeImages($parts, $count, $ratio, 'generations/edit', 'Hasil Edit');

            return response()->json([
                'success' => true,
                'images' => $images,
                'message' => 'Generate edit foto berhasil.',
            ]);
        } catch (\Throwable $error) {
            return $this->errorResponse('EDIT GENERATE ERROR', $error->getMessage(), $error);
        }
    }

    public function generateArtist(Request $request)
    {
        $request->validate([
            'product_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'model_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'logo_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'prompt' => ['nullable', 'string', 'max:700'],
            'ratio' => ['required', 'in:1:1,4:5,9:16,16:9,3:2'],
            'count' => ['required', 'integer', 'in:4,10'],
        ], [
            'product_image.required' => 'Foto produk wajib diunggah.',
            'product_image.max' => 'Ukuran foto produk maksimal 10 MB.',
            'model_image.required' => 'Foto model/artis wajib diunggah.',
            'model_image.max' => 'Ukuran foto model/artis maksimal 10 MB.',
            'logo_image.max' => 'Ukuran logo maksimal 5 MB.',
            'logo_image.image' => 'Logo harus berupa file gambar.',
            'prompt.max' => 'Prompt maksimal 700 karakter.',
            'ratio.in' => 'Rasio gambar yang dipilih tidak tersedia.',
            'count.in' => 'Pilihan hasil hanya tersedia dalam mode Biasa (4) atau Booster (10).',
        ]);

        $count = (int) $request->input('count', 1);
        $ratio = $this->gemini->normalizeRatio($request->input('ratio', '1:1'));
        $prompt = trim((string) $request->input('prompt'));

        if ($prompt === '') {
            $prompt = 'Buat visual campaign produk premium. Model/artis terlihat natural menampilkan produk, produk tetap tajam dan jelas, pencahayaan studio profesional, background elegan, komposisi iklan modern, high-end commercial photography.';
        }

        try {
            $parts = [
                $this->gemini->filePart($request->file('product_image')),
                $this->gemini->filePart($request->file('model_image')),
            ];

            $logoInstruction = 'Tidak ada logo tambahan.';

            if ($request->hasFile('logo_image')) {
                $parts[] = $this->gemini->filePart($request->file('logo_image'));
                $logoInstruction = 'Gambar ketiga adalah logo brand. Tampilkan logo secara utuh, proporsional, terbaca, tidak terdistorsi, dan letakkan secara elegan tanpa menutupi produk atau wajah model.';
            }

            $parts[] = [
                'text' => "Gambar pertama adalah produk dan gambar kedua adalah model/artis. {$logoInstruction}\nInstruksi campaign: {$prompt}\nBuat gambar final dengan rasio {$ratio}. Pertahankan identitas model, bentuk dan label produk, serta warna brand. Produk wajib tajam, jelas, realistis, dan terlihat natural bersama model. Hindari tangan cacat, teks acak, logo ganda, watermark tambahan, dan elemen yang terpotong.",
            ];

            $images = $this->gemini->makeImages($parts, $count, $ratio, 'generations/artist', 'Campaign');
            $results = array_map(function ($image, $index) use ($ratio) {
                return [
                    'url' => $image['url'],
                    'title' => 'Campaign ' . ($index + 1),
                    'meta' => "Product Artist • {$ratio}",
                ];
            }, $images, array_keys($images));

            return response()->json([
                'success' => true,
                'results' => $results,
                'message' => 'Generate product artist berhasil.',
            ]);
        } catch (\Throwable $error) {
            return $this->errorResponse('ARTIST GENERATE ERROR', $error->getMessage(), $error);
        }
    }

    public function renderCarousel(Request $request)
    {
        $request->validate([
            'storyboard_pdf' => ['required', 'file', 'mimes:pdf', 'max:30720'],
            'reel_number' => ['required', 'integer', 'min:1', 'max:100'],
            'aspect_ratio' => ['required', 'in:1:1,4:5,9:16,16:9'],
            'extra_prompt' => ['nullable', 'string', 'max:700'],
            'reference_photos' => ['nullable', 'array', 'max:10'],
            'reference_photos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'storyboard_pdf.required' => 'PDF storyboard wajib diunggah.',
            'storyboard_pdf.max' => 'Ukuran PDF storyboard maksimal 30 MB.',
            'reel_number.integer' => 'Nomor reel harus berupa angka.',
            'reel_number.min' => 'Nomor reel minimal 1.',
            'reel_number.max' => 'Nomor reel maksimal 100.',
            'aspect_ratio.in' => 'Aspect ratio yang dipilih tidak tersedia.',
            'extra_prompt.max' => 'Kondisi tambahan maksimal 700 karakter.',
            'reference_photos.max' => 'Foto referensi maksimal 10 file.',
            'reference_photos.*.max' => 'Ukuran setiap foto referensi maksimal 10 MB.',
        ]);

        $reel = $request->input('reel_number', 1);
        $ratio = $this->gemini->normalizeRatio($request->input('aspect_ratio', '16:9'));
        $extraPrompt = trim((string) $request->input('extra_prompt'));

        try {
            $parts = $this->storyboardParts($request);
            $prompt = "Baca seluruh PDF storyboard dan cari bagian yang secara eksplisit bernomor Reel {$reel}, Ide Konten {$reel}, Konten {$reel}, atau Carousel {$reel}. Abaikan nomor halaman, nomor slide, versi, tanggal, dan angka lain yang bukan penanda ide. Render hanya bagian tersebut menjadi 4 scene/panel gambar yang saling konsisten, cinematic, rapi, dan siap dipakai untuk carousel/storyboard. Rasio output {$ratio}.";

            if ($extraPrompt !== '') {
                $prompt .= "\nKondisi tambahan: {$extraPrompt}";
            }

            $prompt .= "\nJika bagian bernomor tersebut tidak ditemukan dengan yakin, jangan mengambil ide lain. Jika ada foto referensi, gunakan untuk menjaga konsistensi wajah, karakter, pakaian, dan model. Pertahankan detail penting antar-scene. Hindari teks acak, tangan cacat, watermark tambahan, serta perubahan identitas. Kembalikan gambar final, bukan hanya penjelasan teks.";
            $parts[] = ['text' => $prompt];

            $images = $this->gemini->makeImages($parts, 4, $ratio, 'generations/carousel', "Reel {$reel} Scene");

            return response()->json([
                'success' => true,
                'results' => $this->carouselResults($images, $reel, $ratio),
                'message' => 'Render carousel berhasil.',
            ]);
        } catch (\Throwable $error) {
            return $this->errorResponse('CAROUSEL RENDER ERROR', $error->getMessage(), $error);
        }
    }

    public function renderKorosel(Request $request)
    {
        $request->validate([
            'storyboard_pdf' => ['required', 'file', 'mimes:pdf'],
            'reel_number' => ['required'],
            'aspect_ratio' => ['required', 'string'],
            'extra_prompt' => ['nullable', 'string'],
            'reference_photos' => ['nullable', 'array'],
            'reference_photos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
        ]);

        $reel = $request->input('reel_number', 1);
        $ratio = $this->gemini->normalizeRatio($request->input('aspect_ratio', '16:9'));
        $extraPrompt = trim((string) $request->input('extra_prompt'));

        try {
            $parts = $this->storyboardParts($request);
            $prompt = "Baca PDF storyboard yang diupload. Render visual untuk Reel {$reel} saja. Buat 2 scene/panel gambar yang konsisten, cinematic, rapi, dan siap dipakai untuk carousel/storyboard. Rasio output {$ratio}.";

            if ($extraPrompt !== '') {
                $prompt .= "\nKondisi tambahan: {$extraPrompt}";
            }

            $prompt .= "\nJika ada foto referensi, gunakan untuk menjaga konsistensi karakter/model. Kembalikan gambar final, bukan hanya penjelasan teks.";
            $parts[] = ['text' => $prompt];

            $images = $this->gemini->makeImages($parts, 2, $ratio, 'generations/carousel', "Reel {$reel} Scene");

            return response()->json([
                'success' => true,
                'results' => $this->carouselResults($images, $reel, $ratio),
                'message' => 'Render korosel berhasil.',
            ]);
        } catch (\Throwable $error) {
            return $this->errorResponse('KOROSEL RENDER ERROR', $error->getMessage(), $error);
        }
    }

    private function storyboardParts(Request $request): array
    {
        $parts = [
            $this->gemini->filePart($request->file('storyboard_pdf')),
        ];

        foreach ($request->file('reference_photos', []) as $referencePhoto) {
            $parts[] = $this->gemini->filePart($referencePhoto);
        }

        return $parts;
    }

    private function carouselResults(array $images, mixed $reel, string $ratio): array
    {
        return array_map(function ($image, $index) use ($reel, $ratio) {
            return [
                'title' => "Reel {$reel} - Scene " . ($index + 1),
                'url' => $image['url'],
                'status' => 'Completed',
                'prompt' => "Hasil render Reel {$reel} dengan rasio {$ratio}.",
            ];
        }, $images, array_keys($images));
    }

    private function errorResponse(string $logTitle, string $message, \Throwable $error, bool $includeError = false)
    {
        Log::error($logTitle, [
            'message' => $error->getMessage(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
        ]);

        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($includeError) {
            $payload['error'] = $error->getMessage();
        }

        return response()->json($payload, 500);
    }
}
