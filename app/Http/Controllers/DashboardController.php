<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ($user->role === 'guide') {
            return redirect()->route('guide.dashboard');
        }

        $wisatawan = $user->wisatawan;

        $semua = $wisatawan
            ? Booking::with(['paket', 'guide'])
                ->where('id_wisatawan', $wisatawan->id_wisatawan)
                ->orderByDesc('tanggal_transaksi')
                ->get()
            : collect();

        // Booking aktif: belum dibatalkan dan tanggal tour belum lewat
        $aktif = $semua->filter(fn ($b) => $b->status !== 'dibatalkan' && $b->tanggal_booking->gte(today()))->values();
        $riwayat = $semua->reject(fn ($b) => $aktif->contains('id_booking', $b->id_booking))->values();

        $ringkasan = [
            'total'        => $semua->count(),
            'belum_bayar'  => $semua->where('status', 'belum_bayar')->count(),
            'dikonfirmasi' => $semua->where('status', 'dikonfirmasi')->count(),
        ];

        return view('dashboard', compact('user', 'aktif', 'riwayat', 'ringkasan'));
    }
}
