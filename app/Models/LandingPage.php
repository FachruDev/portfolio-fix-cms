<?php

namespace App\Models;

use App\Models\Concerns\FlushesPortfolioContentCache;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'subtitle', 'meta_title', 'meta_description', 'og_image'])]
class LandingPage extends Model
{
    use FlushesPortfolioContentCache;

    protected $table = 'landing_page';
}
