<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['location', 'image', 'email'])]
class ContactInformation extends Model
{
    protected $table = 'contact_information';
}
