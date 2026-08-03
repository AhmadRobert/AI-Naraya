<?php

namespace App\Services;

class CaptionService
{
    protected AIManager $ai;

    public function __construct(AIManager $ai)
    {
        $this->ai = $ai;
    }

    public function generate(string $prompt, string $style = 'default', int $length = 100): string
    {
        $finalPrompt = "

Buat caption Instagram.

Ide / Topik:
{$prompt}

Gaya bahasa:
{$style}

Perkiraan panjang caption (jumlah kata):
{$length}

Output:

- Hook
- Caption
- CTA
- Hashtag

";

        return $this->ai->generateCaption($finalPrompt);
    }

    /**
     * BARU: dipakai controller untuk melaporkan provider yang benar-benar
     * melayani request TERAKHIR (Grok atau Agnes AI kalau Grok fallback).
     * Panggil ini SETELAH generate().
     */
    public function lastProvider(): string
    {
        return $this->ai->lastProvider();
    }
}