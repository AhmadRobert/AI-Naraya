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
            'max_length' => 'sometimes|integer|min:1',
            'sampling_mode' => 'sometimes|string',
            'temperature' => 'sometimes|numeric',
            'top_k' => 'sometimes|integer',
            'top_p' => 'sometimes|numeric',
            'repetition_penalty' => 'sometimes|numeric',
            'seed' => 'sometimes|integer',
            'presence_penalty' => 'sometimes|numeric',
        ]);

        try {
            $options = array_filter([
                'max_length' => $request->input('max_length'),
                'sampling_mode' => $request->input('sampling_mode'),
                'temperature' => $request->input('temperature'),
                'top_k' => $request->input('top_k'),
                'top_p' => $request->input('top_p'),
                'repetition_penalty' => $request->input('repetition_penalty'),
                'seed' => $request->input('seed'),
                'presence_penalty' => $request->input('presence_penalty'),
            ], fn ($val) => $val !== null);

            set_time_limit(0);

            $caption = $this->captionService->generate(
                $request->prompt,
                $request->input('style', 'default'),
                $request->input('length', 100),
                $options
            );

            return response()->json([
                'success' => true,
                'status' => 'done',
                'caption' => $caption,
                'provider' => $this->captionService->lastProvider(),
            ]);

        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function checkStatus($promptId)
    {
        try {
            $result = $this->captionService->checkStatus($promptId);

            if ($result['status'] === 'done') {
                return response()->json([
                    'success' => true,
                    'status' => 'done',
                    'caption' => $result['caption'],
                    'provider' => $this->captionService->lastProvider(),
                ]);
            }

            if ($result['status'] === 'failed') {
                return response()->json([
                    'success' => false,
                    'status' => 'failed',
                    'message' => $result['message'] ?? 'Gagal membuat caption.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'status' => 'processing',
                'message' => 'AI sedang menyusun caption...'
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}