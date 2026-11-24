<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Bahasa;
use App\Models\Guide;
use App\Models\JadwalGuide;
use App\Models\KategoriWisata;
use App\Models\ObjekWisata;
use App\Models\PaketWisata;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Data contoh untuk pengembangan. Harga, persentase fee, dan nama guide hanya contoh.
 * Aman dijalankan ulang (memakai firstOrCreate/updateOrCreate).
 */
class SkemaBaruSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori
        $kategori = [];
        foreach ([
            'Gunung' => 'Wisata pendakian dan pemandangan pegunungan',
            'Danau' => 'Wisata danau dan sekitarnya',
            'Pantai' => 'Wisata pantai',
            'Air Terjun' => 'Wisata air terjun',
            'Budaya' => 'Wisata budaya, pura, dan sejarah',
            'Persawahan' => 'Wisata persawahan dan terasering',
        ] as $nama => $desk) {
            $kategori[$nama] = KategoriWisata::firstOrCreate(['nama_kategori' => $nama], ['deskripsi' => $desk]);
        }

        // Area (nama area, kabupaten)
        $area = [];
        foreach ([['Kintamani', 'Bangli'], ['Ubud', 'Gianyar'], ['Canggu', 'Badung'],
                  ['Uluwatu', 'Badung'], ['Nusa Penida', 'Klungkung'], ['Sanur', 'Denpasar']] as [$nama, $kab]) {
            $area[$nama] = Area::firstOrCreate(['nama_area' => $nama], ['kabupaten' => $kab]);
        }

        // Objek wisata: [nama, kategori, area]
        $objek = [];
        foreach ([
            ['Gunung Batur', 'Gunung', 'Kintamani'],
            ['Danau Batur', 'Danau', 'Kintamani'],
            ['Air Terjun Tegenungan', 'Air Terjun', 'Ubud'],
            ['Tegalalang Rice Terrace', 'Persawahan', 'Ubud'],
            ['Pura Uluwatu', 'Budaya', 'Uluwatu'],
            ['Pantai Kelingking', 'Pantai', 'Nusa Penida'],
            ['Pantai Sanur', 'Pantai', 'Sanur'],
            ['Pantai Batu Bolong', 'Pantai', 'Canggu'],
        ] as [$nama, $kat, $ar]) {
            $objek[$nama] = ObjekWisata::firstOrCreate(['nama_objek' => $nama], [
                'id_kategori' => $kategori[$kat]->id_kategori,
                'id_area' => $area[$ar]->id_area,
                'status' => 'Aktif',
            ]);
        }

        // Paket wisata: [nama, harga per peserta, persen fee guide, durasi, fasilitas, [objek...]]
        $paket = [];
        foreach ([
            ['Sunrise Trekking Gunung Batur', 450000, 70, '8 jam', 'Guide lokal, senter, air mineral, sarapan', ['Gunung Batur', 'Danau Batur']],
            ['Ubud Alam dan Air Terjun', 350000, 70, '6 jam', 'Guide lokal, air mineral', ['Air Terjun Tegenungan', 'Tegalalang Rice Terrace']],
            ['Uluwatu Pura dan Pantai', 400000, 70, '5 jam', 'Guide lokal, air mineral', ['Pura Uluwatu']],
            ['Nusa Penida Day Trip', 650000, 70, '10 jam', 'Guide lokal, air mineral', ['Pantai Kelingking']],
            ['Sanur dan Canggu Pantai', 300000, 70, '5 jam', 'Guide lokal, air mineral', ['Pantai Sanur', 'Pantai Batu Bolong']],
        ] as [$nama, $harga, $fee, $durasi, $fasilitas, $daftarObjek]) {
            $p = PaketWisata::updateOrCreate(['nama_paket' => $nama], [
                'harga_paket' => $harga,
                'persen_fee_guide' => $fee,
                'durasi' => $durasi,
                'deskripsi' => 'Paket ' . $nama . ' bersama guide lokal.',
                'fasilitas' => $fasilitas,
                'status' => 'Aktif',
            ]);
            foreach ($daftarObjek as $urutan => $namaObjek) {
                $p->objek()->syncWithoutDetaching([
                    $objek[$namaObjek]->id_objek => ['urutan' => $urutan + 1],
                ]);
            }
            $paket[$nama] = $p;
        }

        // Bahasa
        $bahasa = [];
        foreach (['Indonesia', 'Inggris', 'Jepang', 'Mandarin', 'Korea', 'Prancis'] as $nama) {
            $bahasa[$nama] = Bahasa::firstOrCreate(['nama_bahasa' => $nama]);
        }

        // Guide contoh (terverifikasi): [nama, email, username, paket, bahasa => tingkat, pengalaman]
        foreach ([
            ['Ayu Lestari', 'guide1@goguide.test', 'ayu.lestari',
                ['Ubud Alam dan Air Terjun', 'Sanur dan Canggu Pantai', 'Nusa Penida Day Trip'],
                ['Indonesia' => 'Fasih', 'Inggris' => 'Mahir'], 'Guide wisata alam dan budaya sejak 2019.'],
            ['Made Wirawan', 'guide2@goguide.test', 'made.wirawan',
                ['Sunrise Trekking Gunung Batur', 'Uluwatu Pura dan Pantai'],
                ['Indonesia' => 'Fasih', 'Inggris' => 'Menengah', 'Jepang' => 'Dasar'], 'Pemandu pendakian dan pura sejak 2017.'],
        ] as [$nama, $email, $username, $daftarPaket, $daftarBahasa, $pengalaman]) {
            $user = User::firstOrCreate(['email' => $email], [
                'name' => $nama,
                'username' => $username,
                'password' => Hash::make('password'),
                'role' => 'guide',
            ]);

            $guide = Guide::updateOrCreate(['id_user' => $user->id], [
                'nama_guide' => $nama,
                'email' => $email,
                'bio_singkat' => 'Guide lokal berpengalaman di Bali.',
                'pengalaman' => $pengalaman,
                'status_verifikasi' => 'Terverifikasi',
                'status' => 'Aktif',
                'tanggal_persetujuan' => now(),
            ]);

            $guide->paket()->syncWithoutDetaching(
                collect($daftarPaket)->map(fn ($n) => $paket[$n]->id_paket)->all()
            );

            foreach ($daftarBahasa as $namaBahasa => $tingkat) {
                $guide->bahasa()->syncWithoutDetaching([
                    $bahasa[$namaBahasa]->id_bahasa => ['tingkat_kemampuan' => $tingkat],
                ]);
            }

            // Tanggal aktif contoh: 20 hari ke depan, tiap hari keempat dikosongkan
            for ($i = 1; $i <= 20; $i++) {
                if ($i % 4 === 0) {
                    continue;
                }
                JadwalGuide::firstOrCreate([
                    'id_guide' => $guide->id_guide,
                    'tanggal_aktif' => now()->addDays($i)->toDateString(),
                ], ['jam_mulai' => '08:00:00', 'jam_selesai' => '17:00:00', 'status' => 'Tersedia']);
            }
        }

        // Satu pendaftaran guide yang menunggu verifikasi (untuk menguji halaman admin)
        Guide::firstOrCreate(['email' => 'calon.guide@goguide.test'], [
            'nama_guide' => 'Komang Dewi (calon guide)',
            'no_wa' => '081234567890',
            'bio_singkat' => 'Pemandu budaya dan pura di Bali.',
            'status_verifikasi' => 'Menunggu',
            'status' => 'Aktif',
        ]);
    }
}
