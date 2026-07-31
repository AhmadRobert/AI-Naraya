<?php

namespace App\Http\Controllers;

use App\Services\AiNarayaGeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class CarouselController extends Controller
{
    protected $geminiService;

    public function __construct(?AiNarayaGeminiService $geminiService = null)
    {
        $this->geminiService = $geminiService ?? new AiNarayaGeminiService();
    }

    /**
     * Proses & Analisis PDF Storyboard sebelum tombol Generate ditekan
     */
    public function processStoryboard(Request $request)
    {
        $request->validate([
            'storyboard_pdf' => 'required|mimes:pdf|max:30720',
        ], [
            'storyboard_pdf.required' => 'File PDF Storyboard wajib diunggah.',
            'storyboard_pdf.mimes' => 'File harus berformat PDF.',
            'storyboard_pdf.max' => 'Ukuran file PDF maksimal 30 MB.',
        ]);

        // Mencegah PHP timeout saat menunggu ComfyUI
        set_time_limit(300);

        try {
            $pdfFile = $request->file('storyboard_pdf');
            $originalName = $pdfFile->getClientOriginalName();

            // Ekstrak raw teks dari PDF
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdfFile->getPathname());

            $rawText = $pdf->getText();
            $pageCount = count($pdf->getPages());

            // Batasi teks agar tidak melebihi kapasitas token Gemini
            $pdfText = trim(substr($rawText, 0, 15000));

            // 1. Kirim raw teks ke ComfyUI (Text-Generation.json) untuk dianalisis
            if (!empty($pdfText) && env('COMFY_CLOUD_API_KEY')) {
                $promptId = $this->submitPdfToComfy($pdfText, $originalName);

                if ($promptId) {
                    return response()->json([
                        'success' => true,
                        'status' => 'processing',
                        'prompt_id' => $promptId,
                        'filename' => $originalName,
                        'page_count' => $pageCount,
                        'raw_text' => $pdfText,
                        'message' => 'Storyboard dikirim ke AI. Sedang menunggu hasil ekstraksi...'
                    ]);
                }
            }

            // 2. Jika gagal disubmit
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim teks storyboard ke AI. Pastikan format PDF jelas dan ComfyUI API merespons.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Carousel Process Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses file PDF Storyboard: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Kirim raw teks PDF ke ComfyUI (hanya submit) dan kembalikan prompt_id.
     */
    private function submitPdfToComfy(string $pdfText, string $filename): ?string
    {
        try {
            $comfyApiKey = env('COMFY_CLOUD_API_KEY');
            $comfyApiUrl = rtrim(env('COMFY_CLOUD_URL', 'https://cloud.comfy.org'), '/');

            if (!$comfyApiKey)
                return null;

            $systemInstruction = "You are a data parser API. Convert the provided messy OCR text from a storyboard table into a clean structure.

RULES:
1. Extract 4 fields per row: \"reel\" (integer), \"visual\" (string), \"copywriting\" (string), and \"purpose\" (string).
2. TRANSLATE the \"visual\" text to English.
3. KEEP the \"copywriting\" and \"purpose\" in exact original Indonesian. If purpose is missing, use \"-\".
4. REMOVE ALL NEWLINES (\\n): Merge broken sentences into a single, clean line.
5. STRICT OUTPUT FORMAT: Output ONLY the requested format below. NO markdown, NO JSON, NO code blocks, and NO conversational text.

EXPECTED OUTPUT FORMAT (Use ||| as delimiter):
1|||English translation of visual instruction|||Original Indonesian text|||Original Indonesian purpose text
2|||Another visual|||Another text|||Another purpose";

            $workflowPath = storage_path('app/Text-Generation.json');
            $fullPromptText = $systemInstruction . "\n\nBerikut adalah raw teks hasil ekstraksi dari PDF storyboard:\n\n{$pdfText}";
            $workflow = $this->prepareTextGenerationWorkflow($workflowPath, $fullPromptText);

            if (!$workflow) {
                return null;
            }

            // 1. Submit prompt ke ComfyUI
            $response = Http::timeout(60)->withHeaders([
                'X-API-Key' => $comfyApiKey,
                'Content-Type' => 'application/json'
            ])->post("{$comfyApiUrl}/api/prompt", [
                        'prompt' => $workflow
                    ]);

            if (!$response->successful()) {
                Log::warning('ComfyUI API Submit Error', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return null;
            }

            return $response->json('prompt_id');

        } catch (\Exception $e) {
            Log::warning('ComfyUI Storyboard Submit Error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Polling status ke ComfyUI History
     */
    public function checkProcessStatus($promptId)
    {
        try {
            $comfyApiKey = env('COMFY_CLOUD_API_KEY');
            $comfyApiUrl = rtrim(env('COMFY_CLOUD_URL', 'https://cloud.comfy.org'), '/');

            if (!$comfyApiKey) {
                return response()->json(['status' => 'error', 'message' => 'API Key tidak ditemukan.'], 500);
            }

            $endpoints = [
                "{$comfyApiUrl}/api/jobs/{$promptId}",
                "{$comfyApiUrl}/api/job/{$promptId}",
                "{$comfyApiUrl}/history/{$promptId}",
                "{$comfyApiUrl}/api/history/{$promptId}"
            ];

            $jobData = null;
            foreach ($endpoints as $endpoint) {
                $res = Http::timeout(30)->withHeaders([
                    'X-API-Key' => $comfyApiKey,
                ])->get($endpoint);

                if ($res->successful()) {
                    $jobData = $res->json();
                    break;
                }
            }

            if (!$jobData) {
                // Asumsikan masih processing jika endpoint tidak merespons (misal queue) atau coba asumsikan processing
                return response()->json(['status' => 'processing', 'message' => 'AI sedang memproses teks...']);
            }

            // Cek status eksplisit jika menggunakan API job (Comfy Cloud)
            if (isset($jobData['status'])) {
                $status = strtolower($jobData['status']);
                if (in_array($status, ['pending', 'in_progress', 'running', 'queued'])) {
                    return response()->json(['status' => 'processing', 'message' => 'AI sedang memproses teks...']);
                }
                if (in_array($status, ['failed', 'cancelled', 'error'])) {
                    return response()->json(['status' => 'error', 'message' => 'Proses AI gagal.']);
                }
            }

            // Cari outputs berdasarkan format (local history vs cloud job)
            // Jika format lokal: {"prompt_id": {"outputs": {...}}}
            // Jika format cloud: {"outputs": {...}}
            $outputs = [];
            if (isset($jobData[$promptId]) && isset($jobData[$promptId]['outputs'])) {
                $outputs = $jobData[$promptId]['outputs'];
            } elseif (isset($jobData['outputs'])) {
                $outputs = $jobData['outputs'];
            } else {
                // Jika tidak ada outputs tapi job data ada (misal masih ada di history tapi empty outputs),
                // atau job selesai tapi node tidak menulis ke output (karena tidak ada OUTPUT_NODE).
                // Kita akan berasumsi job selesai tapi output kosong.
            }

            // Cek apakah job sudah selesai tapi outputnya belum ada
            if (empty($outputs)) {
                // Cek apakah format history lokal menunjukkan ini ada
                if (isset($jobData[$promptId])) {
                    return response()->json(['status' => 'error', 'message' => 'Proses AI selesai, tetapi tidak ada output teks. Pastikan ada output node yang berjalan.']);
                }
                return response()->json(['status' => 'processing', 'message' => 'AI sedang memproses teks...']);
            }

            $textResult = '';

            foreach ($outputs as $nodeId => $output) {
                if (isset($output['text']) && is_array($output['text']) && !empty($output['text'][0])) {
                    $textResult = $output['text'][0];
                    break;
                }
                if (isset($output['generated_text']) && is_array($output['generated_text']) && !empty($output['generated_text'][0])) {
                    $textResult = $output['generated_text'][0];
                    break;
                }
                if (isset($output['string']) && is_array($output['string']) && !empty($output['string'][0])) {
                    $textResult = $output['string'][0];
                    break;
                }
                if (is_array($output)) {
                    foreach ($output as $val) {
                        if (is_array($val) && isset($val[0]) && is_string($val[0])) {
                            $textResult = $val[0];
                            break 2;
                        }
                    }
                }
            }

            if (empty($textResult)) {
                return response()->json(['status' => 'error', 'message' => 'Hasil teks tidak ditemukan di output AI.']);
            }

            // Bersihkan teks result (hilangkan markdown atau tag think)
            $cleanText = preg_replace('/^```(?:txt|text)?\s*|\s*```$/i', '', trim($textResult));
            $cleanText = preg_replace('/<think>.*?<\/think>/is', '', $cleanText);
            $cleanText = trim($cleanText);

            $formatted = [];
            $barisReel = explode("\n", $cleanText);

            foreach ($barisReel as $baris) {
                if (empty(trim($baris))) continue;

                $kolom = explode("|||", $baris);

                if (count($kolom) >= 3) {
                    $rNum = (int) trim($kolom[0]);
                    if ($rNum <= 0) {
                        $rNum = count($formatted) + 1;
                    }

                    $visual = trim($kolom[1]);
                    $copywriting = trim($kolom[2]);
                    $purpose = isset($kolom[3]) ? trim($kolom[3]) : '-';

                    $formatted[] = [
                        'reel' => $rNum,
                        'title' => "Reel {$rNum}",
                        'visual' => $visual ?: "Scene for Reel {$rNum}",
                        'copywriting' => $copywriting ?: '-',
                        'tujuan' => $purpose ?: '-',
                        'prompt' => $visual ?: "Cinematic scene for reel {$rNum}",
                    ];
                }
            }

            if (!empty($formatted)) {
                usort($formatted, fn($a, $b) => ($a['reel'] ?? 0) - ($b['reel'] ?? 0));
                foreach ($formatted as $idx => &$reel) {
                    $reel['reel'] = $idx + 1;
                    if (empty($reel['title'])) {
                        $reel['title'] = 'Reel ' . ($idx + 1);
                    }
                }
                unset($reel);

                return response()->json([
                    'status' => 'done',
                    'reels' => $formatted,
                    'total_reels' => count($formatted)
                ]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'JSON AI tidak valid atau kosong.']);
            }

        } catch (\Exception $e) {
            Log::error('Check Process Status Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat mengecek status.'], 500);
        }
    }



    private function fallbackExtractReels($pdfText, $filename, $pageCount)
    {
        // 1. Coba ekstrak dari tabel berbasis pipe delimiter (format: No | Visual | Copywriting | Tujuan)
        $tableReels = $this->extractFromTableRows($pdfText);
        if (!empty($tableReels)) {
            return $tableReels;
        }

        // 2. Coba ekstrak dari format section headers (Reel 1, Reel 2, dst)
        $sectionReels = $this->extractFromSectionHeaders($pdfText);
        if (!empty($sectionReels)) {
            return $sectionReels;
        }

        // 3. Fallback: buat reel berdasarkan jumlah yang terdeteksi dari filename/teks
        $count = 1;
        if (preg_match('/\b(\d{1,2})\s*(?:ide|reels?|konten|carousel)\b/i', $filename, $m)) {
            $count = (int) $m[1];
        } else if (preg_match_all('/\b(?:reel|scene|ide)\s*[-:#.]?\s*(\d{1,2})\b/i', $pdfText, $matches)) {
            $nums = array_map('intval', $matches[1]);
            $count = max(max($nums), 1);
        } else if ($pageCount > 1) {
            $count = min($pageCount, 20);
        }

        $reels = [];
        for ($i = 1; $i <= $count; $i++) {
            $reels[] = [
                'reel' => $i,
                'title' => "Reel {$i}",
                'visual' => "Adegan visual untuk Reel {$i}",
                'copywriting' => "-",
                'tujuan' => "-",
                'prompt' => "Cinematic shot, highly detailed, photorealistic scene for reel {$i}",
            ];
        }

        return $reels;
    }

    /**
     * Ekstrak data reel dari tabel PDF yang menggunakan pipe (|) sebagai pemisah kolom.
     * Format tabel biasanya: No | Visual/Instruksi Gambar | Copywriting/Teks | Tujuan | ...
     * Baris pertama dianggap header, baris selanjutnya adalah data.
     */
    private function extractFromTableRows($pdfText)
    {
        // Normalisasi line breaks
        $text = str_replace(["\r\n", "\r"], "\n", $pdfText);

        // Ambil semua baris yang mengandung pipe (|)
        $allLines = explode("\n", $text);
        $pipeLines = [];
        foreach ($allLines as $line) {
            $trimmed = trim($line);
            if (!empty($trimmed) && strpos($trimmed, '|') !== false) {
                $pipeLines[] = $trimmed;
            }
        }

        if (count($pipeLines) < 2) {
            return []; // Tidak cukup baris untuk header + data
        }

        // Baris pertama = Header, deteksi posisi kolom
        $headerLine = array_shift($pipeLines);
        $headerCols = array_map('trim', explode('|', $headerLine));

        // Deteksi index kolom berdasarkan nama header
        $colMap = [
            'visual' => -1,
            'copywriting' => -1,
            'tujuan' => -1,
        ];

        foreach ($headerCols as $idx => $colName) {
            $lower = strtolower($colName);
            if (preg_match('/visual|gambar|adegan|instruksi/i', $lower)) {
                $colMap['visual'] = $idx;
            } elseif (preg_match('/copy|teks|text|headline|caption/i', $lower)) {
                $colMap['copywriting'] = $idx;
            } elseif (preg_match('/tujuan|goal|purpose|catatan/i', $lower)) {
                $colMap['tujuan'] = $idx;
            }
        }

        // Jika tidak ada kolom visual terdeteksi, coba assign berdasarkan posisi
        // Asumsi format: No | Visual | Copywriting | Tujuan
        if ($colMap['visual'] === -1 && count($headerCols) >= 2) {
            $colMap['visual'] = 1; // Kolom kedua setelah No
        }
        if ($colMap['copywriting'] === -1 && count($headerCols) >= 3) {
            $colMap['copywriting'] = 2;
        }
        if ($colMap['tujuan'] === -1 && count($headerCols) >= 4) {
            $colMap['tujuan'] = 3;
        }

        // Parse setiap baris data
        $reels = [];
        $reelNum = 1;

        foreach ($pipeLines as $dataLine) {
            $cols = array_map('trim', explode('|', $dataLine));

            // Skip baris yang hanya berisi separator (----) atau kosong
            $nonEmpty = array_filter($cols, function ($c) {
                return !empty($c) && !preg_match('/^[-=]+$/', $c);
            });
            if (empty($nonEmpty)) {
                continue;
            }

            $visual = ($colMap['visual'] >= 0 && isset($cols[$colMap['visual']]))
                ? $cols[$colMap['visual']] : '-';
            $copywriting = ($colMap['copywriting'] >= 0 && isset($cols[$colMap['copywriting']]))
                ? $cols[$colMap['copywriting']] : '-';
            $tujuan = ($colMap['tujuan'] >= 0 && isset($cols[$colMap['tujuan']]))
                ? $cols[$colMap['tujuan']] : '-';

            // Bersihkan visual dari noise
            $visual = preg_replace('/\b(?:SFX|VO|Musik|Audio|Durasi)\s*[:]\s*/i', '', $visual);
            $visual = trim($visual);

            if (empty($visual) || $visual === '-') {
                $visual = "Adegan visual untuk Reel {$reelNum}";
            }

            // Generate prompt dari visual
            $cleanVisual = preg_replace('/[^\w\s,]/', '', $visual);
            $promptStr = "Cinematic shot, highly detailed, " . trim(substr($cleanVisual, 0, 150)) . ", photorealistic";

            $reels[] = [
                'reel' => $reelNum,
                'title' => "Reel {$reelNum}",
                'visual' => $visual,
                'copywriting' => !empty($copywriting) ? $copywriting : '-',
                'tujuan' => !empty($tujuan) ? $tujuan : '-',
                'prompt' => $promptStr,
            ];

            $reelNum++;
        }

        return $reels;
    }

    /**
     * Ekstrak data reel dari format section headers (Reel 1, Scene 1, Ide 1, dll)
     */
    private function extractFromSectionHeaders($pdfText)
    {
        $pattern = '/(?=(?:^|\n)\s*(?:reel|scene|ide|slide|konten)\s*#?\s*\d{1,2}\b)/i';
        $parts = preg_split($pattern, $pdfText, -1, PREG_SPLIT_NO_EMPTY);

        if (count($parts) <= 1) {
            return [];
        }

        $reels = [];
        $seqNum = 1;
        foreach ($parts as $part) {
            $trimmed = trim($part);
            if (empty($trimmed))
                continue;

            // Parse tiap section: cari visual, copywriting, tujuan dari teks blok
            $lines = array_values(array_filter(array_map('trim', explode("\n", $trimmed))));
            $visual = '';
            $copywriting = '-';
            $tujuan = '-';

            foreach ($lines as $line) {
                // Skip header line (Reel 1, Scene 1, dll)
                if (preg_match('/^(?:reel|scene|ide|slide|konten)\s*#?\s*\d{1,2}$/i', $line)) {
                    continue;
                }
                if (preg_match('/^(?:visual|gambar|adegan|instruksi)\s*[:|-]?\s*(.*)$/i', $line, $m)) {
                    $visual = trim($m[1]);
                } elseif (preg_match('/^(?:copywriting|teks|text|headline|caption)\s*[:|-]?\s*(.*)$/i', $line, $m)) {
                    $copywriting = trim($m[1]);
                } elseif (preg_match('/^(?:tujuan|goal|purpose|catatan)\s*[:|-]?\s*(.*)$/i', $line, $m)) {
                    $tujuan = trim($m[1]);
                } elseif (empty($visual)) {
                    $visual = $line; // Baris pertama non-header jadi visual
                }
            }

            if (empty($visual)) {
                $visual = "Adegan visual untuk Reel {$seqNum}";
            }

            $cleanVisual = preg_replace('/[^\w\s,]/', '', $visual);
            $promptStr = "Cinematic shot, highly detailed, " . trim(substr($cleanVisual, 0, 150)) . ", photorealistic";

            $reels[] = [
                'reel' => $seqNum,
                'title' => "Reel {$seqNum}",
                'visual' => $visual,
                'copywriting' => $copywriting,
                'tujuan' => $tujuan,
                'prompt' => $promptStr,
            ];
            $seqNum++;
        }

        return $reels;
    }

    public function generate(Request $request)
    {
        $request->validate([
            'storyboard_pdf' => 'required|mimes:pdf|max:30720',
            'reel_number' => 'required|integer|min:1',
            'aspect_ratio' => 'required|string',
            'extra_prompt' => 'nullable|string',
            'extracted_prompt' => 'nullable|string',
            'reference_photos' => 'nullable|array|max:5',
            'reference_photos.*' => 'image|max:10240',
        ]);

        try {
            $comfyApiKey = env('COMFY_CLOUD_API_KEY');
            $comfyApiUrl = rtrim(env('COMFY_CLOUD_URL', 'https://cloud.comfy.org'), '/');

            $pdfFile = $request->file('storyboard_pdf');
            $targetReel = $request->input('reel_number');
            $scenePrompt = $request->input('extracted_prompt');

            if (empty($scenePrompt)) {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($pdfFile->getPathname());
                $fullPdfText = substr($pdf->getText(), 0, 5000);
                $scenePrompt = $this->generatePromptFromComfy($fullPdfText, $targetReel);
            }

            $extraPrompt = $request->input('extra_prompt', '');
            $finalPrompt = trim($scenePrompt . ($extraPrompt ? ', ' . $extraPrompt : ''));

            // 1. Kirim langsung ke Gemini AI untuk render visual adegan
            if (env('GEMINI_API_KEY')) {
                try {
                    $parts = [
                        $this->geminiService->filePart($pdfFile),
                    ];

                    foreach ($request->file('reference_photos', []) as $photo) {
                        $parts[] = $this->geminiService->filePart($photo);
                    }

                    $parts[] = [
                        'text' => "Visual render untuk Reel {$targetReel}. Prompt adegan: {$finalPrompt}. Buat adegan cinematic, realistis, dan detail tinggi dengan rasio {$request->aspect_ratio}.",
                    ];

                    $images = $this->geminiService->makeImages($parts, 1, $request->aspect_ratio, 'generations/carousel', "Reel {$targetReel}");
                    $urls = array_column($images, 'url');

                    if (!empty($urls)) {
                        return response()->json([
                            'success' => true,
                            'status' => 'done',
                            'prompt_ids' => ['done_direct'],
                            'images' => $urls
                        ]);
                    }
                } catch (\Exception $geminiErr) {
                    Log::warning('Gemini Generate Direct Error, mencoba ComfyUI: ' . $geminiErr->getMessage());
                }
            }

            // 2. Fallback: Submit ke ComfyUI Cloud jika Gemini gagal / API key ComfyUI ada
            if (!empty($comfyApiKey)) {
                try {
                    $uploadedReferences = [];
                    foreach ($request->file('reference_photos', []) as $photo) {
                        $refName = $this->uploadToComfyCloud($photo, $comfyApiKey, $comfyApiUrl);
                        if ($refName)
                            $uploadedReferences[] = $refName;
                    }

                    $workflow = json_decode(file_get_contents(storage_path('app/Carousel-Workflow.json')), true);
                    $workflow["11"]["inputs"]["prompt"] = $finalPrompt;

                    if (!empty($uploadedReferences)) {
                        $workflow["20"]["inputs"]["image"] = $uploadedReferences[0];
                    } else {
                        $workflow["20"]["inputs"]["image"] = "blank.png";
                    }

                    $dimensions = $this->getDimensionsFromRatio($request->aspect_ratio);
                    $workflow["16"]["inputs"]["width"] = $dimensions['width'];
                    $workflow["16"]["inputs"]["height"] = $dimensions['height'];
                    $workflow["16"]["inputs"]["batch_size"] = 1;
                    $workflow["13"]["inputs"]["seed"] = rand(10000000000, 99999999999);

                    $response = Http::withHeaders([
                        'X-API-Key' => $comfyApiKey,
                        'Content-Type' => 'application/json'
                    ])->post("{$comfyApiUrl}/api/prompt", [
                                'prompt' => $workflow
                            ]);

                    if ($response->successful()) {
                        return response()->json([
                            'success' => true,
                            'status' => 'processing',
                            'prompt_ids' => [$response->json('prompt_id')]
                        ], 200);
                    }
                } catch (\Exception $comfyErr) {
                    Log::warning('ComfyUI Cloud Submit Error: ' . $comfyErr->getMessage());
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses render visual dengan AI. Pastikan API key terkonfigurasi.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Carousel Generate Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    private function prepareTextGenerationWorkflow(string $workflowPath, string $fullPromptText): ?array
    {
        if (!file_exists($workflowPath)) {
            Log::warning("Text-Generation.json workflow tidak ditemukan di $workflowPath");
            return null;
        }

        $workflow = json_decode(file_get_contents($workflowPath), true);
        if (!$workflow) {
            Log::warning("Gagal parse Text-Generation.json");
            return null;
        }

        // Jika format adalah UI format (memiliki "nodes")
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
                    $inputs['prompt'] = $fullPromptText;
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
                                $links[$linkId]['origin_slot']
                            ];
                        }
                    }
                }

                $apiWorkflow[$id] = [
                    'class_type' => $classType,
                    'inputs' => $inputs
                ];
            }
            return $apiWorkflow;
        }

        // Jika sudah API format
        $foundNode = false;
        foreach ($workflow as $nodeId => &$node) {
            if (isset($node['class_type']) && $node['class_type'] === 'TextGenerate') {
                $node['inputs']['prompt'] = $fullPromptText;
                $foundNode = true;
                break;
            }
        }
        
        if (!$foundNode) {
            Log::warning('Node TextGenerate tidak ditemukan di Text-Generation.json API format');
        }

        return $workflow;
    }

    private function generatePromptFromComfy($pdfText, $reelNumber)
    {
        $comfyApiKey = env('COMFY_CLOUD_API_KEY');
        $comfyApiUrl = rtrim(env('COMFY_CLOUD_URL', 'https://cloud.comfy.org'), '/');

        if (!$comfyApiKey) {
            return "Cinematic shot, highly detailed, photorealistic, scene describing reel {$reelNumber}";
        }

        $systemInstruction = "You are an expert AI image prompt generator. I will give you a messy extracted text from a PDF storyboard. 
        Your task is to find the scene description for 'Reel {$reelNumber}' or 'Scene {$reelNumber}'. 
        Extract ONLY the visual description, translate it to English if needed, and rewrite it as a highly detailed, comma-separated prompt for a Text-to-Image AI model.
        Do NOT include audio, sound effects, duration, or any conversational text. Respond ONLY with the final prompt, nothing else.";

        $workflowPath = storage_path('app/Text-Generation.json');
        $fullPromptText = $systemInstruction . "\n\n" . $pdfText;
        $workflow = $this->prepareTextGenerationWorkflow($workflowPath, $fullPromptText);

        if (!$workflow) {
            return "Cinematic shot, highly detailed, photorealistic, scene describing reel {$reelNumber}";
        }

        try {
            // 1. Submit prompt ke ComfyUI
            $response = Http::timeout(60)->withHeaders([
                'X-API-Key' => $comfyApiKey,
                'Content-Type' => 'application/json'
            ])->post("{$comfyApiUrl}/api/prompt", [
                        'prompt' => $workflow
                    ]);

            if ($response->successful()) {
                $promptId = $response->json('prompt_id');
                if ($promptId) {
                    // 2. Polling API history sampai selesai
                    $maxPolls = 120; // 120 * 2 = 240 detik maksimal
                    $pollInterval = 2; // detik

                    for ($i = 0; $i < $maxPolls; $i++) {
                        sleep($pollInterval);
                        $historyRes = Http::timeout(30)->withHeaders([
                            'X-API-Key' => $comfyApiKey,
                        ])->get("{$comfyApiUrl}/api/history/{$promptId}");

                        if ($historyRes->successful()) {
                            $historyData = $historyRes->json();
                            if (isset($historyData[$promptId])) {
                                $outputs = $historyData[$promptId]['outputs'] ?? [];
                                foreach ($outputs as $nodeId => $output) {
                                    if (isset($output['text']) && is_array($output['text']) && !empty($output['text'][0])) {
                                        return trim($output['text'][0]);
                                    }
                                    if (isset($output['string']) && is_array($output['string']) && !empty($output['string'][0])) {
                                        return trim($output['string'][0]);
                                    }
                                    if (is_array($output)) {
                                        foreach ($output as $val) {
                                            if (is_array($val) && isset($val[0]) && is_string($val[0])) {
                                                return trim($val[0]);
                                            }
                                        }
                                    }
                                }
                                break;
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('ComfyUI Prompt Generator Error: ' . $e->getMessage());
        }

        return "Cinematic shot, highly detailed, photorealistic, scene describing reel {$reelNumber}, natural lighting, high quality";
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

    public function checkStatus($prompt_ids_string)
    {
        if ($prompt_ids_string === 'done_direct') {
            return response()->json([
                'status' => 'done',
                'message' => 'Proses selesai.'
            ]);
        }

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
}