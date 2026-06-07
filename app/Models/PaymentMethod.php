<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'type',
        'provider',
        'account_number',
        'account_name',
        'is_active',
    ];

    const TYPES = [
        'bank_transfer' => 'Bank Transfer',
        'e_wallet' => 'E-Wallet',
    ];

    public function getTypeNameAttribute()
    {
        return self::TYPES[$this->type] ?? 'Unknown';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
