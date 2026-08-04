<?php

namespace App\Services;

use App\Services\Providers\ComfyTextService;

/**
 * FIX MAPPING: sebelumnya Caption lewat AIManager (Grok). Sesuai mapping
 * final, Caption sekarang pakai ComfyUI (ComfyTextService), Grok dipakai
 * eksklusif untuk Produk Artist/Storyboard/Carousel.
 */
class CaptionService
{
    protected ComfyTextService $comfy;

    public function __construct(ComfyTextService $comfy)
    {
        $this->comfy = $comfy;
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

        return $this->comfy->generate($finalPrompt);
    }

    /**
     * Dipakai controller untuk melaporkan provider yang menangani request
     * Caption. Tidak ada fallback lagi (satu provider per fitur), jadi
     * selalu 'ComfyUI'.
     */
    public function lastProvider(): string
    {
        return 'ComfyUI';
    }
}