<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GabungController extends Controller
{
    public function generate(Request $request)
    {
        // 1. Validasi Ketat: Izinkan array images MINIMAL 2 dan MAKSIMAL 3
        $request->validate([
            'images' => 'required|array|min:2|max:3',
            'images.*' => 'image|max:10240', // Pastikan setiap input adalah gambar (maks 10MB)
            'ratio' => 'required|string',
            'count' => 'required|integer|min:1|max:8',
            'prompt' => 'nullable|string'
        ]);

        try {
            $apiKey = env('COMFY_CLOUD_API_KEY');
            $apiUrl = rtrim(env('COMFY_CLOUD_URL', 'https://cloud.comfy.org'), '/');

            $uploadedImages = $request->file('images');

            // 2. Unggah Gambar Utama (Gambar 1 dan Gambar 2 - Wajib)
            $img0 = $this->uploadToComfyCloud($uploadedImages[0], $apiKey, $apiUrl);
            $img1 = $this->uploadToComfyCloud($uploadedImages[1], $apiKey, $apiUrl);

            if (!$img0 || !$img1) {
                return response()->json(['success' => false, 'message' => 'Gagal mengunggah gambar utama ke AI.'], 500);
            }

            // 3. Logika Aman untuk Gambar ke-3 (Opsional)
            $img2 = null;
            if (isset($uploadedImages[2])) {
                // Jika user mengunggah gambar ke-3, gunakan gambar tersebut
                $img2 = $this->uploadToComfyCloud($uploadedImages[2], $apiKey, $apiUrl);
            } else {
                // Jika user HANYA mengunggah 2 gambar, server otomatis upload blank.png ke AI
                $blankPath = storage_path('app/blank.png');

                if (file_exists($blankPath)) {
                    $response = Http::withHeaders(['X-API-Key' => $apiKey])->attach(
                        'image',
                        file_get_contents($blankPath),
                        'blank.png'
                    )->post("{$apiUrl}/api/upload/image", [
                                'type' => 'input',
                                'overwrite' => 'true'
                            ]);

                    if ($response->successful()) {
                        $img2 = $response->json('name');
                    }
                }
            }

            // 4. Load JSON Workflow
            $workflow = json_decode(file_get_contents(storage_path('app/Image-Generation.json')), true);

            // Setup Rasio
            $ratioMap = [
                '1:1' => ['w' => 1024, 'h' => 1024],
                '4:5' => ['w' => 819, 'h' => 1024],
                '9:16' => ['w' => 576, 'h' => 1024],
                '16:9' => ['w' => 1024, 'h' => 576],
                '3:2' => ['w' => 1024, 'h' => 683],
            ];

            $targetWidth = $ratioMap[$request->ratio]['w'] ?? 1024;
            $targetHeight = $ratioMap[$request->ratio]['h'] ?? 1024;

            // 5. Injeksi Data ke Workflow
            $workflow["1"]["inputs"]["image"] = $img0; // Gambar 1 (kiri/base)
            $workflow["2"]["inputs"]["image"] = $img1; // Gambar 2 (kanan/produk)

            if ($img2) {
                $workflow["28"]["inputs"]["image"] = $img2; // Gambar 3 (jika ada) atau blank.png
            } else {
                // Fallback darurat jika blank.png lokal tidak ada/gagal diupload
                $workflow["28"]["inputs"]["image"] = $img0;
            }

            $workflow["27"]["inputs"]["width"] = $targetWidth;
            $workflow["27"]["inputs"]["height"] = $targetHeight;
            $workflow["27"]["inputs"]["batch_size"] = (int) $request->input('count');
            $workflow["7"]["inputs"]["noise_seed"] = rand(10000000000, 99999999999);

            if ($request->filled('prompt')) {
                $workflow["13"]["inputs"]["text"] = $request->input('prompt');
            }

            // 6. Submit ke AI (Asinkron / Antrean)
            $response = Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Content-Type' => 'application/json'
            ])->post("{$apiUrl}/api/prompt", [
                        'prompt' => $workflow
                    ]);

            if ($response->successful()) {
                // Jika ingin disamakan dengan sistem polling seperti di ArtistController
                return response()->json([
                    'success' => true,
                    'status' => 'processing',
                    'prompt_ids' => [$response->json('prompt_id')]
                ], 200);
            }

            return response()->json(['success' => false, 'message' => 'Gagal mendapatkan antrean dari AI.'], 500);

        } catch (\Exception $e) {
            Log::error('GabungController Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function checkStatus($prompt_ids_string)
    {
        try {
            $apiKey = env('COMFY_CLOUD_API_KEY');
            $apiUrl = rtrim(env('COMFY_CLOUD_URL', 'https://cloud.comfy.org'), '/');

            $promptIds = explode(',', $prompt_ids_string);
            $finalImageUrls = [];

            // LOOP 1: Cek Status Semua ID Terlebih Dahulu
            foreach ($promptIds as $promptId) {
                // Ambil data status
                $endpoints = [
                    "{$apiUrl}/api/jobs/{$promptId}",
                    "{$apiUrl}/api/job/{$promptId}",
                    "{$apiUrl}/history/{$promptId}"
                ];

                $jobData = null;
                foreach ($endpoints as $endpoint) {
                    $res = Http::withHeaders(['X-API-Key' => $apiKey])->get($endpoint);
                    if ($res->successful()) {
                        $jobData = $res->json();
                        break;
                    }
                }

                if (!$jobData) {
                    return response()->json(['status' => 'error', 'message' => 'Gagal terhubung ke AI.'], 500);
                }

                $status = $jobData['status'] ?? 'unknown';

                // Jika ada SATU SAJA yang masih jalan, tahan dulu
                if (in_array(strtolower($status), ['pending', 'in_progress', 'running', 'queued'])) {
                    return response()->json(['status' => 'processing', 'message' => 'AI sedang memproses gambar...']);
                }

                // Jika ada yang gagal
                if (in_array(strtolower($status), ['failed', 'cancelled', 'error'])) {
                    return response()->json(['status' => 'error', 'message' => 'Salah satu proses AI gagal.'], 500);
                }

                // Jika completed, ambil output JSON-nya
                $outputs = $jobData[$promptId]['outputs'] ?? $jobData['outputs'] ?? [];

                if (empty($outputs)) {
                    return response()->json(['status' => 'processing', 'message' => 'Menyiapkan file...']);
                }

                // Kumpulkan data gambar yang siap di-download
                foreach ($outputs as $nodeId => $nodeOutputs) {
                    if (isset($nodeOutputs['images'])) {
                        foreach ($nodeOutputs['images'] as $imageInfo) {
                            $finalImageUrls[] = $imageInfo;
                        }
                    }
                }
            }

            // LOOP 2: Jika SEMUA sudah 'completed', download gambarnya ke server lokal
            $downloadedUrls = [];
            foreach ($finalImageUrls as $imageInfo) {
                $filename = $imageInfo['filename'];
                $subfolder = $imageInfo['subfolder'] ?? '';
                $type = $imageInfo['type'] ?? 'output';

                $imageFileResponse = Http::withHeaders(['X-API-Key' => $apiKey])
                    ->get("{$apiUrl}/api/view", ['filename' => $filename, 'subfolder' => $subfolder, 'type' => $type]);

                if ($imageFileResponse->status() === 404) {
                    $imageFileResponse = Http::withHeaders(['X-API-Key' => $apiKey])
                        ->get("{$apiUrl}/view", ['filename' => $filename, 'subfolder' => $subfolder, 'type' => $type]);
                }

                if ($imageFileResponse->successful()) {
                    if (!Storage::disk('public')->exists('results')) {
                        Storage::disk('public')->makeDirectory('results');
                    }

                    $localFilename = 'results/gabung_' . uniqid() . '_' . $filename;
                    Storage::disk('public')->put($localFilename, $imageFileResponse->body());
                    $downloadedUrls[] = url('storage/' . $localFilename);
                }
            }

            if (empty($downloadedUrls)) {
                return response()->json(['status' => 'error', 'message' => 'Gagal mengunduh gambar dari AI.'], 500);
            }

            return response()->json([
                'status' => 'done',
                'images' => $downloadedUrls
            ]);

        } catch (\Exception $e) {
            Log::error('Gabung Status Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    /**
     * Helper untuk upload file ke Comfy Cloud
     */
    private function uploadToComfyCloud($file, $apiKey, $apiUrl)
    {
        $response = Http::withHeaders([
            'X-API-Key' => $apiKey
        ])->attach(
                'image',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post("{$apiUrl}/api/upload/image", [
                    'type' => 'input',
                    'overwrite' => 'true'
                ]);

        if ($response->successful()) {
            return $response->json('name');
        }

        Log::error('ComfyUI Upload Error: ' . $response->body());
        return null;
    }
}