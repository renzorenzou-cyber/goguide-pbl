<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourGuide extends Model
{
    protected $table = 'tour_guide';
    protected $primaryKey = 'id_tourguide';
    protected $fillable = ['id_guide', 'id_paket', 'status', 'catatan'];

    public function guide()
    {
        return $this->belongsTo(Guide::class, 'id_guide', 'id_guide');
    }

    public function paket()
    {
        return $this->belongsTo(PaketWisata::class, 'id_paket', 'id_paket');
    }
}
