<?php

namespace App\Services;

use App\Models\Prompt;
use Illuminate\Database\Eloquent\Collection;

class PromptLibraryService
{
    /**
     * Ambil semua prompt, opsional difilter per kategori.
     */
    public function list(?string $category = null): Collection
    {
        $query = Prompt::query()->orderBy('category')->orderBy('title');

        if ($category) {
            $query->where('category', $category);
        }

        return $query->get();
    }

    public function find(int $id): Prompt
    {
        return Prompt::findOrFail($id);
    }

    public function create(array $data, ?int $userId = null): Prompt
    {
        return Prompt::create([
            'category' => $data['category'],
            'title' => $data['title'],
            'template' => $data['template'],
            'variables' => $data['variables'] ?? $this->extractVariables($data['template']),
            'rating' => $data['rating'] ?? null,
            'created_by' => $userId,
        ]);
    }

    public function update(int $id, array $data): Prompt
    {
        $prompt = $this->find($id);

        $prompt->update([
            'category' => $data['category'] ?? $prompt->category,
            'title' => $data['title'] ?? $prompt->title,
            'template' => $data['template'] ?? $prompt->template,
            'variables' => $data['variables'] ?? $this->extractVariables($data['template'] ?? $prompt->template),
            'rating' => $data['rating'] ?? $prompt->rating,
        ]);

        return $prompt;
    }

    public function delete(int $id): void
    {
        $this->find($id)->delete();
    }

    /**
     * Tandai prompt ini baru dipakai (naikkan usage_count) dan kembalikan
     * hasil template yang sudah diisi variabelnya (kalau ada dikirim).
     */
    public function markUsed(int $id, array $values = []): array
    {
        $prompt = $this->find($id);

        $prompt->increment('usage_count');

        return [
            'prompt' => $prompt,
            'filled' => $prompt->fill_($values),
        ];
    }

    public function categories(): array
    {
        return [
            'Produk',
            'Promo',
            'Edukasi',
            'Testimoni',
            'Storyboard Video',
        ];
    }

    /**
     * Deteksi otomatis nama variabel dari pola {nama_variabel} di teks.
     */
    protected function extractVariables(string $template): array
    {
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $template, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }
}