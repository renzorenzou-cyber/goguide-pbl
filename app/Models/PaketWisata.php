<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketWisata extends Model
{
    protected $table = 'paket_wisata';
    protected $primaryKey = 'id_paket';
    protected $fillable = [
        'nama_paket', 'harga_paket', 'persen_fee_guide', 'durasi',
        'deskripsi', 'fasilitas', 'foto', 'status',
    ];

    public function scopeAktif($query)
    {
        return $query->where('paket_wisata.status', 'Aktif');
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto && file_exists(public_path($this->foto)) ? asset($this->foto) : null;
    }

    public function objek()
    {
        return $this->belongsToMany(ObjekWisata::class, 'detail_paket_wisata', 'id_paket', 'id_objek', 'id_paket', 'id_objek')
            ->withPivot('urutan', 'durasi_kunjungan', 'keterangan')
            ->orderBy('detail_paket_wisata.urutan');
    }

    public function guide()
    {
        return $this->belongsToMany(Guide::class, 'tour_guide', 'id_paket', 'id_guide', 'id_paket', 'id_guide')
            ->wherePivot('status', 'Aktif');
    }

    public function booking()
    {
        return $this->hasMany(Booking::class, 'id_paket', 'id_paket');
    }
}
