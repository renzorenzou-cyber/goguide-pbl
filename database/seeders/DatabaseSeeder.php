<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Guide;
use App\Models\PaketWisata;
use App\Models\User;
use App\Models\Wisatawan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * DATA CONTOH untuk mengisi dashboard saat presentasi (2 wisatawan dan 5 booking).
 * Hapus setelah presentasi:
 *   php artisan tinker
 *   App\Models\Booking::where('kode_booking', 'like', 'GGG-DEMO%')->delete();
 *   App\Models\User::whereIn('email', ['customer1@goguide.test', 'customer2@goguide.test'])->delete();
 * Jalankan SkemaBaruSeeder lebih dulu.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('role', 'admin')->value('id');

        $budi = User::firstOrCreate(['email' => 'customer1@goguide.test'], [
            'name' => 'Budi Santoso', 'phone' => '081234567801',
            'password' => Hash::make('password'), 'role' => 'wisatawan',
        ]);
        $sari = User::firstOrCreate(['email' => 'customer2@goguide.test'], [
            'name' => 'Sari Wulandari', 'phone' => '081234567802',
            'password' => Hash::make('password'), 'role' => 'wisatawan',
        ]);

        $wisatawanBudi = Wisatawan::firstOrCreate(['id_user' => $budi->id], [
            'nama_wisatawan' => $budi->name,
            'email' => $budi->email,
            'telepon' => $budi->phone,
            'tanggal_daftar' => $budi->created_at ?? now(),
        ]);
        $wisatawanSari = Wisatawan::firstOrCreate(['id_user' => $sari->id], [
            'nama_wisatawan' => $sari->name,
            'email' => $sari->email,
            'telepon' => $sari->phone,
            'tanggal_daftar' => $sari->created_at ?? now(),
        ]);

        $ayu  = Guide::where('email', 'guide1@goguide.test')->first();
        $made = Guide::where('email', 'guide2@goguide.test')->first();
        if (! $ayu || ! $made) {
            $this->command?->warn('Guide contoh belum ada. Jalankan SkemaBaruSeeder dulu.');
            return;
        }

        $tglAyu  = $ayu->jadwal()->orderBy('tanggal_aktif')->pluck('tanggal_aktif')->map(fn ($t) => $t->toDateString())->values();
        $tglMade = $made->jadwal()->orderBy('tanggal_aktif')->pluck('tanggal_aktif')->map(fn ($t) => $t->toDateString())->values();

        $daftar = [
            // kode, wisatawan, guide, nama paket, tanggal tour, peserta, status
            ['GGG-DEMO0001', $wisatawanBudi, $ayu,  'Ubud Alam dan Air Terjun',     $tglAyu[0],  2, 'dikonfirmasi'],
            ['GGG-DEMO0002', $wisatawanBudi, $ayu,  'Sanur dan Canggu Pantai',      $tglAyu[1],  3, 'sudah_bayar'],
            ['GGG-DEMO0003', $wisatawanSari, $made, 'Sunrise Trekking Gunung Batur', $tglMade[0], 4, 'belum_bayar'],
            ['GGG-DEMO0004', $wisatawanSari, $made, 'Uluwatu Pura dan Pantai',       $tglMade[2], 2, 'dibatalkan'],
            ['GGG-DEMO0005', $wisatawanBudi, $ayu,  'Nusa Penida Day Trip',          now()->subDays(7)->toDateString(), 2, 'dikonfirmasi'],
        ];

        foreach ($daftar as $n => [$kode, $wisatawan, $guide, $namaPaket, $tgl, $peserta, $status]) {
            $paket = PaketWisata::where('nama_paket', $namaPaket)->first();
            if (! $paket) {
                continue;
            }

            $total = $paket->harga_paket * $peserta;
            $sudahBayar = in_array($status, ['sudah_bayar', 'dikonfirmasi'], true);

            Booking::firstOrCreate(['kode_booking' => $kode], [
                'id_wisatawan'       => $wisatawan->id_wisatawan,
                'id_paket'           => $paket->id_paket,
                'id_guide'           => $guide->id_guide,
                'tanggal_transaksi'  => now()->subDays(8 - $n),
                'tanggal_booking'    => $tgl,
                'jumlah_peserta'     => $peserta,
                'catatan'            => $n === 0 ? 'Mohon dijemput di hotel.' : null,
                'harga_satuan'       => $paket->harga_paket,
                'total_bayar'        => $total,
                'fee_guide'          => intdiv($total * $paket->persen_fee_guide, 100),
                'kode_va'            => '88081234' . str_pad((string) ($n + 1), 5, '0', STR_PAD_LEFT),
                'id_transaksi_gateway' => 'SIM-' . $kode,
                'batas_bayar'        => now()->addDay(),
                'tanggal_bayar'      => $sudahBayar ? now()->subDays(6 - $n) : null,
                'status'             => $status,
                'alasan_batal'       => $status === 'dibatalkan' ? 'Batas waktu pembayaran terlewati' : null,
                'dikonfirmasi_oleh'  => $status === 'dikonfirmasi' ? $adminId : null,
                'tanggal_konfirmasi' => $status === 'dikonfirmasi' ? now()->subDays(5 - $n) : null,
            ]);
        }
    }
}
