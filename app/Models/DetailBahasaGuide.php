<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailBahasaGuide extends Model
{
    protected $table = 'detail_bahasa_guide';
    protected $primaryKey = 'id_detail_bahasa';
    protected $fillable = ['id_guide', 'id_bahasa', 'tingkat_kemampuan'];

    public function guide()
    {
        return $this->belongsTo(Guide::class, 'id_guide', 'id_guide');
    }

    public function bahasa()
    {
        return $this->belongsTo(Bahasa::class, 'id_bahasa', 'id_bahasa');
    }
}
