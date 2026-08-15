<?php

namespace App\Services\Providers;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ComfyTextService
{
    protected ?string $apiKey;

    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.comfy.api_key') ?: env('COMFY_CLOUD_API_KEY');
        $this->baseUrl = rtrim((string) (config('services.comfy.url') ?: env('COMFY_CLOUD_URL', 'https://cloud.comfy.org')), '/');
    }

    /**
     * Generate teks lewat ComfyUI dengan loop pengecekan status sampai selesai.
     */
    public function generate(string $prompt, array $options = []): string
    {
        set_time_limit(0);

        $promptId = $this->submitPrompt($prompt, $options);

        return $this->pollForText($promptId);
    }

    /**
     * Polling (loop) ke ComfyUI history/job sampai teks hasil selesai atau error.
     */
    public function pollForText(string $promptId, int $maxPolls = 180, int $interval = 2): string
    {
        for ($i = 0; $i < $maxPolls; $i++) {
            sleep($interval);

            $res = $this->checkTextStatus($promptId);

            if ($res['status'] === 'done' && ! empty($res['caption'])) {
                return $res['caption'];
            }

            if ($res['status'] === 'failed') {
                throw new Exception($res['message'] ?? 'Proses ComfyUI gagal.');
            }
        }

        throw new Exception('Timeout/gagal menunggu hasil Caption dari ComfyUI.');
    }

    /**
     * Submit prompt ke ComfyUI dan langsung kembalikan prompt_id (tanpa menunggu/polling).
     */
    public function submitPrompt(string $prompt, array $options = []): string
    {
        if (! $this->apiKey) {
            throw new Exception('COMFY_CLOUD_API_KEY belum diisi di .env.');
        }

        $workflow = $this->prepareTextGenerationWorkflow($prompt, $options);

        if (! $workflow) {
            throw new Exception('Workflow storage/app/Text-Generation.json tidak ditemukan atau tidak valid.');
        }

        $response = Http::timeout(30)
            ->withHeaders([
                'X-API-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post("{$this->baseUrl}/api/prompt", [
                'prompt' => $workflow,
            ]);

        if (! $response->successful()) {
            Log::warning('ComfyUI Text Submit Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $errBody = $response->json('error.message') ?? $response->json('message') ?? $response->body();
            throw new Exception('Gagal mengirim prompt Caption ke ComfyUI: ' . $errBody);
        }

        $promptId = $response->json('prompt_id') ?? $response->json('id');

        if (! $promptId) {
            throw new Exception('ComfyUI tidak mengembalikan prompt_id untuk Caption.');
        }

        return $promptId;
    }

    /**
     * Cek status pengerjaan prompt di ComfyUI (dipakai polling dari frontend).
     */
    public function checkTextStatus(string $promptId): array
    {
        $endpoints = [
            "{$this->baseUrl}/api/history/{$promptId}",
            "{$this->baseUrl}/history/{$promptId}",
            "{$this->baseUrl}/api/jobs/{$promptId}",
            "{$this->baseUrl}/api/job/{$promptId}",
        ];

        $jobData = null;
        foreach ($endpoints as $endpoint) {
            try {
                $res = Http::timeout(10)
                    ->withHeaders(['X-API-Key' => $this->apiKey])
                    ->get($endpoint);

                if ($res->successful() && ! empty($res->json())) {
                    $jobData = $res->json();
                    break;
                }
            } catch (\Throwable $e) {
                Log::debug("ComfyUI poll endpoint gagal: {$endpoint}", ['error' => $e->getMessage()]);
            }
        }

        if (! $jobData) {
            return ['status' => 'processing'];
        }

        // Cari outputs dalam berbagai format response ComfyUI Cloud
        $outputs = [];
        if (isset($jobData[$promptId]['outputs'])) {
            $outputs = $jobData[$promptId]['outputs'];
        } elseif (isset($jobData['outputs'])) {
            $outputs = $jobData['outputs'];
        } elseif (is_array($jobData)) {
            foreach ($jobData as $item) {
                if (is_array($item) && isset($item['outputs'])) {
                    $outputs = $item['outputs'];
                    break;
                }
            }
        }

        // Cek status error dari job/history jika ada
        $statusStr = '';
        if (isset($jobData['status'])) {
            $statusStr = is_string($jobData['status']) ? $jobData['status'] : ($jobData['status']['status_str'] ?? '');
        } elseif (isset($jobData[$promptId]['status']['status_str'])) {
            $statusStr = $jobData[$promptId]['status']['status_str'];
        } else {
            foreach ($jobData as $item) {
                if (is_array($item) && isset($item['status'])) {
                    $statusStr = is_string($item['status']) ? $item['status'] : ($item['status']['status_str'] ?? '');
                    break;
                }
            }
        }

        $statusStr = strtolower($statusStr);
        if (in_array($statusStr, ['failed', 'cancelled', 'error'])) {
            $reason = $jobData['status_reason'] ?? $jobData['message'] ?? 'Proses ComfyUI gagal.';

            return [
                'status' => 'failed',
                'message' => 'ComfyUI Error: ' . (is_array($reason) ? json_encode($reason) : $reason),
            ];
        }

        Log::info('COMFY TEXT CHECK STATUS', [
            'prompt_id' => $promptId,
            'outputs' => $outputs,
        ]);

        foreach ($outputs as $node) {
            $text = $this->extractText($node);

            if ($text !== null && trim($text) !== '') {
                return [
                    'status' => 'done',
                    'caption' => $this->cleanText($text),
                ];
            }
        }

        return ['status' => 'processing'];
    }



    protected function cleanText(string $text): string
    {
        $text = preg_replace('/^```(?:txt|text)?\s*|\s*```$/i', '', trim($text));
        $text = preg_replace('/<think>.*?<\/think>/is', '', $text);

        return trim($text);
    }

    /**
     * Siapkan workflow Text-Generation.json dengan prompt dan parameter yang sudah diisi.
     */
    protected function prepareTextGenerationWorkflow(string $prompt, array $options = []): ?array
    {
        $workflowPath = storage_path('app/Text-Generation.json');

        if (! file_exists($workflowPath)) {
            Log::warning("Text-Generation.json workflow tidak ditemukan di {$workflowPath}");

            return null;
        }

        $workflow = json_decode(file_get_contents($workflowPath), true);

        if (! $workflow) {
            Log::warning('Gagal parse Text-Generation.json');

            return null;
        }

        // Generate seed acak untuk setiap request agar ComfyUI selalu memproses ulang & tidak mengembalikan hasil cache
        $seed = !empty($options['seed']) && (int)$options['seed'] > 0 ? (int)$options['seed'] : rand(10000000, 999999999);

        // Format UI ComfyUI (punya "nodes") -> konversi ke format API.
        if (isset($workflow['nodes'])) {

            $apiWorkflow = [];
            $links = [];

            if (isset($workflow['links'])) {
                foreach ($workflow['links'] as $link) {
                    $links[$link[0]] = [
                        'origin_id' => (string) $link[1],
                        'origin_slot' => $link[2],
                    ];
                }
            }

            foreach ($workflow['nodes'] as $node) {

                $id = (string) $node['id'];
                $classType = $node['type'] ?? '';
                $inputs = [];

                if ($classType === 'TextGenerate') {
                    $inputs['prompt'] = $prompt;
                    $inputs['max_length'] = (int) ($options['max_length'] ?? $node['widgets_values'][1] ?? 1024);
                    $inputs['sampling_mode'] = (string) ($options['sampling_mode'] ?? $node['widgets_values'][2] ?? 'on');
                    $inputs['sampling_mode.temperature'] = (float) ($options['temperature'] ?? $node['widgets_values'][3] ?? 0.7);
                    $inputs['sampling_mode.top_p'] = (float) ($options['top_p'] ?? $node['widgets_values'][4] ?? 0.90);
                    $inputs['sampling_mode.top_k'] = (int) ($options['top_k'] ?? $node['widgets_values'][5] ?? 40);
                    $inputs['sampling_mode.repetition_penalty'] = (float) ($options['repetition_penalty'] ?? $node['widgets_values'][6] ?? 1.1);
                    $inputs['sampling_mode.seed'] = $seed;
                    $inputs['sampling_mode.presence_penalty'] = (float) ($options['presence_penalty'] ?? 0.1);
                } elseif ($classType === 'CLIPLoader') {
                    $inputs['clip_name'] = $node['widgets_values'][0] ?? 'qwen3.5_9b_bf16.safetensors';
                    $inputs['type'] = $node['widgets_values'][1] ?? 'ltxv';
                    $inputs['device'] = $node['widgets_values'][2] ?? 'default';
                }

                if (isset($node['inputs'])) {
                    foreach ($node['inputs'] as $input) {
                        $linkId = $input['link'] ?? null;

                        if ($linkId !== null && isset($links[$linkId])) {
                            $inputs[$input['name']] = [
                                $links[$linkId]['origin_id'],
                                $links[$linkId]['origin_slot'],
                            ];
                        }
                    }
                }

                $apiWorkflow[$id] = [
                    'class_type' => $classType,
                    'inputs' => $inputs,
                ];
            }

            return $apiWorkflow;
        }

        // Sudah format API (flat, key = node id).
        $foundNode = false;

        foreach ($workflow as &$node) {
            if (($node['class_type'] ?? null) === 'TextGenerate') {
                $node['inputs']['prompt'] = $prompt;
                $node['inputs']['max_length'] = (int) ($options['max_length'] ?? 1024);
                $node['inputs']['sampling_mode'] = (string) ($options['sampling_mode'] ?? 'on');
                $node['inputs']['sampling_mode.temperature'] = (float) ($options['temperature'] ?? 0.7);
                $node['inputs']['sampling_mode.top_k'] = (int) ($options['top_k'] ?? 40);
                $node['inputs']['sampling_mode.top_p'] = (float) ($options['top_p'] ?? 0.90);
                $node['inputs']['sampling_mode.repetition_penalty'] = (float) ($options['repetition_penalty'] ?? 1.1);
                $node['inputs']['sampling_mode.seed'] = $seed;
                $node['inputs']['sampling_mode.presence_penalty'] = (float) ($options['presence_penalty'] ?? 0.1);
                $foundNode = true;
                break;
            }
        }

        unset($node);

        if (! $foundNode) {
            Log::warning('Node TextGenerate tidak ditemukan di Text-Generation.json (format API)');
        }

        return $workflow;
    }

    protected function extractText($data): ?string
    {
        if (is_string($data)) {
            $str = trim($data);
            if (preg_match('/\.(safetensors|sft|ckpt|bin|pt|png|jpg|jpeg|webp)$/i', $str)) {
                return null;
            }

            return $str !== '' ? $str : null;
        }

        if (! is_array($data)) {
            return null;
        }

        foreach (['text', 'string', 'output', 'result'] as $key) {
            if (isset($data[$key])) {
                $res = $this->extractText($data[$key]);
                if ($res !== null) {
                    return $res;
                }
            }
        }

        foreach ($data as $value) {
            $res = $this->extractText($value);
            if ($res !== null) {
                return $res;
            }
        }

        return null;
    }
}