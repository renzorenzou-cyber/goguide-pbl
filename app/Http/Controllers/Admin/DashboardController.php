<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Guide;
use App\Models\ObjekWisata;
use App\Models\PaketWisata;
use App\Models\Wisatawan;

class DashboardController extends Controller
{
    public function index()
    {
        // Semua angka dihitung dari data di database
        $stat = [
            'wisatawan'     => Wisatawan::count(),
            'guide'         => Guide::disetujui()->count(),
            'objek'         => ObjekWisata::count(),
            'paket'         => PaketWisata::count(),
            'booking'       => Booking::count(),
            'pemasukan'     => Booking::whereIn('status', ['sudah_bayar', 'dikonfirmasi', 'selesai'])->sum('total_bayar'),
            'perlu_approve' => Booking::where('status', 'sudah_bayar')->count(),
        ];

        $perStatus = Booking::selectRaw('status, count(*) as jumlah')->groupBy('status')->pluck('jumlah', 'status');

        $menunggu = Guide::where('status_verifikasi', 'Menunggu')->latest()->get();

        $bookingTerbaru = Booking::with(['wisatawan', 'paket', 'guide'])
            ->orderByDesc('tanggal_transaksi')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stat', 'perStatus', 'menunggu', 'bookingTerbaru'));
    }
}
