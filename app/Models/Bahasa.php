<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bahasa extends Model
{
    protected $table = 'bahasa';
    protected $primaryKey = 'id_bahasa';
    protected $fillable = ['nama_bahasa'];

    public function guide()
    {
        return $this->belongsToMany(Guide::class, 'detail_bahasa_guide', 'id_bahasa', 'id_guide', 'id_bahasa', 'id_guide')
            ->withPivot('tingkat_kemampuan');
    }
}
