<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guide\DashboardController as GuideDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PaketAdminController;
use App\Http\Controllers\Admin\AreaAdminController;
use App\Http\Controllers\Admin\KategoriAdminController;
use App\Http\Controllers\Admin\ObjekAdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\GuideVerifikasiController;
use App\Http\Controllers\Guide\RegistrasiController as GuideRegistrasiController;
use App\Http\Controllers\Guide\PaketController as GuidePaketController;
use App\Http\Controllers\Guide\JadwalController as GuideJadwalController;
use App\Http\Controllers\Admin\BookingAdminController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Admin\FeeAdminController;
use App\Http\Controllers\Admin\BbmAdminController;
use App\Http\Controllers\UlasanController;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/paket/{paket}', [PaketController::class, 'show'])->name('paket.show');
Route::get('/paket', [PaketController::class, 'index'])->name('paket.index');
Route::get('/guides',          [GuideController::class, 'index'])->name('guides.index');
Route::get('/guides/{guide}',  [GuideController::class, 'show'])->name('guides.show');
// Nama 'guide.daftar' membuat tombol "Daftar sebagai Guide" di beranda otomatis aktif.
Route::get('/daftar-guide',  [GuideRegistrasiController::class, 'create'])->name('guide.daftar');
Route::post('/daftar-guide', [GuideRegistrasiController::class, 'store'])->middleware('throttle:6,10')->name('guide.daftar.store');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // --- Notifikasi (semua peran) ---
    Route::get('/notifikasi',             [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');
    Route::post('/notifikasi/{id}/baca',  [NotifikasiController::class, 'baca'])->name('notifikasi.baca');

    Route::get('/booking/create',              [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking',                    [BookingController::class, 'store'])->name('booking.store');
Route::get('/bookings',                    [BookingController::class, 'index'])->name('bookings.index');
Route::get('/bookings/{booking}',          [BookingController::class, 'show'])->name('bookings.show');
Route::post('/bookings/{booking}/bayar',   [BookingController::class, 'simulasiBayar'])->name('bookings.simulate');
Route::post('/bookings/{booking}/batal',   [BookingController::class, 'cancel'])->name('bookings.cancel');
Route::get('/ulasan',                    [UlasanController::class, 'index'])->name('ulasan.index');
Route::get('/bookings/{booking}/ulasan', [UlasanController::class, 'create'])->name('ulasan.create');
Route::post('/bookings/{booking}/ulasan',  [UlasanController::class, 'store'])->name('bookings.ulasan');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('paket',               [PaketAdminController::class, 'index'])->name('paket.index');
    Route::get('paket/create',        [PaketAdminController::class, 'create'])->name('paket.create');
    Route::post('paket',              [PaketAdminController::class, 'store'])->name('paket.store');
    Route::get('paket/{id}/edit',     [PaketAdminController::class, 'edit'])->name('paket.edit');
    Route::put('paket/{id}',          [PaketAdminController::class, 'update'])->name('paket.update');
    Route::patch('paket/{id}/status', [PaketAdminController::class, 'toggleStatus'])->name('paket.toggle');
    Route::delete('paket/{id}',       [PaketAdminController::class, 'destroy'])->name('paket.destroy');
    Route::get('guide',                  [GuideVerifikasiController::class, 'index'])->name('guide.index');
Route::get('guide/{id}',             [GuideVerifikasiController::class, 'show'])->name('guide.show');
Route::get('guide/{id}/bio',         [GuideVerifikasiController::class, 'bio'])->name('guide.bio');
Route::get('guide/{id}/sertifikat', [GuideVerifikasiController::class, 'sertifikat'])->name('guide.sertifikat');
Route::post('guide/{id}/setujui',    [GuideVerifikasiController::class, 'setujui'])->name('guide.approve');
Route::post('guide/{id}/tolak',      [GuideVerifikasiController::class, 'tolak'])->name('guide.reject');

    // --- Booking: verifikasi pembayaran dan konfirmasi ---
    Route::get('booking',                  [BookingAdminController::class, 'index'])->name('booking.index');
    Route::post('booking/{id}/konfirmasi', [BookingAdminController::class, 'konfirmasi'])->name('booking.confirm');
    Route::post('booking/{id}/tolak',      [BookingAdminController::class, 'tolak'])->name('booking.reject');
    Route::post('booking/{id}/selesai',    [BookingAdminController::class, 'selesai'])->name('booking.finish');

        // --- Fee guide ---
    Route::get('fee',             [FeeAdminController::class, 'index'])->name('fee.index');
    Route::post('fee/{id}/bayar', [FeeAdminController::class, 'bayar'])->name('fee.pay');

        // --- Catatan BBM ---
    Route::get('bbm',         [BbmAdminController::class, 'index'])->name('bbm.index');
    Route::post('bbm',        [BbmAdminController::class, 'store'])->name('bbm.store');
    Route::delete('bbm/{id}', [BbmAdminController::class, 'destroy'])->name('bbm.destroy');

    // --- Objek wisata ---
Route::get('objek',               [ObjekAdminController::class, 'index'])->name('objek.index');
Route::get('objek/create',        [ObjekAdminController::class, 'create'])->name('objek.create');
Route::post('objek',              [ObjekAdminController::class, 'store'])->name('objek.store');
Route::get('objek/{id}/edit',     [ObjekAdminController::class, 'edit'])->name('objek.edit');
Route::put('objek/{id}',          [ObjekAdminController::class, 'update'])->name('objek.update');
Route::patch('objek/{id}/status', [ObjekAdminController::class, 'toggleStatus'])->name('objek.toggle');
Route::delete('objek/{id}',       [ObjekAdminController::class, 'destroy'])->name('objek.destroy');

// --- Area ---
Route::get('area',         [AreaAdminController::class, 'index'])->name('area.index');
Route::post('area',        [AreaAdminController::class, 'store'])->name('area.store');
Route::put('area/{id}',    [AreaAdminController::class, 'update'])->name('area.update');
Route::delete('area/{id}', [AreaAdminController::class, 'destroy'])->name('area.destroy');

// --- Kategori ---
Route::get('kategori',         [KategoriAdminController::class, 'index'])->name('kategori.index');
Route::post('kategori',        [KategoriAdminController::class, 'store'])->name('kategori.store');
Route::put('kategori/{id}',    [KategoriAdminController::class, 'update'])->name('kategori.update');
Route::delete('kategori/{id}', [KategoriAdminController::class, 'destroy'])->name('kategori.destroy');
});

Route::middleware(['auth', 'guide', 'ganti.password'])->prefix('guide')->name('guide.')->group(function () {
    Route::get('/', [GuideDashboardController::class, 'index'])->name('dashboard');

    Route::get('paket', [GuidePaketController::class, 'index'])->name('paket');
    Route::put('paket', [GuidePaketController::class, 'update'])->name('paket.update');

    Route::get('jadwal',         [GuideJadwalController::class, 'index'])->name('jadwal');
    Route::post('jadwal',        [GuideJadwalController::class, 'store'])->name('jadwal.store');
    Route::delete('jadwal/{id}', [GuideJadwalController::class, 'destroy'])->name('jadwal.destroy');
});


require __DIR__.'/auth.php';
