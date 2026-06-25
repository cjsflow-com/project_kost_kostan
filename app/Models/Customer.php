<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    //

    use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'gender',
        'address',
    ];

    const GENDER = [
        1 => 'Laki-Laki',
        2 => 'Perempuan',
    ];

    public function getGenderLabelAttribute()
    {
        return self::GENDER[$this->gender] ?? 'Tidak Diketahui';
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'customer_id');
    }


}
