<?php

namespace App\Models;

use App\Models\Concerns\FlushesPortfolioContentCache;
use App\Models\Concerns\HasDisplayOrder;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_id', 'description', 'order_no'])]
class ProjectCoreFeature extends Model
{
    use FlushesPortfolioContentCache, HasDisplayOrder;

    protected $attributes = [
        'order_no' => 0,
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
