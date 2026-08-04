<?php

namespace App\Models;

use App\Models\Concerns\FlushesPortfolioContentCache;
use App\Models\Concerns\HasDisplayOrder;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'subtitle', 'order_no'])]
class ExpertiseSection extends Model
{
    use FlushesPortfolioContentCache, HasDisplayOrder;

    protected $attributes = [
        'order_no' => 0,
    ];
}
