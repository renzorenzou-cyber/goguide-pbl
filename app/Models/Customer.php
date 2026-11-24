<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Profil wisatawan. Password dan login tetap di tabel users (satu pintu login untuk semua peran),
 * baris customer dibuat otomatis saat akun ber-role customer dibuat (lihat User::booted).
 */
class Customer extends Model
{
    protected $table = 'customer';
    protected $primaryKey = 'id_wisatawan';
    protected $fillable = ['id_user', 'nama_wisatawan', 'email', 'telepon', 'tanggal_daftar', 'status'];
    protected $casts = ['tanggal_daftar' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function booking()
    {
        return $this->hasMany(Booking::class, 'id_wisatawan', 'id_wisatawan');
    }
}
