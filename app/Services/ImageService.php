<?php

namespace App\Services;

use App\Services\Providers\GrokImageService;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * FIX MAPPING: sebelumnya kelas ini juga punya generateWithComfy() lewat
 * ComfyImageService untuk Gabungkan Foto & Edit Foto. Itu SEKARANG DIHAPUS
 * karena duplikat - Gabungkan Foto & Edit Foto sudah ditangani langsung
 * oleh GabungController & EditController (implementasi ComfyUI milik
 * Candra yang sudah terbukti jalan), tanpa lewat ImageService sama sekali.
 *
 * ImageService sekarang murni untuk fitur yang memakai Grok: Produk Artist
 * dan Carousel.
 */
class ImageService
{
    protected GrokImageService $grok;

    public function __construct(GrokImageService $grok)
    {
        $this->grok = $grok;
    }

    /**
     * Dipakai: Produk Artist.
     */
    public function generateWithGrok(array $imageFiles, string $prompt, string $ratio, int $count = 1): array
    {
        $result = $this->grok->generate($imageFiles, $prompt, $ratio, $count);
        $result['provider'] = 'Grok (xAI)';

        return $result;
    }

    /**
     * Dipakai: Carousel. Analisis PDF + render tiap scene, semua lewat Grok.
     */
    public function generateFromStoryboard(
        UploadedFile $storyboardPdf,
        array $referencePhotos,
        string $prompt,
        string $ratio,
        ?int $reelNumber = null
    ): array {

        $referencePhotos = array_values(array_filter(
            $referencePhotos,
            fn ($file) => $file instanceof UploadedFile
        ));

        $scenes = $this->grok->analyzeScenes($storyboardPdf);

        if ($reelNumber !== null) {

            $scenes = array_values(array_filter(
                $scenes,
                fn (array $scene) => ($scene['reel'] ?? 1) === $reelNumber
            ));

            if (empty($scenes)) {
                throw new Exception("Scene untuk reel nomor {$reelNumber} tidak ditemukan di storyboard PDF ini.");
            }
        }

        $images = [];

        foreach ($scenes as $index => $scene) {

            if ($index > 0) {
                sleep(3);
            }

            $sceneInstruction = sprintf(
                "Scene %d.\nVisual: %s\nCamera: %s\nMood: %s\n\n%s",
                $scene['scene'],
                $scene['visual'],
                $scene['camera'],
                $scene['mood'],
                trim($prompt)
            );

            try {

                $result = $this->grok->generate($referencePhotos, $sceneInstruction, $ratio, 1);

                foreach ($result['images'] as $image) {
                    $images[] = array_merge($image, [
                        'scene' => $scene['scene'],
                        'reel' => $scene['reel'] ?? 1,
                        'label' => "Scene {$scene['scene']}",
                    ]);
                }

            } catch (Throwable $e) {

                Log::warning("Gagal generate scene {$scene['scene']} lewat Grok", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (empty($images)) {
            throw new Exception('Semua scene gagal digenerate lewat Grok.');
        }

        return [
            'provider' => 'Grok (xAI)',
            'images' => $images,
        ];
    }
}