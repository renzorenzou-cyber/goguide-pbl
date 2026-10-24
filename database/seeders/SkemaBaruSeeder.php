<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Guide;
use App\Models\JadwalGuide;
use App\Models\KategoriWisata;
use App\Models\ObjekWisata;
use App\Models\PaketWisata;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Data contoh untuk pengembangan. Harga, persentase fee, dan nama guide hanya contoh,
 * ganti dengan data asli sebelum web dipakai.
 */
class SkemaBaruSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori
        $kategori = [];
        foreach (['Gunung', 'Danau', 'Pantai', 'Air Terjun', 'Budaya', 'Persawahan'] as $nama) {
            $kategori[$nama] = KategoriWisata::firstOrCreate(['nama_kategori' => $nama]);
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
            ]);
        }

        // Paket wisata: [nama, harga per peserta, persen fee guide, durasi, [objek...]]
        $paket = [];
        foreach ([
            ['Sunrise Trekking Gunung Batur', 450000, 70, '8 jam', ['Gunung Batur', 'Danau Batur']],
            ['Ubud Alam dan Air Terjun', 350000, 70, '6 jam', ['Air Terjun Tegenungan', 'Tegalalang Rice Terrace']],
            ['Uluwatu Pura dan Pantai', 400000, 70, '5 jam', ['Pura Uluwatu']],
            ['Nusa Penida Day Trip', 650000, 70, '10 jam', ['Pantai Kelingking']],
            ['Sanur dan Canggu Pantai', 300000, 70, '5 jam', ['Pantai Sanur', 'Pantai Batu Bolong']],
        ] as [$nama, $harga, $fee, $durasi, $daftarObjek]) {
            $p = PaketWisata::firstOrCreate(['nama_paket' => $nama], [
                'harga_paket' => $harga,
                'persen_fee_guide' => $fee,
                'durasi' => $durasi,
                'deskripsi' => 'Paket ' . $nama . ' bersama guide lokal.',
                'aktif' => true,
            ]);
            foreach ($daftarObjek as $urutan => $namaObjek) {
                $p->objek()->syncWithoutDetaching([
                    $objek[$namaObjek]->id_objek => ['urutan' => $urutan + 1],
                ]);
            }
            $paket[$nama] = $p;
        }

        // Guide contoh (sudah disetujui): [nama, email, username, paket yang ditangani]
        foreach ([
            ['Ayu Lestari', 'guide1@goguide.test', 'ayu.lestari', ['Ubud Alam dan Air Terjun', 'Sanur dan Canggu Pantai', 'Nusa Penida Day Trip']],
            ['Made Wirawan', 'guide2@goguide.test', 'made.wirawan', ['Sunrise Trekking Gunung Batur', 'Uluwatu Pura dan Pantai']],
        ] as [$nama, $email, $username, $daftarPaket]) {
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
                'status_pendaftaran' => 'disetujui',
                'tanggal_persetujuan' => now(),
            ]);

            $guide->paket()->syncWithoutDetaching(
                collect($daftarPaket)->map(fn ($n) => $paket[$n]->id_paket)->all()
            );

            // Tanggal aktif contoh: 20 hari ke depan, tiap hari keempat dikosongkan
            for ($i = 1; $i <= 20; $i++) {
                if ($i % 4 === 0) {
                    continue;
                }
                JadwalGuide::firstOrCreate([
                    'id_guide' => $guide->id_guide,
                    'tanggal_aktif' => now()->addDays($i)->toDateString(),
                ]);
            }
        }

        // Satu pendaftaran guide yang menunggu persetujuan (untuk menguji halaman admin)
        Guide::firstOrCreate(['email' => 'calon.guide@goguide.test'], [
            'nama_guide' => 'Komang Dewi (calon guide)',
            'no_wa' => '081234567890',
            'bio_singkat' => 'Pemandu budaya dan pura di Bali.',
            'status_pendaftaran' => 'menunggu',
        ]);
    }
}
