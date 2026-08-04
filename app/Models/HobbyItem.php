<?php

namespace App\Models;

use App\Models\Concerns\FlushesPortfolioContentCache;
use App\Models\Concerns\HasDisplayOrder;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['hobby_section_id', 'photo', 'photo_alt', 'order_no'])]
class HobbyItem extends Model
{
    use FlushesPortfolioContentCache, HasDisplayOrder;

    protected $attributes = [
        'order_no' => 0,
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(HobbySection::class, 'hobby_section_id');
    }
}
