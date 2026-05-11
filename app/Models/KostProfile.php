<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KostProfile extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'address',
        'phone',
        'whatsapp',
        'email',
        'latitude',
        'longitude',
        'rules',
    ];
}
