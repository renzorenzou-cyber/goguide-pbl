<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id', 'reviewer_name', 'tour_id', 'guide_id',
        'booking_id', 'rating', 'comment',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function tour()    { return $this->belongsTo(Tour::class); }
    public function guide()   { return $this->belongsTo(Guide::class); }
    public function booking() { return $this->belongsTo(Booking::class); }
}