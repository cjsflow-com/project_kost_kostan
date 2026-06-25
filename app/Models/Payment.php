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
        'payment_proof',
        'paid_at',
        'note',
    ];

    protected $appends = ['status_label'];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_UPLOADED = 'uploaded';
    const STATUS_VERIFIED = 'verified';
    const STATUS_REJECTED = 'rejected';

    const STATUS = [
        self::STATUS_PENDING => 'Sedang Menunggu Pembayaran',
        self::STATUS_UPLOADED => 'Bukti Pembayaran Telah Diupload',
        self::STATUS_VERIFIED => 'Pembayaran Telah Diverifikasi',
        self::STATUS_REJECTED => 'Pembayaran Ditolak',
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
