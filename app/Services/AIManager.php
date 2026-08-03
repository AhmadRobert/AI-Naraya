<?php

namespace App\Services;

use App\Services\Providers\GrokService;

/**
 * BARU: Agnes AI dihapus total dari jalur teks. Grok (xAI) sekarang
 * satu-satunya provider untuk Caption & Storyboard, tidak ada fallback.
 * lastProvider() dipertahankan (selalu 'Grok (xAI)') supaya
 * CaptionController/StoryboardController tidak perlu diubah.
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

    public function generateCaption(string $prompt): string
    {
        return $this->grok->generate($prompt);
    }

    public function generateStoryboard(string $prompt): string
    {
        return $this->grok->generate($prompt);
    }
}