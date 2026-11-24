<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Profil wisatawan terhubung ke akun login di tabel users.
 */
class Wisatawan extends Model
{
    protected $table = 'wisatawan';
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
