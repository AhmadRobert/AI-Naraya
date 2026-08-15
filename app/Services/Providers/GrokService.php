<?php

namespace App\Services\Providers;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GrokService
{
    protected string $apiKey;

    protected string $model;

    protected string $baseUrl = 'https://api.x.ai/v1';

    public function __construct()
    {
        $this->apiKey = config('services.grok.api_key');

        $this->model = config('services.grok.model', 'grok-4.3');

        // FIX PENTING: pengecekan API key SEBELUMNYA ada di constructor
        // (langsung throw kalau kosong). Itu berbahaya sekarang karena
        // AIManager juga bergantung ke AgnesService sebagai backup - kalau
        // GrokService gagal DIBUAT (bukan gagal dipanggil), Laravel akan
        // error duluan sebelum sempat mencoba fallback ke Agnes sama
        // sekali. Pengecekan dipindah ke generate() supaya AIManager tetap
        // bisa dibuat dan mekanisme try/catch fallback benar-benar jalan.
    }

    /**
     * Generate teks (dipakai Caption & Storyboard).
     */
    public function generate(string $prompt): string
    {
        if (! $this->apiKey) {
            throw new Exception('GROK_API_KEY belum diisi di .env.');
        }

        $response = Http::timeout(120)

            ->connectTimeout(15)

            ->retry(2, 1000)

            ->withToken($this->apiKey)

            ->post("{$this->baseUrl}/chat/completions", [

                'model' => $this->model,

                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],

            ]);

        if (! $response->successful()) {

            Log::error('GROK TEXT ERROR', [

                'status' => $response->status(),

                'response' => $response->json(),

            ]);

            throw new Exception(

                data_get(
                    $response->json(),
                    'error.message',
                    'Grok Error.'
                )

            );
        }

        $json = $response->json();

        $text = data_get($json, 'choices.0.message.content', '');

        if ($text === '') {
            Log::error('Grok: tidak menemukan choices.0.message.content', $json);
        }

        return $text;
    }
}