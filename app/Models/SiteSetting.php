<?php

namespace App\Models;

use App\Models\Concerns\FlushesPortfolioContentCache;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['site_title', 'logo_light', 'logo_light_alt', 'logo_dark', 'logo_dark_alt', 'favicon', 'copyright_text', 'meta_title', 'meta_description', 'og_image'])]
class SiteSetting extends Model
{
    use FlushesPortfolioContentCache;
}
