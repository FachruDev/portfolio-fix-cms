<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'subtitle'])]
class NotesHero extends Model
{
    protected $table = 'notes_hero';
}
