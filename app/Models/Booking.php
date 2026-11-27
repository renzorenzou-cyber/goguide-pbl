<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    public const STATUS = ['belum_bayar', 'sudah_bayar', 'dikonfirmasi', 'selesai', 'dibatalkan'];

    protected $table = 'booking';
    protected $primaryKey = 'id_booking';
    protected $fillable = [
        'kode_booking', 'id_wisatawan', 'id_paket', 'id_guide',
        'tanggal_transaksi', 'tanggal_booking', 'jam_mulai', 'jumlah_peserta', 'catatan',
        'harga_satuan', 'total_bayar', 'fee_guide',
        'kode_va', 'metode_pembayaran', 'id_transaksi_gateway', 'batas_bayar', 'tanggal_bayar',
        'status', 'alasan_batal', 'dikonfirmasi_oleh', 'tanggal_konfirmasi',
        'status_bayar_guide', 'tanggal_bayar_guide',
    ];
    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'tanggal_booking' => 'date',
        'batas_bayar' => 'datetime',
        'tanggal_bayar' => 'datetime',
        'tanggal_konfirmasi' => 'datetime',
        'tanggal_bayar_guide' => 'datetime',
    ];

    public function wisatawan()
    {
        return $this->belongsTo(Wisatawan::class, 'id_wisatawan', 'id_wisatawan');
    }

    public function paket()
    {
        return $this->belongsTo(PaketWisata::class, 'id_paket', 'id_paket');
    }

    public function guide()
    {
        return $this->belongsTo(Guide::class, 'id_guide', 'id_guide');
    }

    public function pengonfirmasi()
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }

        public function bahanBakar()
    {
        return $this->hasMany(BahanBakar::class, 'id_booking', 'id_booking');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_booking', 'id_booking');
    }
        public function ulasan()
    {
        return $this->hasOne(Ulasan::class, 'id_booking', 'id_booking');
    }

}
    

