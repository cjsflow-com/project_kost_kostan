<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RoomFacility;
use App\Models\RoomImage;

class Room extends Model
{
    //
    protected $fillable = [
        'room_number',
        'title',
        'description',
        'price_per_month',
        'room_size',
        'floor',
        'capacity',
        'status_id',
    ];

    const STATUS = [
        0 => 'Perbaikan',
        1 => 'Tersedia',
        2 => 'Sudah Ditempati',
    ];

    public function getStatusNameAttribute()
    {
        return self::STATUS[$this->status_id] ?? 'Unknown';
    }

    public function facilities()
    {
        return $this->belongsToMany(
            RoomFacility::class,
            'room_facility_rooms',
            'room_id',
            'room_facility_id'
        );
    }

    public function images()
    {
        return $this->hasMany(RoomImage::class);
    }
}
