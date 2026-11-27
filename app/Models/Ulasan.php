<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ulasan dan rating wisatawan untuk guide. Satu booking hanya boleh punya satu ulasan (kolom id_booking unik).
 */
class Ulasan extends Model
{
    protected $table = 'ulasan';
    protected $primaryKey = 'id_ulasan';
    protected $fillable = ['id_booking', 'id_wisatawan', 'id_guide', 'rating', 'komentar'];
    protected $casts = ['rating' => 'integer'];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking', 'id_booking');
    }

    public function wisatawan()
    {
        return $this->belongsTo(Wisatawan::class, 'id_wisatawan', 'id_wisatawan');
    }

    public function guide()
    {
        return $this->belongsTo(Guide::class, 'id_guide', 'id_guide');
    }
}