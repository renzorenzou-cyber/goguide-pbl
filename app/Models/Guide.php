<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    protected $table = 'guide';
    protected $primaryKey = 'id_guide';
    protected $fillable = [
        'id_user', 'nama_guide', 'email', 'no_wa', 'no_telepon', 'alamat',
        'bio_singkat', 'bio_lengkap_file', 'rating', 'status_pendaftaran',
        'alasan_penolakan', 'disetujui_oleh', 'tanggal_persetujuan',
    ];
    protected $casts = [
        'rating' => 'decimal:2',
        'tanggal_persetujuan' => 'datetime',
    ];

    public function scopeDisetujui($query)
    {
        return $query->where('status_pendaftaran', 'disetujui');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalGuide::class, 'id_guide', 'id_guide');
    }

    public function paket()
    {
        return $this->belongsToMany(PaketWisata::class, 'tour_guide', 'id_guide', 'id_paket', 'id_guide', 'id_paket');
    }

    public function booking()
    {
        return $this->hasMany(Booking::class, 'id_guide', 'id_guide');
    }
}
