<?php

namespace App\Models;

use App\Models\Concerns\FlushesPortfolioContentCache;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['location', 'image', 'image_alt', 'email', 'meta_title', 'meta_description', 'og_image'])]
class ContactInformation extends Model
{
    use FlushesPortfolioContentCache;

    protected $table = 'contact_information';
}
