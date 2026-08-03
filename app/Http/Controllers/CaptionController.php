<?php

namespace App\Http\Controllers;

use App\Services\CaptionService;
use Illuminate\Http\Request;
use Throwable;

class CaptionController extends Controller
{
    protected CaptionService $captionService;

    public function __construct(CaptionService $captionService)
    {
        $this->captionService = $captionService;
    }

    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:5000',
            'style' => 'sometimes|string|max:100',
            'length' => 'sometimes|integer|min:1|max:1000',
        ]);

        try {

            $caption = $this->captionService->generate(
                $request->prompt,
                $request->input('style', 'default'),
                $request->input('length', 100)
            );

            return response()->json([
                'success' => true,
                // FIX: provider sekarang dilaporkan dinamis - "Grok (xAI)"
                // kalau provider utama berhasil, atau "Agnes AI (backup)"
                // kalau Grok gagal dan otomatis fallback ke Agnes.
                // (samakan pola dengan StoryboardController)
                'provider' => $this->captionService->lastProvider(),
                'caption' => $caption
            ]);

        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }
}