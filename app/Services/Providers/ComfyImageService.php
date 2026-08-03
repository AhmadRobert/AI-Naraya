<?php

namespace App\Services\Providers;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * ============================================================
 * BELUM BISA DIPAKAI - menunggu file workflow dari kamu.
 * ============================================================
 *
 * ComfyUI itu BERBASIS WORKFLOW GRAPH (node-node yang saling sambung),
 * beda total dari provider lain yang sudah kita pakai (Grok/Agnes cukup
 * kirim {model, prompt}). Endpoint cloud-nya (cloud.comfy.org/api/prompt)
 * menerima body {"prompt": <ISI WORKFLOW GRAPH JSON>}, bukan
 * {"prompt": "teks instruksi"} seperti provider lain.
 *
 * Supaya kelas ini beneran bisa jalan, saya butuh 2 hal konkret dari kamu:
 *
 * 1. File workflow yang sudah kamu susun & export dalam format API dari
 *    canvas ComfyUI (menu Export > "API Format", BUKAN export biasa),
 *    untuk kebutuhan:
 *    - Edit Foto (image edit / img2img, 1 gambar input)
 *    - Gabungkan Foto (multi-image, 2+ gambar input - workflow-nya
 *      biasanya perlu beberapa "Load Image" node)
 *
 * 2. ID node di masing-masing file itu yang menampung:
 *    - teks prompt (biasanya node "CLIPTextEncode" atau sejenisnya)
 *    - gambar input (biasanya node "LoadImage")
 *    - output akhir (biasanya node "SaveImage")
 *
 * Begitu file JSON itu ada, taruh di storage/app/comfy/edit-foto.json
 * dan storage/app/comfy/gabung-foto.json (atau kirim isinya ke saya),
 * saya sesuaikan method buildWorkflow() di bawah supaya mengisi node
 * yang benar sebelum submit ke POST /api/prompt.
 *
 * Sampai saat itu, method generate() di bawah akan SELALU melempar
 * error yang jelas (bukan crash membingungkan), supaya jelas kelihatan
 * di UI kalau memang belum dikonfigurasi - bukan dianggap bug.
 */
class ComfyImageService
{
    protected string $apiKey;

    protected string $baseUrl = 'https://cloud.comfy.org/api';

    public function __construct()
    {
        $this->apiKey = config('services.comfy.api_key');
    }

    public function generate(array $imageFiles, string $prompt, string $ratio, int $count = 1): array
    {
        if (! $this->apiKey) {
            throw new Exception('COMFY_API_KEY belum diisi di .env.');
        }

        throw new Exception(
            'ComfyUI belum bisa dipakai: workflow_api.json untuk fitur ini belum ' .
            'dikonfigurasi. Lihat catatan lengkap di app/Services/Providers/ComfyImageService.php.'
        );

        // ----------------------------------------------------------------
        // Kerangka alur yang akan dipakai setelah workflow JSON tersedia
        // (masih dinonaktifkan lewat throw di atas):
        //
        // $workflow = $this->buildWorkflow($imageFiles, $prompt, $ratio);
        // $promptId = $this->submitWorkflow($workflow);
        // $result = $this->pollUntilDone($promptId);
        // return $this->extractImages($result);
        // ----------------------------------------------------------------
    }

    /**
     * TODO setelah workflow JSON ada: muat file workflow_api.json yang
     * sesuai (edit vs gabung, berdasarkan jumlah $imageFiles), lalu isi
     * node prompt teks & node gambar dengan data yang sebenarnya sebelum
     * dikirim.
     */
    protected function buildWorkflow(array $imageFiles, string $prompt, string $ratio): array
    {
        throw new Exception('buildWorkflow() belum diimplementasikan - butuh workflow_api.json.');
    }

    protected function submitWorkflow(array $workflow): string
    {
        $response = Http::timeout(60)

            ->withHeaders(['X-API-Key' => $this->apiKey])

            ->asJson()

            ->post("{$this->baseUrl}/prompt", [
                'prompt' => $workflow,
            ]);

        $json = $this->handleResponse($response);

        $promptId = data_get($json, 'prompt_id');

        if (! $promptId) {
            throw new Exception('Gagal submit workflow ke ComfyUI Cloud.');
        }

        return $promptId;
    }

    protected function pollUntilDone(string $promptId): array
    {
        $maxAttempts = 36; // 36 x 5 detik = 3 menit
        $attempt = 0;

        while ($attempt < $maxAttempts) {

            sleep(5);
            $attempt++;

            $response = Http::timeout(30)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->get("{$this->baseUrl}/history/{$promptId}");

            $json = $this->handleResponse($response);

            $entry = data_get($json, $promptId);

            if ($entry) {
                return $entry;
            }
        }

        throw new Exception('ComfyUI timeout, silakan coba lagi.');
    }

    /**
     * TODO: bentuk output ComfyUI itu per-node ("outputs": {"<node_id>":
     * {"images": [...]}}) - perlu tahu node ID SaveImage dari workflow
     * JSON yang sebenarnya untuk tahu di mana harus ambil hasilnya, dan
     * file gambar diambil lewat GET /api/view (bukan base64 langsung).
     */
    protected function extractImages(array $historyEntry): array
    {
        throw new Exception('extractImages() belum diimplementasikan - butuh workflow_api.json.');
    }

    protected function handleResponse(Response $response): array
    {
        $json = $response->json() ?? [];

        if ($response->successful()) {
            return $json;
        }

        Log::error('ComfyUI Error', [
            'status' => $response->status(),
            'body' => $json,
        ]);

        $message = data_get($json, 'error.message')
            ?? data_get($json, 'message')
            ?? 'ComfyUI Error.';

        throw new Exception($message);
    }
}