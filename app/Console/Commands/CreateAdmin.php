<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\DashboardController as WisatawanDashboard;
use App\Http\Controllers\Guide\DashboardController as GuideDashboard;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaketController;
use App\Models\Booking;
use App\Models\Wisatawan;
use App\Models\Guide;
use App\Models\PaketWisata;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class CekSistem extends Command
{
    protected $signature = 'goguide:cek';

    protected $description = 'Memeriksa kesiapan database, rute, tampilan, dan dashboard tiap peran';

    private int $masalah = 0;

    public function handle(): int
    {
        $this->info('1. TABEL DAN KOLOM');
        $this->cekSkema();

        $this->newLine();
        $this->info('2. KODE, RUTE, DAN TAMPILAN');
        $this->cekKode();

        $this->newLine();
        $this->info('3. DATA');
        $this->cekData();

        $this->newLine();
        $this->info('4. UJI TAMPILAN (render halaman)');
        $this->cekRender();

        $this->newLine();
        if ($this->masalah === 0) {
            $this->info('SEMUA OK. Silakan coba login lewat browser.');
        } else {
            $this->error($this->masalah . ' masalah ditemukan. Kirim seluruh output ini ke asisten.');
        }

        return $this->masalah === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function ok(string $pesan): void
    {
        $this->line('  [OK]    ' . $pesan);
    }

    private function gagal(string $pesan): void
    {
        $this->masalah++;
        $this->line('  [GAGAL] ' . $pesan);
    }

    private function info2(string $pesan): void
    {
        $this->line('  [INFO]  ' . $pesan);
    }

    private function cekSkema(): void
    {
        $harus = [
            'users' => ['id', 'name', 'email', 'username', 'phone', 'role', 'wajib_ganti_password'],
            'wisatawan' => ['id_wisatawan', 'id_user', 'nama_wisatawan', 'email', 'telepon', 'tanggal_daftar', 'status'],
            'guide' => ['id_guide', 'id_user', 'nama_guide', 'email', 'pengalaman', 'status_verifikasi', 'status'],
            'jadwal_guide' => ['id_jadwal', 'id_guide', 'tanggal_aktif', 'jam_mulai', 'jam_selesai', 'status'],
            'tour_guide' => ['id_tourguide', 'id_guide', 'id_paket', 'status'],
            'kategori_wisata' => ['id_kategori', 'nama_kategori', 'deskripsi'],
            'area' => ['id_area', 'nama_area', 'kabupaten'],
            'objek_wisata' => ['id_objek', 'nama_objek', 'id_kategori', 'id_area', 'status'],
            'paket_wisata' => ['id_paket', 'nama_paket', 'harga_paket', 'fasilitas', 'status'],
            'detail_paket_wisata' => ['id_detail_paket', 'id_paket', 'id_objek', 'urutan'],
            'bahasa' => ['id_bahasa', 'nama_bahasa'],
            'detail_bahasa_guide' => ['id_detail_bahasa', 'id_guide', 'id_bahasa'],
            'booking' => ['id_booking', 'kode_booking', 'id_wisatawan', 'id_paket', 'id_guide', 'tanggal_booking',
                'jumlah_peserta', 'total_bayar', 'kode_va', 'metode_pembayaran', 'batas_bayar', 'status'],
            'notifikasi' => ['id_notifikasi', 'id_user', 'jenis', 'judul', 'isi'],
        ];

        foreach ($harus as $tabel => $kolom) {
            try {
                if (! Schema::hasTable($tabel)) {
                    $this->gagal("tabel {$tabel} tidak ada (jalankan php artisan migrate)");
                    continue;
                }
                $hilang = array_values(array_filter($kolom, fn ($k) => ! Schema::hasColumn($tabel, $k)));
                if ($hilang) {
                    $this->gagal("tabel {$tabel}: kolom hilang " . implode(', ', $hilang));
                } else {
                    $this->ok("tabel {$tabel}");
                }
            } catch (\Throwable $e) {
                $this->gagal("tabel {$tabel}: " . $e->getMessage());
            }
        }

        // Kolom lama yang seharusnya sudah tidak ada
        foreach ([['paket_wisata', 'aktif'], ['guide', 'status_pendaftaran']] as [$tabel, $kolom]) {
            if (Schema::hasTable($tabel) && Schema::hasColumn($tabel, $kolom)) {
                $this->gagal("kolom lama {$tabel}.{$kolom} masih ada (migration proposal belum dijalankan)");
            }
        }
    }

    private function cekKode(): void
    {
        foreach (['guide', 'wisatawan'] as $relasi) {
            method_exists(User::class, $relasi)
                ? $this->ok("User::{$relasi}() ada")
                : $this->gagal("User::{$relasi}() belum ada di app/Models/User.php");
        }

        foreach (['cekKetersediaan', 'tanggalTersedia', 'scopeDisetujui'] as $metode) {
            method_exists(Guide::class, $metode)
                ? $this->ok("Guide::{$metode}() ada")
                : $this->gagal("Guide::{$metode}() belum ada");
        }

        foreach (['home', 'paket.show', 'login', 'register', 'logout', 'dashboard', 'admin.dashboard', 'guide.dashboard', 'profile.edit'] as $nama) {
            Route::has($nama) ? $this->ok("rute {$nama}") : $this->gagal("rute {$nama} tidak ada (cek routes/web.php)");
        }

        foreach (['home', 'paket.show', 'auth.login', 'dashboard', 'admin.dashboard', 'guide.dashboard', 'partials.badge-status', 'layouts.site'] as $v) {
            view()->exists($v) ? $this->ok("view {$v}") : $this->gagal("view {$v} tidak ditemukan");
        }

        $login = @file_get_contents(resource_path('views/auth/login.blade.php')) ?: '';
        if (str_contains($login, 'id="login-email" type="email"')) {
            $this->gagal('form login masih type="email" (ubah jadi type="text" supaya username bisa dipakai)');
        } else {
            $this->ok('form login menerima email atau username');
        }
    }

    private function cekData(): void
    {
        try {
            foreach (['admin', 'guide', 'wisatawan'] as $peran) {
                $n = User::where('role', $peran)->count();
                $n > 0 ? $this->ok("akun {$peran}: {$n}") : $this->gagal("belum ada akun {$peran}" . ($peran === 'admin' ? ' (php artisan admin:create email "Nama")' : ''));
            }

            $tanpaProfil = User::where('role', 'wisatawan')
                ->whereNotIn('id', Wisatawan::pluck('id_user'))->count();
            $tanpaProfil === 0
                ? $this->ok('semua akun wisatawan punya baris di tabel wisatawan')
                : $this->gagal("{$tanpaProfil} akun wisatawan belum punya baris di tabel wisatawan");

            $guideTanpaAkun = Guide::disetujui()->whereNull('id_user')->count();
            $guideTanpaAkun === 0
                ? $this->ok('semua guide terverifikasi terhubung ke akun users')
                : $this->gagal("{$guideTanpaAkun} guide terverifikasi belum punya akun login");

            $this->info2('guide terverifikasi: ' . Guide::disetujui()->count()
                . ', menunggu: ' . Guide::where('status_verifikasi', 'Menunggu')->count());
            $this->info2('paket aktif: ' . PaketWisata::aktif()->count() . ', booking: ' . Booking::count());

            $tanpaUsername = User::where('role', 'guide')->whereNull('username')->count();
            if ($tanpaUsername > 0) {
                $this->info2("{$tanpaUsername} akun guide belum punya username (login tetap bisa pakai email)");
            }
        } catch (\Throwable $e) {
            $this->gagal('data: ' . $e->getMessage());
        }
    }

    private function cekRender(): void
    {
        if (! file_exists(public_path('hot')) && ! file_exists(public_path('build/manifest.json'))) {
            $this->info2('Vite belum jalan (npm run dev) sehingga uji tampilan dilewati. Jalankan npm run dev lalu ulangi perintah ini.');
            return;
        }

        $this->coba('halaman Home', function () {
            return app(HomeController::class)->index(Request::create('/', 'GET'))->render();
        });

        $paket = PaketWisata::aktif()->first();
        if ($paket) {
            $this->coba('halaman detail paket', function () use ($paket) {
                return app(PaketController::class)->show(Request::create('/paket/' . $paket->id_paket, 'GET'), $paket)->render();
            });
        } else {
            $this->gagal('belum ada paket aktif untuk diuji');
        }

        $peran = [
            'wisatawan' => [WisatawanDashboard::class, 'dashboard wisatawan'],
            'guide' => [GuideDashboard::class, 'dashboard guide'],
            'admin' => [AdminDashboard::class, 'dashboard admin'],
        ];

        foreach ($peran as $role => [$kelas, $label]) {
            $user = User::where('role', $role)->first();
            if (! $user) {
                $this->info2("{$label} dilewati (tidak ada akun {$role})");
                continue;
            }

            $this->coba("{$label} ({$user->email})", function () use ($user, $kelas) {
                Auth::guard('web')->setUser($user);
                $req = Request::create('/', 'GET');
                $req->setUserResolver(fn () => $user);

                $hasil = app($kelas)->index($req);
                if ($hasil instanceof \Illuminate\Contracts\View\View) {
                    return $hasil->render();
                }

                throw new \RuntimeException('hasil bukan tampilan (' . get_class($hasil) . ')');
            });
        }

        Auth::guard('web')->forgetUser();
    }

    private function coba(string $label, \Closure $aksi): void
    {
        try {
            $html = $aksi();
            strlen((string) $html) > 500
                ? $this->ok($label)
                : $this->gagal("{$label}: hasil render terlalu pendek");
        } catch (\Throwable $e) {
            $this->gagal("{$label}: " . class_basename($e) . ' - ' . $e->getMessage()
                . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')');
        }
    }
}
