<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
    protected $fillable = [
        'reservation_id',
        'payment_method_id',
        'amount',
        'payment_code',
        'status',
        'paid_at',
        'note',
    ];

    protected $appends = ['status_label'];

    const STATUS = [
        'pending' => 'Sedang Menunggu Pembayaran',
        'uploaded' => 'Bukti Pembayaran Telah Diupload',
        'verified' => 'Pembayaran Telah Diverifikasi',
        'rejected' => 'Pembayaran Ditolak',
    ];

    public function getStatusLabelAttribute()
    {
        return self::STATUS[$this->status] ?? 'Unknown';
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
