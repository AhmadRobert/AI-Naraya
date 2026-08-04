<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ImageGenerationController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /*
    |--------------------------------------------------------------------------
    | Produk Artist -> Grok
    |--------------------------------------------------------------------------
    */

    public function generateArtist(Request $request)
    {
        $request->validate([
            'product_image' => 'required|image',
            'model_image' => 'required|image',
            'logo_image' => 'nullable|image',
            'prompt' => 'nullable|string',
            'ratio' => 'required|string',
            'count' => 'sometimes|integer|in:1,2,4,8',
        ]);

        try {

            $images = [
                $request->file('product_image'),
                $request->file('model_image'),
            ];

            $roleNote = 'Gambar pertama adalah PRODUK utama, gambar kedua adalah MODEL/ARTIS.';

            if ($request->hasFile('logo_image')) {
                $images[] = $request->file('logo_image');
                $roleNote .= ' Gambar ketiga adalah LOGO BRAND, tempatkan secara natural dan proporsional.';
            }

            $prompt = trim($roleNote . "\n\n" . (string) $request->input('prompt', ''));

            $result = $this->imageService->generateWithGrok(
                $images,
                $prompt,
                $request->ratio,
                (int) $request->input('count', 4)
            );

            return $this->successResponse($result);

        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Carousel -> Grok
    |--------------------------------------------------------------------------
    */

    public function renderCarousel(Request $request)
    {
        $request->validate([
            'storyboard_pdf' => 'required|file|mimes:pdf',
            'reference_photos' => 'nullable|array|max:3',
            'reference_photos.*' => 'image',
            'aspect_ratio' => 'required|string',
            'extra_prompt' => 'nullable|string',
            'reel_number' => 'nullable|integer|min:1',
        ]);

        try {

            $result = $this->imageService->generateFromStoryboard(
                $request->file('storyboard_pdf'),
                $request->hasFile('reference_photos') ? $request->file('reference_photos') : [],
                (string) $request->input('extra_prompt', ''),
                $request->aspect_ratio,
                $request->filled('reel_number') ? (int) $request->reel_number : null
            );

            return $this->successResponse($result);

        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    protected function successResponse(array $result)
    {
        $images = array_map(function (array $image) {

            $item = [
                'url' => "data:{$image['mime_type']};base64,{$image['base64']}",
            ];

            if (isset($image['scene'])) {
                $item['scene'] = $image['scene'];
            }

            if (isset($image['reel'])) {
                $item['reel'] = $image['reel'];
            }

            if (isset($image['label'])) {
                $item['label'] = $image['label'];
            }

            return $item;

        }, $result['images']);

        return response()->json([

            'success' => true,

            'provider' => $result['provider'] ?? null,

            'images' => $images,

        ]);
    }

    protected function errorResponse(Throwable $e)
    {
        Log::error($e);

        return response()->json([

            'success' => false,

            'message' => $e->getMessage(),

        ], 500);
    }

    public function downloadImage(Request $request)
    {
        $imageUrl = $request->query('url');

        if (! $imageUrl) {
            return abort(404);
        }

        $imageContent = file_get_contents($imageUrl);

        return response($imageContent, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'attachment; filename="hasil-edit-ai.jpg"',
        ]);
    }
}