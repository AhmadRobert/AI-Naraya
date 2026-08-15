<?php

namespace App\Http\Controllers;

use App\Services\PromptLibraryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PromptController extends Controller
{
    protected PromptLibraryService $promptLibrary;

    public function __construct(PromptLibraryService $promptLibrary)
    {
        $this->promptLibrary = $promptLibrary;
    }

    /**
     * GET /prompts - halaman UI Prompt Library
     */
    public function page()
    {
        return view('PromptLibrary', [
            'categories' => $this->promptLibrary->categories(),
        ]);
    }

    /**
     * GET /api/prompts - daftar semua prompt (opsional ?category=Produk)
     */
    public function index(Request $request)
    {
        $prompts = $this->promptLibrary->list($request->input('category'));

        return response()->json([
            'success' => true,
            'prompts' => $prompts,
            'categories' => $this->promptLibrary->categories(),
        ]);
    }

    /**
     * POST /api/prompts - tambah prompt baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:100',
            'title' => 'required|string|max:150',
            'template' => 'required|string|max:5000',
            'variables' => 'nullable|array',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        try {

            $prompt = $this->promptLibrary->create(
                $request->only(['category', 'title', 'template', 'variables', 'rating']),
                $request->user()?->id
            );

            return response()->json([
                'success' => true,
                'prompt' => $prompt,
            ], 201);

        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * PUT/PATCH /api/prompts/{id} - edit prompt
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'category' => 'sometimes|string|max:100',
            'title' => 'sometimes|string|max:150',
            'template' => 'sometimes|string|max:5000',
            'variables' => 'nullable|array',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        try {

            $prompt = $this->promptLibrary->update(
                $id,
                $request->only(['category', 'title', 'template', 'variables', 'rating'])
            );

            return response()->json([
                'success' => true,
                'prompt' => $prompt,
            ]);

        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * DELETE /api/prompts/{id}
     */
    public function destroy(int $id)
    {
        try {

            $this->promptLibrary->delete($id);

            return response()->json(['success' => true]);

        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * POST /api/prompts/{id}/use - tandai dipakai + kembalikan template
     * yang sudah diisi variabelnya (kalau dikirim di body 'values').
     */
    public function markUsed(Request $request, int $id)
    {
        try {

            $result = $this->promptLibrary->markUsed(
                $id,
                $request->input('values', [])
            );

            return response()->json([
                'success' => true,
                'prompt' => $result['prompt'],
                'filled' => $result['filled'],
            ]);

        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    protected function errorResponse(Throwable $e)
    {
        Log::error($e);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}