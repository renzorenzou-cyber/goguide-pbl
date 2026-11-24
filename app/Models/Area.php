<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'area';
    protected $primaryKey = 'id_area';
    protected $fillable = ['nama_area', 'kabupaten', 'alamat', 'latitude', 'longitude'];

    public function objek()
    {
        return $this->hasMany(ObjekWisata::class, 'id_area', 'id_area');
    }
}
