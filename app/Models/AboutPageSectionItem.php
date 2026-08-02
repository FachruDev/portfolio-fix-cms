<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['about_page_section_id', 'title', 'subtitle', 'icon', 'order_no'])]
class AboutPageSectionItem extends Model
{
    protected $attributes = [
        'order_no' => 0,
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(AboutPageSection::class, 'about_page_section_id');
    }
}
