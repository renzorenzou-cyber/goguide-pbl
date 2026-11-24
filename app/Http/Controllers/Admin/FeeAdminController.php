<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Guide;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class FeeAdminController extends Controller
{
    /** Rekap fee guide dari booking yang sudah selesai. */
    public function index(Request $request)
    {
        $tab = $request->query('status') === 'sudah' ? 'sudah' : 'belum';

        $selesai = fn () => Booking::where('status', 'selesai');

        $ringkas = [
            'belum_total' => (int) $selesai()->where('status_bayar_guide', 'belum')->sum('fee_guide'),
            'belum_n'     => $selesai()->where('status_bayar_guide', 'belum')->count(),
            'sudah_total' => (int) $selesai()->where('status_bayar_guide', 'sudah')->sum('fee_guide'),
            'sudah_n'     => $selesai()->where('status_bayar_guide', 'sudah')->count(),
        ];

        // Fee yang masih harus dibayar, dikelompokkan per guide
        $perGuide = $selesai()->where('status_bayar_guide', 'belum')
            ->selectRaw('id_guide, count(*) as jumlah, sum(fee_guide) as total_fee')
            ->groupBy('id_guide')
            ->with('guide')
            ->get()
            ->sortByDesc('total_fee')
            ->values();

        $filterGuide = $request->filled('guide') ? Guide::find($request->query('guide')) : null;

        $bookings = $selesai()->with(['guide', 'paket', 'wisatawan'])
            ->where('status_bayar_guide', $tab)
            ->when($filterGuide, fn ($q) => $q->where('id_guide', $filterGuide->id_guide))
            ->when($request->q, fn ($q, $kw) => $q->where('kode_booking', 'like', "%{$kw}%"))
            ->orderByDesc('tanggal_booking')
            ->paginate(10)
            ->withQueryString();

        return view('admin.fee.index', compact('tab', 'ringkas', 'perGuide', 'bookings', 'filterGuide'));
    }

    /** Tandai fee satu booking sudah dibayarkan ke guide. */
    public function bayar(Request $request, $id)
    {
        $booking = Booking::with('guide')->findOrFail($id);

        if ($booking->status !== 'selesai') {
            return back()->with('error', 'Fee hanya bisa dibayarkan setelah tour selesai.');
        }
        if ($booking->status_bayar_guide === 'sudah') {
            return back()->with('error', 'Fee booking ini sudah ditandai dibayar.');
        }

        $booking->update(['status_bayar_guide' => 'sudah', 'tanggal_bayar_guide' => now()]);

        $rp = 'Rp' . number_format($booking->fee_guide, 0, ',', '.');

        if ($booking->guide?->id_user) {
            Notifikasi::create([
                'id_user' => $booking->guide->id_user, 'id_booking' => $booking->id_booking,
                'jenis' => 'fee_dibayar', 'judul' => 'Fee tour dibayarkan',
                'isi' => "Fee {$rp} untuk booking {$booking->kode_booking} sudah dibayarkan admin.",
                'dibaca' => false,
            ]);
        }

        return back()->with('success', "Fee {$rp} untuk {$booking->kode_booking} ditandai sudah dibayar ke {$booking->guide?->nama_guide}.");
    }
}