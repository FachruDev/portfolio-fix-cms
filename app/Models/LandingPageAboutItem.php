<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['landing_page_about_section_id', 'title', 'subtitle', 'icon'])]
class LandingPageAboutItem extends Model
{
    public function section(): BelongsTo
    {
        return $this->belongsTo(LandingPageAboutSection::class, 'landing_page_about_section_id');
    }
}
