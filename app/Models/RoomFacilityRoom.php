<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomFacilityRoom extends Model
{
    //
    protected $fillable = [
        'room_id',
        'room_facility_id',
    ];

    public function facilities()
    {
        return $this->belongsToMany(
            RoomFacility::class,
            'room_facility_rooms',
            'room_id',
            'room_facility_id'
        );
    }
}
