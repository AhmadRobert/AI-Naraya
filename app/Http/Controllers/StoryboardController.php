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