<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPaketWisata extends Model
{
    protected $table = 'detail_paket_wisata';
    protected $primaryKey = 'id_detail_paket';
    protected $fillable = ['id_paket', 'id_objek', 'urutan'];

    public function paket()
    {
        return $this->belongsTo(PaketWisata::class, 'id_paket', 'id_paket');
    }

    public function objek()
    {
        return $this->belongsTo(ObjekWisata::class, 'id_objek', 'id_objek');
    }
}
