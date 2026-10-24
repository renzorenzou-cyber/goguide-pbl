<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjekWisata extends Model
{
    protected $table = 'objek_wisata';
    protected $primaryKey = 'id_objek';
    protected $fillable = ['nama_objek', 'id_kategori', 'id_area', 'deskripsi', 'foto'];

    public function kategori()
    {
        return $this->belongsTo(KategoriWisata::class, 'id_kategori', 'id_kategori');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area', 'id_area');
    }

    public function paket()
    {
        return $this->belongsToMany(PaketWisata::class, 'detail_paket_wisata', 'id_objek', 'id_paket', 'id_objek', 'id_paket');
    }
}
