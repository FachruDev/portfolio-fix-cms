<?php

namespace App\Models;

use App\Models\Concerns\FlushesPortfolioContentCache;
use App\Models\Concerns\HasDisplayOrder;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'slug', 'is_active', 'order_no'])]
class NotesCategory extends Model
{
    use FlushesPortfolioContentCache, HasDisplayOrder;

    protected $table = 'notes_categories';

    protected $attributes = [
        'is_active' => true,
        'order_no' => 0,
    ];

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'notes_category_id')->published()->latest('published_at')->latest('id');
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
