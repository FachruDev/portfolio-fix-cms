<?php

namespace App\Models;

use App\Models\Concerns\FlushesPortfolioContentCache;
use App\Models\Concerns\PublishesContent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['notes_category_id', 'title', 'subtitle', 'slug', 'meta_title', 'meta_description', 'og_image', 'is_active', 'is_featured', 'published_at'])]
class Note extends Model
{
    use FlushesPortfolioContentCache, PublishesContent;

    public const UPDATED_AT = null;

    protected $attributes = [
        'is_active' => true,
        'is_featured' => false,
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
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}
