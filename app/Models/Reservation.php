<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\User;

class Reservation extends Model
{
    //
    protected $fillable = [
        'user_id',
        'room_id',
        'start_date',
        'duration_month',
        'customer_id',
        'customer_ktp_card',
        'room_price',
        'admin_fee',
        'deposit',
        'total_price',
        'status',
        'note',
    ];

    protected $appends = ['status_label'];

    const STATUS = [
        'pending' => 'Menunggu Konfirmasi',
        'waiting_payment' => 'Menunggu Pembayaran',
        'payment_uploaded' => 'Bukti Pembayaran Diupload',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'cancelled' => 'Dibatalkan',
        'expired' => 'Kadaluarsa',
    ];

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function admin(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(ReservationStatusHistory::class);
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUS[$this->status] ?? 'Tidak Diketahui';
    }

}
