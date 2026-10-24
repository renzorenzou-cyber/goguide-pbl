<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';
    protected $fillable = ['id_user', 'id_booking', 'jenis', 'judul', 'isi', 'dibaca'];
    protected $casts = ['dibaca' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking', 'id_booking');
    }
}
