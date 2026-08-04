<?php

namespace App\Models;

use App\Models\Concerns\FlushesPortfolioContentCache;
use App\Models\Concerns\HasDisplayOrder;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['landing_page_about_section_id', 'title', 'subtitle', 'icon', 'order_no'])]
class LandingPageAboutItem extends Model
{
    use FlushesPortfolioContentCache, HasDisplayOrder;

    protected $attributes = [
        'order_no' => 0,
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(LandingPageAboutSection::class, 'landing_page_about_section_id');
    }
}
