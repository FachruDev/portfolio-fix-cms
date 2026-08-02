<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['site_title', 'logo_light', 'logo_dark', 'favicon', 'copyright_text'])]
class SiteSetting extends Model {}
