<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    protected $table = 'guide';
    protected $primaryKey = 'id_guide';
    protected $fillable = [
        'id_user', 'nama_guide', 'email', 'no_wa', 'no_telepon', 'alamat',
'bio_singkat', 'bio_lengkap_file', 'sertifikat_file', 'pengalaman', 'tentang_saya', 'foto', 'rating',    ];
    protected $casts = [
        'rating' => 'decimal:2',
        'tanggal_persetujuan' => 'datetime',
    ];

    /** Guide yang sudah diverifikasi admin dan akunnya aktif (boleh dipesan). */
    public function scopeDisetujui($query)
    {
        return $query->where('guide.status_verifikasi', 'Terverifikasi')->where('guide.status', 'Aktif');
    }

    public function getFotoUrlAttribute(): ?string
    {
        if ($this->user && $this->user->photo_url) {
            return $this->user->photo_url;
        }

        return $this->foto && file_exists(public_path($this->foto)) ? asset($this->foto) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalGuide::class, 'id_guide', 'id_guide');
    }

    public function paket()
    {
        return $this->belongsToMany(PaketWisata::class, 'tour_guide', 'id_guide', 'id_paket', 'id_guide', 'id_paket')
            ->wherePivot('status', 'Aktif');
    }

    public function bahasa()
    {
        return $this->belongsToMany(Bahasa::class, 'detail_bahasa_guide', 'id_guide', 'id_bahasa', 'id_guide', 'id_bahasa')
            ->withPivot('tingkat_kemampuan');
    }

    public function booking()
    {
        return $this->hasMany(Booking::class, 'id_guide', 'id_guide');
    }
       public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'id_guide', 'id_guide');
    }

    /** Hasil: [true/false, pesan] */
    public function cekKetersediaan(string $tanggal): array
    {
        if ($tanggal < now()->addDay()->toDateString()) {
            return [false, 'Tanggal booking minimal besok.'];
        }

        $aktif = $this->jadwal()
            ->whereDate('tanggal_aktif', $tanggal)
            ->where('status', 'Tersedia')
            ->exists();

        if (! $aktif) {
            return [false, 'Guide tidak aktif pada tanggal ini.'];
        }

        $sudahDipesan = $this->booking()
            ->whereDate('tanggal_booking', $tanggal)
            ->where('status', '!=', 'dibatalkan')
            ->exists();

        if ($sudahDipesan) {
            return [false, 'Guide sudah dipesan pada tanggal ini.'];
        }

        return [true, 'Guide tersedia pada tanggal ini.'];
    }

    public function tanggalTersedia(int $batas = 6)
    {
        $terpesan = $this->booking()
            ->where('status', '!=', 'dibatalkan')
            ->pluck('tanggal_booking')
            ->map(fn ($t) => $t->toDateString())
            ->all();

        return $this->jadwal()
            ->where('status', 'Tersedia')
            ->whereDate('tanggal_aktif', '>=', now()->addDay()->toDateString())
            ->orderBy('tanggal_aktif')
            ->pluck('tanggal_aktif')
            ->map(fn ($t) => $t->toDateString())
            ->reject(fn ($t) => in_array($t, $terpesan, true))
            ->take($batas)
            ->values();
    }
}
