<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArtistController extends Controller
{
    public function index()
    {
        return view('Artist');
    }

    // 1. TUGAS: HANYA MENGIRIM DATA KE AI LALU LANGSUNG KEMBALIKAN PROMPT ID
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'product_image' => 'required|image|max:10240',
            'model_image' => 'required|image|max:10240',
            'icon_image' => 'nullable|image|max:10240', // Input ke-3 (Opsional)
            'prompt' => 'required|string|max:1000',
            'ratio' => 'required|string|in:1:1,4:5,9:16,16:9,3:2',
            'count' => 'required|integer|in:1,2,4,8',
        ]);

        try {
            $apiKey = env('COMFY_CLOUD_API_KEY');
            $apiUrl = rtrim(env('COMFY_CLOUD_URL', 'https://cloud.comfy.org'), '/');

            // 1. Unggah file utama ke Comfy
            $uploadedProduct = $this->uploadToComfyCloud($request->file('product_image'), $apiKey, $apiUrl);
            $uploadedModel = $this->uploadToComfyCloud($request->file('model_image'), $apiKey, $apiUrl);

            if (!$uploadedProduct || !$uploadedModel) {
                return response()->json(['status' => 'error', 'message' => 'Gagal mengunggah gambar ke AI.'], 500);
            }

            // 2. Logika Cerdas untuk Gambar ke-3 (Icon/Logo)
            $uploadedIcon = null;

            if ($request->hasFile('icon_image')) {
                // Jika user upload icon, gunakan itu
                $uploadedIcon = $this->uploadToComfyCloud($request->file('icon_image'), $apiKey, $apiUrl);
            } else {
                // Jika tidak ada icon, upload blank.png dari server lokal Laravel ke Comfy Cloud
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
                        $uploadedIcon = $response->json('name');
                    }
                }
            }

            // Siapkan Workflow
            $dimensions = $this->getDimensionsFromRatio($validated['ratio']);
            $jsonPath = storage_path('app/Product-Artist.json');
            $workflow = json_decode(file_get_contents($jsonPath), true);

            // Injeksi Data ke Workflow
            $workflow["7"]["inputs"]["image"] = $uploadedModel;
            $workflow["8"]["inputs"]["image"] = $uploadedProduct;

            // 3. Masukkan ke Node 19
            if ($uploadedIcon) {
                $workflow["19"]["inputs"]["image"] = $uploadedIcon;
            } else {
                // Fallback darurat (Mencegah error mutlak jika blank.png lokal tidak terbaca/gagal upload)
                // Kita "pinjam" nama file model_image yang sudah pasti berhasil diupload di atas.
                // AI akan mengabaikannya jika prompt tidak menyuruh memunculkan gambar ke-3.
                $workflow["19"]["inputs"]["image"] = $uploadedModel;
            }

            $workflow["16"]["inputs"]["width"] = $dimensions['width'];
            $workflow["16"]["inputs"]["height"] = $dimensions['height'];
            $workflow["16"]["inputs"]["batch_size"] = (int) $validated['count'];
            $workflow["11"]["inputs"]["prompt"] = $validated['prompt'];
            $workflow["13"]["inputs"]["seed"] = rand(10000000000000, 99999999999999);

            // Submit Workflow
            $response = Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Content-Type' => 'application/json'
            ])->post("{$apiUrl}/api/prompt", [
                        'prompt' => $workflow
                    ]);

            if (!$response->successful()) {
                return response()->json(['status' => 'error', 'message' => 'Gagal submit ke AI.'], 500);
            }

            $promptId = $response->json('prompt_id');

            return response()->json([
                'status' => 'processing',
                'message' => 'Proses AI dimulai, sedang masuk antrean.',
                'prompt_id' => $promptId
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 2. TUGAS: DIPANGGIL BERULANG-ULANG OLEH JAVASCRIPT UNTUK CEK STATUS
    public function checkStatus($promptId)
    {
        try {
            $apiKey = env('COMFY_CLOUD_API_KEY');
            $apiUrl = rtrim(env('COMFY_CLOUD_URL', 'https://cloud.comfy.org'), '/');

            // Kita satukan! Ambil data job menggunakan endpoint yang sudah terbukti membalas dengan benar
            $endpoints = [
                "{$apiUrl}/api/jobs/{$promptId}",
                "{$apiUrl}/api/job/{$promptId}",
                "{$apiUrl}/history/{$promptId}"
            ];

            $jobData = null;
            foreach ($endpoints as $endpoint) {
                $jobRes = Http::withHeaders(['X-API-Key' => $apiKey])->get($endpoint);
                if ($jobRes->successful()) {
                    $jobData = $jobRes->json();
                    break;
                }
            }

            if ($jobData === null) {
                return response()->json(['status' => 'error', 'message' => 'Gagal terhubung ke Comfy Cloud.'], 500);
            }

            // Ambil status dari JSON (seperti yang Anda kirimkan tadi)
            $status = $jobData['status'] ?? 'unknown';

            // JIKA STATUS MASIH PENDING / PROSES -> Suruh web nge-loop lagi
            if (in_array(strtolower($status), ['pending', 'in_progress', 'running', 'queued'])) {
                return response()->json([
                    'status' => 'processing',
                    'message' => 'AI sedang memproses gambar. Harap tunggu...'
                ]);
            }

            // JIKA STATUS GAGAL -> Hentikan loop dan tampilkan pesan gagal
            if (in_array(strtolower($status), ['failed', 'cancelled', 'error'])) {
                return response()->json(['status' => 'error', 'message' => 'Proses AI gagal atau dibatalkan.'], 500);
            }

            // ====================================================================
            // JIKA KODE SAMPAI DI SINI, ARTINYA STATUS SUDAH "COMPLETED" / SELESAI
            // ====================================================================

            $outputs = [];
            if (isset($jobData[$promptId]['outputs'])) {
                $outputs = $jobData[$promptId]['outputs'];
            } elseif (isset($jobData['outputs'])) {
                $outputs = $jobData['outputs'];
            }

            // Jika status selesai tapi belum ada output (delay dari server), tunggu sebentar
            if (empty($outputs)) {
                return response()->json([
                    'status' => 'processing',
                    'message' => 'Menyiapkan file gambar final...'
                ]);
            }

            // DOWNLOAD GAMBAR KETIKA OUTPUT SUDAH ADA
            $finalImageUrls = [];
            foreach ($outputs as $nodeId => $nodeOutputs) {
                if (isset($nodeOutputs['images'])) {
                    foreach ($nodeOutputs['images'] as $imageInfo) {
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
                            // Pastikan folder results ada
                            if (!Storage::disk('public')->exists('results'))
                                Storage::disk('public')->makeDirectory('results');

                            $localFilename = 'results/' . uniqid() . '_' . $filename;
                            Storage::disk('public')->put($localFilename, $imageFileResponse->body());
                            $finalImageUrls[] = url('storage/' . $localFilename);
                        }
                    }
                }
            }

            if (empty($finalImageUrls)) {
                return response()->json(['status' => 'error', 'message' => 'Gambar ditemukan di AI, tetapi server web gagal mengunduhnya.'], 500);
            }

            // BERHASIL! KIRIM URL GAMBAR KE FRONTEND UNTUK DITAMPILKAN
            return response()->json([
                'status' => 'success',
                'message' => 'Selesai',
                'images' => $finalImageUrls
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function uploadToComfyCloud($file, $apiKey, $apiUrl)
    {
        $response = Http::withHeaders(['X-API-Key' => $apiKey])->attach(
            'image',
            file_get_contents($file->getRealPath()),
            $file->getClientOriginalName()
        )->post("{$apiUrl}/api/upload/image", ['type' => 'input', 'overwrite' => 'true']);

        return $response->successful() ? $response->json('name') : null;
    }

    private function getDimensionsFromRatio(string $ratio): array
    {
        return match ($ratio) {
            '1:1' => ['width' => 1024, 'height' => 1024],
            '4:5' => ['width' => 819, 'height' => 1024],
            '9:16' => ['width' => 576, 'height' => 1024],
            '16:9' => ['width' => 1024, 'height' => 576],
            '3:2' => ['width' => 1024, 'height' => 683],
            default => ['width' => 1024, 'height' => 1024],
        };
    }
}