<?php

namespace App\Models;

use App\Models\Concerns\FlushesPortfolioContentCache;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'subtitle', 'photo', 'photo_alt', 'meta_title', 'meta_description', 'og_image'])]
class AboutPage extends Model
{
    use FlushesPortfolioContentCache;

    protected $table = 'about_page';
}
