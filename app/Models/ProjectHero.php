<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'subtitle'])]
class ProjectHero extends Model
{
    protected $table = 'project_hero';
}
