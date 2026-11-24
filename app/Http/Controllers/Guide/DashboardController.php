<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $guide = $user->guide;

        // Akun role guide, tetapi data guide belum terhubung
        if (! $guide) {
            return view('guide.dashboard', ['user' => $user, 'guide' => null]);
        }

        $paket = $guide->paket()->get();

        $booking = $guide->booking()->with(['paket', 'wisatawan'])->orderBy('tanggal_booking')->get();

        // Booking masuk: sudah dikonfirmasi admin dan tanggal tour belum lewat
        $masuk = $booking->filter(fn ($b) => $b->status === 'dikonfirmasi' && $b->tanggal_booking->gte(today()))->values();

        // Riwayat: dikonfirmasi yang sudah lewat, selesai, atau dibatalkan
        $riwayat = $booking
            ->filter(fn ($b) => in_array($b->status, ['dikonfirmasi', 'selesai', 'dibatalkan'], true)
                && ! $masuk->contains('id_booking', $b->id_booking))
            ->sortByDesc('tanggal_booking')
            ->values();

        // Tanggal yang sudah dipesan (selain dibatalkan)
        $tanggalTerpesan = $booking->where('status', '!=', 'dibatalkan')
            ->map(fn ($b) => $b->tanggal_booking->toDateString())
            ->all();

        $jadwal = $guide->jadwal()
            ->whereDate('tanggal_aktif', '>=', today())
            ->orderBy('tanggal_aktif')
            ->take(12)
            ->get();

        $aktifBulanIni = $guide->jadwal()
            ->whereYear('tanggal_aktif', now()->year)
            ->whereMonth('tanggal_aktif', now()->month)
            ->count();

                // Fee guide: perkiraan dari tour yang sudah dikonfirmasi, lalu fee dari tour selesai
        $selesai = $booking->where('status', 'selesai');
        $fee = [
            'perkiraan' => (int) $booking->where('status', 'dikonfirmasi')->sum('fee_guide'),
            'belum'     => (int) $selesai->where('status_bayar_guide', 'belum')->sum('fee_guide'),
            'sudah'     => (int) $selesai->where('status_bayar_guide', 'sudah')->sum('fee_guide'),
        ];

        return view('guide.dashboard', compact(
            'user', 'guide', 'paket', 'masuk', 'riwayat', 'jadwal', 'tanggalTerpesan', 'aktifBulanIni', 'fee'

        ));
    }
}
