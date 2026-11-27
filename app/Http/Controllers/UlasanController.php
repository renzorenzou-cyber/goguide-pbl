<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Guide;
use App\Models\Notifikasi;
use App\Models\Ulasan;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Wisatawan memberi rating dan ulasan untuk guide setelah tour selesai.
 * Aturan: hanya pemilik booking, hanya status selesai, sekali per booking, tidak bisa diedit.
 */
class UlasanController extends Controller
{
    /** Halaman "Ulasan Saya": tour yang sudah selesai, dipisah antara yang menunggu rating dan yang sudah dirating. */
    public function index(Request $request)
    {
        abort_unless($request->user()->role === 'wisatawan', 403, 'Halaman ini khusus akun wisatawan.');

        $wisatawan = $request->user()->wisatawan;

        $selesai = $wisatawan
            ? Booking::with(['paket', 'guide', 'ulasan'])
                ->where('id_wisatawan', $wisatawan->id_wisatawan)
                ->where('status', 'selesai')
                ->orderByDesc('tanggal_booking')
                ->get()
            : collect();

        return view('ulasan.index', [
            'menunggu' => $selesai->filter(fn ($b) => ! $b->ulasan)->values(),
            'sudah'    => $selesai->filter(fn ($b) => $b->ulasan)->values(),
        ]);
    }

    /** Halaman form rating untuk satu booking yang sudah selesai. */
    public function create(Request $request, Booking $booking)
    {
        $this->pastikanPemilik($request, $booking);

        if ($booking->status !== 'selesai') {
            return redirect()->route('ulasan.index')
                ->with('error', 'Rating hanya bisa diberikan setelah tour selesai.');
        }

        // Sudah diulas: kembali ke daftar (pesan flash dari store ikut dibawa)
        if ($booking->ulasan()->exists()) {
            $request->session()->reflash();

            return redirect()->route('ulasan.index');
        }

        $booking->load(['paket', 'guide']);

        return view('ulasan.create', compact('booking'));
    }

    public function store(Request $request, Booking $booking)
    {
        $this->pastikanPemilik($request, $booking);
        $wisatawan = $request->user()->wisatawan;

        if ($booking->status !== 'selesai') {
            return back()->with('error', 'Rating hanya bisa diberikan setelah tour selesai.');
        }
        if ($booking->ulasan()->exists()) {
            return back()->with('error', 'Kamu sudah memberi ulasan untuk booking ini.');
        }

        $data = $request->validate([
            'rating'   => ['required', 'integer', 'between:1,5'],
            'komentar' => ['nullable', 'string', 'max:500'],
        ], [
            'rating.required' => 'Pilih jumlah bintang dulu.',
            'rating.between'  => 'Rating harus 1 sampai 5 bintang.',
            'komentar.max'    => 'Komentar maksimal 500 karakter.',
        ]);

        try {
            DB::transaction(function () use ($booking, $wisatawan, $data) {
                Ulasan::create([
                    'id_booking'   => $booking->id_booking,
                    'id_wisatawan' => $wisatawan->id_wisatawan,
                    'id_guide'     => $booking->id_guide,
                    'rating'       => (int) $data['rating'],
                    'komentar'     => trim($data['komentar'] ?? '') ?: null,
                ]);

                // Rating guide = rata-rata semua ulasannya (dibulatkan 2 desimal sesuai kolom DECIMAL(3,2))
                $rata = Ulasan::where('id_guide', $booking->id_guide)->avg('rating');
                Guide::where('id_guide', $booking->id_guide)->update(['rating' => round((float) $rata, 2)]);
            });
        } catch (UniqueConstraintViolationException $e) {
            // Dobel klik / dua tab: ulasan pertama sudah masuk
            return back()->with('error', 'Kamu sudah memberi ulasan untuk booking ini.');
        }

        $booking->load(['guide', 'paket']);

        if ($booking->guide?->id_user) {
            $nama = Str::before($wisatawan->nama_wisatawan ?: 'Wisatawan', ' ');
            Notifikasi::create([
                'id_user'    => $booking->guide->id_user,
                'id_booking' => $booking->id_booking,
                'jenis'      => 'ulasan_baru',
                'judul'      => 'Ulasan baru',
                'isi'        => "{$nama} memberi {$data['rating']} bintang untuk tour {$booking->paket?->nama_paket} ({$booking->kode_booking}).",
                'dibaca'     => false,
            ]);
        }

        $pesan = 'Terima kasih! Rating dan ulasanmu sudah tersimpan.';

        // Dikirim dari halaman "Ulasan Saya" -> kembali ke daftar; dari detail booking -> kembali ke detail booking
        return $request->input('dari') === 'ulasan'
            ? redirect()->route('ulasan.index')->with('success', $pesan)
            : back()->with('success', $pesan);
    }

    private function pastikanPemilik(Request $request, Booking $booking): void
    {
        $wisatawan = $request->user()->wisatawan;

        abort_unless($wisatawan && (int) $booking->id_wisatawan === (int) $wisatawan->id_wisatawan, 403);
    }
}