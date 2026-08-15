<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prompt extends Model
{
    protected $fillable = [
        'category',
        'title',
        'template',
        'variables',
        'rating',
        'usage_count',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'rating' => 'float',
            'usage_count' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Ganti semua placeholder {variabel} di template dengan nilai
     * yang dikasih user, misal ->fill(['nama_produk' => 'Kopi Susu']).
     */
    public function fill_(array $values): string
    {
        $result = $this->template;

        foreach ($values as $key => $value) {
            $result = str_replace('{' . $key . '}', (string) $value, $result);
        }

        return $result;
    }
}