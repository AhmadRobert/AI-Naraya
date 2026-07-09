<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuthController;
use App\Models\User;

if (! function_exists('aiNarayaGeminiApiKey')) {
    function aiNarayaGeminiApiKey(): string
    {
        $apiKey = env('GEMINI_API_KEY');

        if (! $apiKey) {
            throw new Exception('GEMINI_API_KEY belum diisi di file .env.');
        }

        return $apiKey;
    }
}

if (! function_exists('aiNarayaGeminiRequestHeaders')) {
    function aiNarayaGeminiRequestHeaders(): array
    {
        return [
            'x-goog-api-key' => aiNarayaGeminiApiKey(),
            'Content-Type' => 'application/json',
        ];
    }
}

if (! function_exists('aiNarayaImageModel')) {
    function aiNarayaImageModel(): string
    {
        return env('GEMINI_IMAGE_MODEL', 'gemini-2.5-flash-image');
    }
}

if (! function_exists('aiNarayaTextModel')) {
    function aiNarayaTextModel(): string
    {
        return env('GEMINI_TEXT_MODEL', 'gemini-2.5-flash');
    }
}

if (! function_exists('aiNarayaNormalizeRatio')) {
    function aiNarayaNormalizeRatio(?string $ratio): string
    {
        $allowed = ['1:1', '16:9', '9:16', '4:5', '3:2', '5:4', '2:3', '3:4'];

        return in_array($ratio, $allowed, true) ? $ratio : '1:1';
    }
}

if (! function_exists('aiNarayaFilePart')) {
    function aiNarayaFilePart($file): array
    {
        $mimeType = $file->getMimeType()
            ?: $file->getClientMimeType()
            ?: 'application/octet-stream';

        // Format REST Gemini yang aman untuk input file/gambar/PDF.
        return [
            'inline_data' => [
                'mime_type' => $mimeType,
                'data' => base64_encode(file_get_contents($file->getRealPath())),
            ],
        ];
    }
}

if (! function_exists('aiNarayaExtractGeminiText')) {
    function aiNarayaExtractGeminiText(array $responseData): string
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
}

if (! function_exists('aiNarayaSaveGeminiImages')) {
    function aiNarayaSaveGeminiImages(array $responseData, string $folder, string $titlePrefix = 'AI-Naraya'): array
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
}

