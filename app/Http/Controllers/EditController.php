<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EditController extends Controller
{
    public function generate(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'image' => 'required|image|max:10240', // Maks 10MB
            'prompt' => 'nullable|string',
            'ratio' => 'required|string',
            'count' => 'required|integer|min:1|max:8',
        ]);

        try {
            $apiKey = env('COMFY_CLOUD_API_KEY');
            $apiUrl = rtrim(env('COMFY_CLOUD_URL', 'https://cloud.comfy.org'), '/');

            // 2. Unggah Gambar Utama ke Comfy Cloud
            $mainImageName = $this->uploadToComfyCloud($request->file('image'), $apiKey, $apiUrl);

            // 3. Buat & Unggah Gambar Transparan (1x1) untuk node 8 & 19
            $transparentImageName = $this->uploadTransparentToComfyCloud($apiKey, $apiUrl);

            if (!$mainImageName || !$transparentImageName) {
                return response()->json(['success' => false, 'message' => 'Gagal mengunggah gambar ke AI.'], 500);
            }

            // 4. Tentukan Resolusi dari Rasio
            [$width, $height] = $this->getResolutionFromRatio($request->ratio);

            // 5. Load Workflow (Image-Generation.json dari storage)
            $workflow = json_decode(file_get_contents(storage_path('app/Image-Generation.json')), true);

            // 6. Injeksi Data ke Workflow
            $workflow['1']['inputs']['image'] = $mainImageName; // Image 1 (Base)
            $workflow['2']['inputs']['image'] = $transparentImageName; // Image 2
            $workflow['28']['inputs']['image'] = $transparentImageName; // Image 3 (Logo)

            $workflow['13']['inputs']['text'] = $request->prompt ?? ''; // Positive Prompt
            $workflow['7']['inputs']['noise_seed'] = rand(10000000000, 99999999999); // Random Seed

            $workflow['27']['inputs']['width'] = $width; // Empty Latent Image Width
            $workflow['27']['inputs']['height'] = $height; // Empty Latent Image Height
            $workflow['27']['inputs']['batch_size'] = (int) $request->count; // Generate sekaligus banyak

            // 7. Submit ke AI (Cukup 1 kali Request karena sudah pakai batch_size)
            $response = Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Content-Type' => 'application/json'
            ])->post("{$apiUrl}/api/prompt", [
                        'prompt' => $workflow
                    ]);

            if ($response->successful()) {
                $promptIds = [$response->json('prompt_id')];

                return response()->json([
                    'success' => true,
                    'prompt_ids' => $promptIds
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Gagal mendapatkan antrean dari AI.'], 500);

        } catch (\Exception $e) {
            Log::error('EditController Generate Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server: ' . $e->getMessage()], 500);
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

                // Kumpulkan data gambar dari Node 15 (SaveImageAdvanced)
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

                    $localFilename = 'results/edit_' . uniqid() . '_' . $filename;
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
            Log::error('Edit Status Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    // ==========================================
    // HELPER FUNCTIONS
    // ==========================================

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

    private function uploadTransparentToComfyCloud($apiKey, $apiUrl)
    {
        // Ambil gambar blank dari storage
        $blankPath = storage_path('app/blank.png');

        if (!file_exists($blankPath)) {
            Log::error('blank.png tidak ditemukan di storage/app/');
            return null;
        }

        // Upload ke Comfy Cloud
        $response = Http::withHeaders([
            'X-API-Key' => $apiKey
        ])->attach(
                'image',
                file_get_contents($blankPath),
                'blank.png'
            )->post("{$apiUrl}/api/upload/image", [
                    'type' => 'input',
                    'overwrite' => 'true'
                ]);

        if ($response->successful()) {
            return $response->json('name');
        }

        Log::error('ComfyUI Blank Upload Error: ' . $response->body());
        return null;
    }

    private function getResolutionFromRatio($ratio)
    {
        return match ($ratio) {
            '1:1' => [1024, 1024],
            '16:9' => [1344, 768],
            '9:16' => [768, 1344],
            '4:5' => [896, 1152],
            '3:2' => [1216, 832],
            default => [1024, 1024],
        };
    }

}