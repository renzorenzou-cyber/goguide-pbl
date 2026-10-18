<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id', 'order_id', 'snap_token',
        'payment_type', 'status', 'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function booking() { return $this->belongsTo(Booking::class); }
}