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

    public function generate(string $prompt, string $style = 'default', int $length = 100, array $options = []): string
    {
        $finalPrompt = $this->buildFinalPrompt($prompt, $style, $length);

        return $this->comfy->generate($finalPrompt, $options);
    }

    public function submit(string $prompt, string $style = 'default', int $length = 100, array $options = []): string
    {
        $finalPrompt = $this->buildFinalPrompt($prompt, $style, $length);

        return $this->comfy->submitPrompt($finalPrompt, $options);
    }

    public function checkStatus(string $promptId): array
    {
        return $this->comfy->checkTextStatus($promptId);
    }

    protected function buildFinalPrompt(string $prompt, string $style, int $length): string
    {
        $styleMap = [
            'default'   => 'Santai dan Natural',
            'santai'    => 'Santai',
            'formal'    => 'Profesional',
            'persuasif' => 'Persuasif',
            'humoris'   => 'Lucu',
        ];

        $toneName = $styleMap[$style] ?? $style;

        return "You are an Expert Social Media Copywriter and Digital Marketer.
Your task is to generate a highly engaging, persuasive, and creative social media caption based on the provided input.

STRICT RULES:
1. LANGUAGE: You must write exclusively in natural, engaging, and modern Indonesian (Bahasa Indonesia).
2. STRUCTURE: Your caption must flow naturally and logically. It MUST contain a catchy opening sentence to grab attention, an engaging main explanation of the product/context, a clear Call to Action (e.g., \"Komen di bawah\", \"Klik link di bio\"), and 3 to 5 relevant hashtags at the very bottom.
3. NO LABELS: DO NOT explicitly write the words \"Hook:\", \"Body:\", \"CTA:\", or \"Hashtags:\" in your output. Blend them seamlessly into normal paragraphs.
4. FORMATTING: Use line breaks (paragraphs) for readability. Use emojis naturally and appropriately, do not overdo it.
5. TONE: The tone should be {$toneName}.
6. LENGTH: Aim for approximately {$length} words.
7. DIRECT OUTPUT ONLY: Output ONLY the final caption text. Do NOT include any introductory or conversational text like \"Here is your caption\" or \"Tentu, ini hasilnya\".

POST CONTEXT / PRODUCT DETAILS:
{$prompt}

CAPTION:
";
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