if (! function_exists('aiNarayaGenerateGeminiImage')) {
    function aiNarayaGenerateGeminiImage(array $parts, string $ratio = '1:1', ?string $model = null): array
    {
        $model = $model ?: aiNarayaImageModel();
        $ratio = aiNarayaNormalizeRatio($ratio);

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $response = Http::timeout(180)
            ->withHeaders(aiNarayaGeminiRequestHeaders())
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
}

if (! function_exists('aiNarayaGenerateGeminiText')) {
    function aiNarayaGenerateGeminiText(string $prompt, ?string $model = null): array
    {
        $model = $model ?: aiNarayaTextModel();

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $response = Http::timeout(60)
            ->retry(2, 1000)
            ->withHeaders(aiNarayaGeminiRequestHeaders())
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
}

if (! function_exists('aiNarayaMakeGeminiImages')) {
    function aiNarayaMakeGeminiImages(array $baseParts, int $count, string $ratio, string $folder, string $titlePrefix): array
    {
        $count = min(max($count, 1), 10);
        $results = [];

        for ($i = 1; $i <= $count; $i += 1) {
            $parts = $baseParts;
            $parts[] = [
                'text' => "\n\nBuat variasi ke-{$i}. Hasil harus berupa gambar final, bukan deskripsi teks. Pertahankan proporsi natural, detail tajam, pencahayaan premium, tidak distorsi, dan siap dipakai sebagai konten profesional.",
            ];

            $responseData = aiNarayaGenerateGeminiImage($parts, $ratio);
            $images = aiNarayaSaveGeminiImages($responseData, $folder, "{$titlePrefix} {$i}");

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
}

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/beli', function () {
    return view('beli');
})->name('beli');

/*
|--------------------------------------------------------------------------
| Developer Create Account
|--------------------------------------------------------------------------
| Buka: http://127.0.0.1:8000/buat-akun
| Kode developer diambil dari .env: DEV_CREATE_KEY
*/

Route::get('/buat-akun', function () {
    return view('developer-create-account');
})->name('developer.account.form');

Route::post('/buat-akun', function (Request $request) {
    $request->validate([
        'developer_key' => ['required', 'string'],
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
        'password' => ['required', 'string', 'min:6', 'confirmed'],
    ], [
        'developer_key.required' => 'Kode developer wajib diisi.',
        'name.required' => 'Nama wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 6 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak sama.',
    ]);

    $developerKey = env('DEV_CREATE_KEY', 'naraya-dev-2026');

    if (! hash_equals($developerKey, (string) $request->developer_key)) {
        return back()
            ->withErrors([
                'developer_key' => 'Kode developer salah.',
            ])
            ->onlyInput('name', 'email');
    }

    $email = strtolower(trim($request->email));

    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name' => trim($request->name),
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]
    );

    return back()->with([
        'success' => 'Akun berhasil dibuat dan sudah bisa dipakai login.',
        'created_name' => $user->name,
        'created_email' => $user->email,
    ]);
})->name('developer.account.store');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/gabung', function () {
        return view('gabung');
    })->name('gabung');

    Route::get('/edit', function () {
        return view('edit');
    })->name('edit');

    Route::get('/artist', function () {
        return view('artist');
    })->name('artist');

    Route::get('/carousel', function () {
        return view('carousel');
    })->name('carousel');

    Route::redirect('/korosel', '/carousel');
    Route::redirect('/Carousel', '/carousel');

    Route::get('/gemini-test', function () {
        try {
            $responseData = aiNarayaGenerateGeminiText('Balas singkat dalam bahasa Indonesia: Gemini API AI-Naraya sudah tersambung.');
            $text = aiNarayaExtractGeminiText($responseData) ?: 'Gemini berhasil merespons, tetapi format teks tidak terbaca.';

            return response()->json([
                'success' => true,
                'message' => $text,
                'raw' => $responseData,
            ]);
        } catch (\Throwable $error) {
            Log::error('GEMINI TEST ERROR', [
                'message' => $error->getMessage(),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Request ke Gemini gagal. Cek API key, koneksi internet, model, atau billing/free tier.',
                'error' => $error->getMessage(),
            ], 500);
        }
    })->name('gemini.test');

    Route::post('/gabung/generate', function (Request $request) {
        $request->validate([
            'images' => ['required', 'array', 'min:2', 'max:5'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp'],
            'prompt' => ['nullable', 'string'],
            'ratio' => ['required', 'string'],
            'count' => ['required', 'integer', 'min:1', 'max:8'],
        ]);

        $count = (int) $request->input('count', 1);
        $ratio = aiNarayaNormalizeRatio($request->input('ratio', '1:1'));
        $prompt = trim((string) $request->input('prompt'));

        if ($prompt === '') {
            $prompt = 'Gabungkan semua gambar menjadi satu komposisi visual yang natural, realistis, cinematic, pencahayaan soft premium, warna harmonis, detail tajam, dan background bersih. Hasil tidak boleh terlihat seperti tempelan.';
        }

        try {
            $parts = [];

            foreach ($request->file('images') as $image) {
                $parts[] = aiNarayaFilePart($image);
            }

            $parts[] = [
                'text' => "Instruksi: {$prompt}\nGunakan semua gambar referensi yang diberikan. Buat satu gambar final dengan rasio {$ratio}.",
            ];

            $images = aiNarayaMakeGeminiImages($parts, $count, $ratio, 'generations/gabung', 'Hasil Gabung');

            return response()->json([
                'success' => true,
                'images' => $images,
                'message' => 'Generate gabung foto berhasil.',
            ]);
        } catch (\Throwable $error) {
            Log::error('GABUNG GENERATE ERROR', [
                'message' => $error->getMessage(),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 500);
        }
    })->name('gabung.generate');

    Route::post('/edit/generate', function (Request $request) {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp'],
            'prompt' => ['nullable', 'string'],
            'ratio' => ['required', 'string'],
            'count' => ['required', 'integer', 'min:1', 'max:8'],
        ]);

        $count = (int) $request->input('count', 1);
        $ratio = aiNarayaNormalizeRatio($request->input('ratio', '1:1'));
        $prompt = trim((string) $request->input('prompt'));

        if ($prompt === '') {
            $prompt = 'Edit foto ini agar terlihat profesional, realistis, natural, clean, high detail, pencahayaan lebih rapi, warna harmonis, dan siap dipakai untuk konten visual.';
        }

        try {
            $parts = [
                aiNarayaFilePart($request->file('image')),
                [
                    'text' => "Instruksi edit: {$prompt}\nPertahankan identitas subjek utama. Jangan merusak wajah, tangan, produk, logo, atau bentuk utama. Buat gambar final dengan rasio {$ratio}.",
                ],
            ];

            $images = aiNarayaMakeGeminiImages($parts, $count, $ratio, 'generations/edit', 'Hasil Edit');

            return response()->json([
                'success' => true,
                'images' => $images,
                'message' => 'Generate edit foto berhasil.',
            ]);
        } catch (\Throwable $error) {
            Log::error('EDIT GENERATE ERROR', [
                'message' => $error->getMessage(),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 500);
        }
    })->name('edit.generate');

    Route::post('/artist/generate', function (Request $request) {
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
        $ratio = aiNarayaNormalizeRatio($request->input('ratio', '1:1'));
        $prompt = trim((string) $request->input('prompt'));

        if ($prompt === '') {
            $prompt = 'Buat visual campaign produk premium. Model/artis terlihat natural menampilkan produk, produk tetap tajam dan jelas, pencahayaan studio profesional, background elegan, komposisi iklan modern, high-end commercial photography.';
        }

        try {
            $parts = [
                aiNarayaFilePart($request->file('product_image')),
                aiNarayaFilePart($request->file('model_image')),
            ];

            $logoInstruction = 'Tidak ada logo tambahan.';

            if ($request->hasFile('logo_image')) {
                $parts[] = aiNarayaFilePart($request->file('logo_image'));
                $logoInstruction = 'Gambar ketiga adalah logo brand. Tampilkan logo secara utuh, proporsional, terbaca, tidak terdistorsi, dan letakkan secara elegan tanpa menutupi produk atau wajah model.';
            }

            $parts[] = [
                'text' => "Gambar pertama adalah produk dan gambar kedua adalah model/artis. {$logoInstruction}\nInstruksi campaign: {$prompt}\nBuat gambar final dengan rasio {$ratio}. Pertahankan identitas model, bentuk dan label produk, serta warna brand. Produk wajib tajam, jelas, realistis, dan terlihat natural bersama model. Hindari tangan cacat, teks acak, logo ganda, watermark tambahan, dan elemen yang terpotong.",
            ];

            $images = aiNarayaMakeGeminiImages($parts, $count, $ratio, 'generations/artist', 'Campaign');

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
            Log::error('ARTIST GENERATE ERROR', [
                'message' => $error->getMessage(),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 500);
        }
    })->name('artist.generate');

    Route::post('/carousel/render', function (Request $request) {
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
        $ratio = aiNarayaNormalizeRatio($request->input('aspect_ratio', '16:9'));
        $extraPrompt = trim((string) $request->input('extra_prompt'));

        try {
            $parts = [
                aiNarayaFilePart($request->file('storyboard_pdf')),
            ];

            foreach ($request->file('reference_photos', []) as $referencePhoto) {
                $parts[] = aiNarayaFilePart($referencePhoto);
            }

            $prompt = "Baca seluruh PDF storyboard dan cari bagian yang secara eksplisit bernomor Reel {$reel}, Ide Konten {$reel}, Konten {$reel}, atau Carousel {$reel}. Abaikan nomor halaman, nomor slide, versi, tanggal, dan angka lain yang bukan penanda ide. Render hanya bagian tersebut menjadi 4 scene/panel gambar yang saling konsisten, cinematic, rapi, dan siap dipakai untuk carousel/storyboard. Rasio output {$ratio}.";

            if ($extraPrompt !== '') {
                $prompt .= "\nKondisi tambahan: {$extraPrompt}";
            }

            $prompt .= "\nJika bagian bernomor tersebut tidak ditemukan dengan yakin, jangan mengambil ide lain. Jika ada foto referensi, gunakan untuk menjaga konsistensi wajah, karakter, pakaian, dan model. Pertahankan detail penting antar-scene. Hindari teks acak, tangan cacat, watermark tambahan, serta perubahan identitas. Kembalikan gambar final, bukan hanya penjelasan teks.";

            $parts[] = ['text' => $prompt];

            $images = aiNarayaMakeGeminiImages($parts, 4, $ratio, 'generations/carousel', "Reel {$reel} Scene");

            $results = array_map(function ($image, $index) use ($reel, $ratio) {
                return [
                    'title' => "Reel {$reel} - Scene " . ($index + 1),
                    'url' => $image['url'],
                    'status' => 'Completed',
                    'prompt' => "Hasil render Reel {$reel} dengan rasio {$ratio}.",
                ];
            }, $images, array_keys($images));

            return response()->json([
                'success' => true,
                'results' => $results,
                'message' => 'Render carousel berhasil.',
            ]);
        } catch (\Throwable $error) {
            Log::error('CAROUSEL RENDER ERROR', [
                'message' => $error->getMessage(),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 500);
        }
    })->name('carousel.render');

    Route::post('/korosel/render', function (Request $request) {
        $request->validate([
            'storyboard_pdf' => ['required', 'file', 'mimes:pdf'],
            'reel_number' => ['required'],
            'aspect_ratio' => ['required', 'string'],
            'extra_prompt' => ['nullable', 'string'],
            'reference_photos' => ['nullable', 'array'],
            'reference_photos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
        ]);

        $reel = $request->input('reel_number', 1);
        $ratio = aiNarayaNormalizeRatio($request->input('aspect_ratio', '16:9'));
        $extraPrompt = trim((string) $request->input('extra_prompt'));

        try {
            $parts = [
                aiNarayaFilePart($request->file('storyboard_pdf')),
            ];

            foreach ($request->file('reference_photos', []) as $referencePhoto) {
                $parts[] = aiNarayaFilePart($referencePhoto);
            }

            $prompt = "Baca PDF storyboard yang diupload. Render visual untuk Reel {$reel} saja. Buat 2 scene/panel gambar yang konsisten, cinematic, rapi, dan siap dipakai untuk carousel/storyboard. Rasio output {$ratio}.";

            if ($extraPrompt !== '') {
                $prompt .= "\nKondisi tambahan: {$extraPrompt}";
            }

            $prompt .= "\nJika ada foto referensi, gunakan untuk menjaga konsistensi karakter/model. Kembalikan gambar final, bukan hanya penjelasan teks.";

            $parts[] = ['text' => $prompt];

            $images = aiNarayaMakeGeminiImages($parts, 2, $ratio, 'generations/carousel', "Reel {$reel} Scene");

            $results = array_map(function ($image, $index) use ($reel, $ratio) {
                return [
                    'title' => "Reel {$reel} - Scene " . ($index + 1),
                    'url' => $image['url'],
                    'status' => 'Completed',
                    'prompt' => "Hasil render Reel {$reel} dengan rasio {$ratio}.",
                ];
            }, $images, array_keys($images));

            return response()->json([
                'success' => true,
                'results' => $results,
                'message' => 'Render korosel berhasil.',
            ]);
        } catch (\Throwable $error) {
            Log::error('KOROSEL RENDER ERROR', [
                'message' => $error->getMessage(),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 500);
        }
    })->name('korosel.render');
});
