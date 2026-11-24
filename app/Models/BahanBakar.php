<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBakar extends Model
{
    protected $table = 'bahan_bakar';
    protected $primaryKey = 'id_bahan_bakar';
    protected $fillable = [
        'id_booking', 'jenis_bahan_bakar', 'jumlah_liter', 'harga_per_liter',
        'total_biaya', 'tanggal_isi', 'catatan',
    ];
    protected $casts = [
        'tanggal_isi'  => 'date',
        'jumlah_liter' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking', 'id_booking');
    }
}