<?php

namespace App\Http\Controllers;

use App\Services\StoryboardService;
use Illuminate\Http\Request;
use Throwable;

class StoryboardController extends Controller
{
    protected StoryboardService $storyboardService;

    public function __construct(StoryboardService $storyboardService)
    {
        $this->storyboardService = $storyboardService;
    }

    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:10000',
        ]);

        try{

            $storyboard = $this->storyboardService->generate(
                $request->prompt
            );

            return response()->json([
                'success'=>true,
                // FIX: provider sekarang dilaporkan dinamis - "Grok (xAI)"
                // kalau provider utama berhasil, atau "Agnes AI (backup)"
                // kalau Grok gagal dan otomatis fallback ke Agnes.
                'provider'=>$this->storyboardService->lastProvider(),
                'storyboard'=>$storyboard
            ]);

        }catch(Throwable $e){

            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage()
            ],500);

        }
    }
}