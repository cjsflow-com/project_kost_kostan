<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralFacility extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'image',
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
