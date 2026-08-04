<?php

namespace App\Models;

use App\Models\Concerns\FlushesPortfolioContentCache;
use App\Models\Concerns\HasDisplayOrder;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'subtitle', 'order_no'])]
class LandingPageAboutSection extends Model
{
    use FlushesPortfolioContentCache, HasDisplayOrder;

    protected $attributes = [
        'order_no' => 0,
    ];

    public function items(): HasMany
    {
        return $this->hasMany(LandingPageAboutItem::class)->ordered();
    }
}
