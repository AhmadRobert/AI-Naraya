<?php

namespace App\Services;

use App\Services\Providers\ComfyImageService;
use App\Services\Providers\GrokImageService;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * BARU: provider sekarang dipetakan PER FITUR (bukan primary/backup lagi):
 * - Gabungkan Foto, Edit Foto -> ComfyUI
 * - Produk Artist, Carousel   -> Grok
 *
 * Agnes AI sudah dihapus total, tidak ada fallback antar provider lagi
 * di iterasi ini - kalau provider yang ditugaskan untuk fitur tsb gagal,
 * error asli langsung dilempar ke user (tidak dicoba provider lain).
 */
class ImageService
{
    protected GrokImageService $grok;

    protected ComfyImageService $comfy;

    public function __construct(GrokImageService $grok, ComfyImageService $comfy)
    {
        $this->grok = $grok;
        $this->comfy = $comfy;
    }

    /**
     * Dipakai: Gabungkan Foto, Edit Foto.
     */
    public function generateWithComfy(array $imageFiles, string $prompt, string $ratio, int $count = 1): array
    {
        $result = $this->comfy->generate($imageFiles, $prompt, $ratio, $count);
        $result['provider'] = 'ComfyUI';

        return $result;
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