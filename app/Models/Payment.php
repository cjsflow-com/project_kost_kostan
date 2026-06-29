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

    protected static function booted()
    {
        static::deleting(function ($payment) {
            // Delete the payment proof file from storage
            $payment->deletePaymentProofFile($payment->payment_proof);
        });

        // Jalan ketika data diedit/update
        static::updating(function ($payment) {
            // Cek apakah field payment_proof berubah
            if ($payment->isDirty('payment_proof')) {
                // Ambil payment_proof lama dari database
                $oldPaymentProof = $payment->getOriginal('payment_proof');

                // Hapus payment_proof lama
                $payment->deletePaymentProofFile($oldPaymentProof);
            }
        });
    }

    private function deletePaymentProofFile(?string $path): void
    {
        if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        }
    }

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
