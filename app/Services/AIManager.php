<?php

namespace App\Services;

use App\Services\Providers\GrokService;

/**
 * FIX MAPPING: Caption sudah dipindah ke ComfyTextService (lihat
 * CaptionService.php), jadi method generateCaption() di sini dihapus -
 * AIManager sekarang murni pembungkus GrokService khusus untuk Storyboard
 * (satu-satunya fitur teks yang masih pakai Grok).
 *
 * Tidak ada fallback provider (Agnes sudah dihapus total sebelumnya) -
 * satu fitur = satu provider, sesuai mapping akhir project ini.
 */
class AIManager
{
    protected GrokService $grok;

    public function __construct(GrokService $grok)
    {
        $this->grok = $grok;
    }

    public function lastProvider(): string
    {
        return 'Grok (xAI)';
    }

    public function generateStoryboard(string $prompt): string
    {
        return $this->grok->generate($prompt);
    }
}