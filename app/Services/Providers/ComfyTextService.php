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
        $this->apiKey = config('services.comfy.api_key');
        $this->baseUrl = rtrim((string) config('services.comfy.url', 'https://cloud.comfy.org'), '/');
    }

    /**
     * Generate teks lewat ComfyUI (dipakai Caption).
     */
    public function generate(string $prompt): string
    {
        if (! $this->apiKey) {
            throw new Exception('COMFY_CLOUD_API_KEY belum diisi di .env.');
        }

        $workflow = $this->prepareTextGenerationWorkflow($prompt);

        if (! $workflow) {
            throw new Exception('Workflow storage/app/Text-Generation.json tidak ditemukan atau tidak valid.');
        }

        $response = Http::timeout(60)
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

            throw new Exception('Gagal mengirim prompt Caption ke ComfyUI.');
        }

        $promptId = $response->json('prompt_id');

        if (! $promptId) {
            throw new Exception('ComfyUI tidak mengembalikan prompt_id untuk Caption.');
        }

        return $this->pollForText($promptId);
    }

    /**
     * Polling ke ComfyUI history sampai teks hasil selesai, atau timeout.
     * Pola sama persis dengan CarouselController::generatePromptFromComfy().
     */
    protected function pollForText(string $promptId, int $maxPolls = 90, int $pollIntervalSeconds = 2): string
{
    for ($i = 0; $i < $maxPolls; $i++) {

        sleep($pollIntervalSeconds);

        $historyRes = Http::timeout(30)
            ->withHeaders([
                'X-API-Key' => $this->apiKey,
            ])
            ->get("{$this->baseUrl}/api/history/{$promptId}");

        if (!$historyRes->successful()) {
            continue;
        }

        $history = $historyRes->json();

        Log::info('COMFY HISTORY', [
    'history' => $history,
]);

        if (!isset($history[$promptId])) {
            continue;
        }

        $outputs = $history[$promptId]['outputs'] ?? [];

        foreach ($outputs as $node) {

            $text = $this->extractText($node);

            if ($text !== null) {
                return $this->cleanText($text);
            }
        }

        // JANGAN BREAK.
        // Biarkan polling lanjut karena node Preview Text
        // kadang baru muncul beberapa detik setelah history dibuat.
    }

    throw new Exception('Timeout/gagal menunggu hasil Caption dari ComfyUI.');
}

    protected function cleanText(string $text): string
    {
        $text = preg_replace('/^```(?:txt|text)?\s*|\s*```$/i', '', trim($text));
        $text = preg_replace('/<think>.*?<\/think>/is', '', $text);

        return trim($text);
    }

    /**
     * Siapkan workflow Text-Generation.json dengan prompt yang sudah diisi.
     * Logika identik dengan CarouselController::prepareTextGenerationWorkflow()
     * milik Candra (mendukung format UI ComfyUI dengan "nodes", maupun
     * format API yang sudah flat).
     */
    protected function prepareTextGenerationWorkflow(string $prompt): ?array
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
                    $inputs['max_tokens'] = $node['widgets_values'][1] ?? 2048;
                    $inputs['stream'] = $node['widgets_values'][2] ?? 'on';
                    $inputs['temperature'] = $node['widgets_values'][3] ?? 0.1;
                    $inputs['top_p'] = $node['widgets_values'][4] ?? 20;
                    $inputs['top_k'] = $node['widgets_values'][5] ?? 0.5;
                    $inputs['repetition_penalty'] = $node['widgets_values'][6] ?? 0.05;
                    $inputs['min_p'] = $node['widgets_values'][7] ?? 1.05;
                    $inputs['seed'] = $node['widgets_values'][8] ?? 12345;
                    $inputs['random'] = $node['widgets_values'][9] ?? 0;
                    $inputs['keep_alive'] = $node['widgets_values'][10] ?? false;
                    $inputs['vram_offload'] = $node['widgets_values'][11] ?? true;
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
        return $data;
    }

    if (!is_array($data)) {
        return null;
    }

    foreach ($data as $value) {

        if (is_string($value)) {
            return $value;
        }

        if (is_array($value)) {

            $result = $this->extractText($value);

            if ($result !== null) {
                return $result;
            }
        }
    }

    return null;
}
}