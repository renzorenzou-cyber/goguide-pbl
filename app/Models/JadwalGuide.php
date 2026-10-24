<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalGuide extends Model
{
    protected $table = 'jadwal_guide';
    protected $primaryKey = 'id_jadwal';
    protected $fillable = ['id_guide', 'tanggal_aktif'];
    protected $casts = ['tanggal_aktif' => 'date'];

    public function guide()
    {
        return $this->belongsTo(Guide::class, 'id_guide', 'id_guide');
    }
}
