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
        'room_price',
        'admin_fee',
        'deposit',
        'total_price',
        'status',
        'note',
    ];

    protected $appends = ['status_label'];

       // keys untuk database
    const STATUS_PENDING = 'pending';
    const STATUS_WAITING_PAYMENT = 'waiting_payment';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    // mapping key => label untuk UI
    const STATUS_LABELS = [
        self::STATUS_PENDING => 'Menunggu Konfirmasi',
        self::STATUS_WAITING_PAYMENT => 'Menunggu Pembayaran',
        self::STATUS_APPROVED => 'Disetujui',
        self::STATUS_REJECTED => 'Ditolak',
        self::STATUS_CANCELLED => 'Dibatalkan',
        self::STATUS_EXPIRED => 'Kadaluarsa',
    ];

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
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
        return self::STATUS_LABELS[$this->status] ?? 'Tidak Diketahui';
    }

}
