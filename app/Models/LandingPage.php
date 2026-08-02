<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'subtitle'])]
class LandingPage extends Model
{
    protected $table = 'landing_page';
}
