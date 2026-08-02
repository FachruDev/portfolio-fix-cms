<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['hobby_section_id', 'photo'])]
class HobbyItem extends Model
{
    public function section(): BelongsTo
    {
        return $this->belongsTo(HobbySection::class, 'hobby_section_id');
    }
}
