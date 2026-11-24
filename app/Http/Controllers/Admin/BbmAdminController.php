<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBakar;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BbmAdminController extends Controller
{
    /** Rekap catatan BBM per bulan + form tambah catatan. */
    public function index(Request $request)
    {
        $bulan = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string) $request->query('bulan'))
            ? $request->query('bulan')
            : now()->format('Y-m');

        $awal  = Carbon::createFromFormat('Y-m-d', $bulan . '-01')->startOfMonth()->toDateString();
        $akhir = Carbon::createFromFormat('Y-m-d', $bulan . '-01')->endOfMonth()->toDateString();

        $bulanIni = fn () => BahanBakar::whereBetween('tanggal_isi', [$awal, $akhir]);

        $ringkas = [
            'total_biaya' => (int) $bulanIni()->sum('total_biaya'),
            'total_liter' => (float) $bulanIni()->sum('jumlah_liter'),
            'jumlah'      => $bulanIni()->count(),
        ];

        $catatan = $bulanIni()->with(['booking.paket', 'booking.guide'])
            ->when($request->q, fn ($q, $kw) => $q->whereHas('booking', fn ($b) => $b->where('kode_booking', 'like', "%{$kw}%")))
            ->orderByDesc('tanggal_isi')
            ->orderByDesc('id_bahan_bakar')
            ->paginate(10)
            ->withQueryString();

        // BBM dicatat untuk booking yang sudah dikonfirmasi atau selesai
        $bookings = Booking::with(['paket', 'guide'])
            ->whereIn('status', ['dikonfirmasi', 'selesai'])
            ->orderByDesc('tanggal_booking')
            ->limit(100)
            ->get();

        return view('admin.bbm.index', compact('bulan', 'ringkas', 'catatan', 'bookings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_booking'        => ['required', 'integer', 'exists:booking,id_booking'],
            'jenis_bahan_bakar' => ['required', 'string', 'max:50'],
            'jumlah_liter'      => ['required', 'numeric', 'min:0.1', 'max:9999.99'],
            'harga_per_liter'   => ['required', 'integer', 'min:1', 'max:1000000'],
            'tanggal_isi'       => ['required', 'date_format:Y-m-d'],
            'catatan'           => ['nullable', 'string', 'max:255'],
        ], [
            'id_booking.required'        => 'Pilih booking yang dicatat BBM-nya.',
            'jenis_bahan_bakar.required' => 'Jenis bahan bakar wajib diisi.',
            'jumlah_liter.required'      => 'Jumlah liter wajib diisi.',
            'jumlah_liter.min'           => 'Jumlah liter minimal 0,1.',
            'harga_per_liter.required'   => 'Harga per liter wajib diisi.',
            'harga_per_liter.integer'    => 'Harga per liter berupa angka bulat, misalnya 10000.',
            'tanggal_isi.required'       => 'Tanggal pengisian wajib diisi.',
        ]);

        $booking = Booking::findOrFail($data['id_booking']);
        if (! in_array($booking->status, ['dikonfirmasi', 'selesai'], true)) {
            return back()->withInput()->with('error', 'BBM hanya bisa dicatat untuk booking yang sudah dikonfirmasi atau selesai.');
        }

        // Total dihitung di server. Tidak mengubah total bayar wisatawan.
        $data['total_biaya'] = (int) round((float) $data['jumlah_liter'] * (int) $data['harga_per_liter']);

        BahanBakar::create($data);

        return redirect()
            ->route('admin.bbm.index', ['bulan' => substr($data['tanggal_isi'], 0, 7)])
            ->with('success', "Catatan BBM untuk {$booking->kode_booking} disimpan.");
    }

    public function destroy($id)
    {
        $bbm = BahanBakar::findOrFail($id);
        $bbm->delete();

        return back()->with('success', 'Catatan BBM dihapus.');
    }
}