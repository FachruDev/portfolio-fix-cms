<?php

namespace App\Models;

use App\Models\Concerns\FlushesPortfolioContentCache;
use App\Models\Concerns\PublishesContent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['project_category_id', 'title', 'subtitle', 'slug', 'description_rich', 'thumbnail', 'thumbnail_alt', 'is_active', 'is_featured', 'published_at', 'github_url', 'live_url', 'meta_title', 'meta_description', 'og_image'])]
class Project extends Model
{
    use FlushesPortfolioContentCache, PublishesContent;

    protected $attributes = [
        'is_active' => true,
        'is_featured' => false,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function techStacks(): BelongsToMany
    {
        return $this->belongsToMany(TechStack::class, 'project_tech_stack');
    }

    public function coreFeatures(): HasMany
    {
        return $this->hasMany(ProjectCoreFeature::class)->ordered();
    }

    public function designHighlights(): HasMany
    {
        return $this->hasMany(ProjectDesignHighlight::class)->ordered();
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
