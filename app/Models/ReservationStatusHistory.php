<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationStatusHistory extends Model
{
    //
    protected $fillable = [
        'reservation_id',
        'status',
        'title',
        'description',
        'created_by',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
