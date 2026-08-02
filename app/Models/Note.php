<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['notes_category_id', 'title', 'subtitle', 'slug', 'is_active'])]
class Note extends Model
{
    public const UPDATED_AT = null;

    protected $attributes = [
        'is_active' => true,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(NotesCategory::class, 'notes_category_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
