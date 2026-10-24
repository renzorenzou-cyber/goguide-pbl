<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketWisata extends Model
{
    protected $table = 'paket_wisata';
    protected $primaryKey = 'id_paket';
    protected $fillable = ['nama_paket', 'harga_paket', 'persen_fee_guide', 'durasi', 'deskripsi', 'foto', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function objek()
    {
        return $this->belongsToMany(ObjekWisata::class, 'detail_paket_wisata', 'id_paket', 'id_objek', 'id_paket', 'id_objek')
            ->withPivot('urutan')
            ->orderBy('detail_paket_wisata.urutan');
    }

    public function guide()
    {
        return $this->belongsToMany(Guide::class, 'tour_guide', 'id_paket', 'id_guide', 'id_paket', 'id_guide');
    }

    public function booking()
    {
        return $this->hasMany(Booking::class, 'id_paket', 'id_paket');
    }
}
