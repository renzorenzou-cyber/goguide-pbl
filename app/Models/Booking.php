<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_code', 'user_id', 'tour_id', 'tour_date',
        'participants', 'total_price', 'status', 'notes',
    ];

    protected $casts = [
        'tour_date' => 'date',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function tour() { return $this->belongsTo(Tour::class); }
    public function payments() { return $this->hasMany(Payment::class); }
}