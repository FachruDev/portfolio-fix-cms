<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['project_category_id', 'title', 'subtitle', 'slug', 'description_rich', 'thumbnail', 'is_active', 'github_url', 'live_url'])]
class Project extends Model
{
    protected $attributes = [
        'is_active' => true,
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
        return $this->hasMany(ProjectCoreFeature::class);
    }

    public function designHighlights(): HasMany
    {
        return $this->hasMany(ProjectDesignHighlight::class);
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
