<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    //
    protected $fillable = [
        'room_id',
        'customer_id',
        'start_date',
        'end_date',
        'status_id',
    ];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    const STATUS = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function getStatusNameAttribute()
    {
        return self::STATUS[$this->status_id] ?? 'Unknown';
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

}
