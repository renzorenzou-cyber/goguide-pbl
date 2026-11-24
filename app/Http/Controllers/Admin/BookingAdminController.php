<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingAdminController extends Controller
{
    private const TAB = ['sudah_bayar', 'belum_bayar', 'dikonfirmasi', 'selesai', 'dibatalkan'];

    public function index(Request $request)
    {
        $tab = in_array($request->query('status'), self::TAB, true) ? $request->query('status') : 'sudah_bayar';

        $jumlah = Booking::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status');

        $bookings = Booking::with(['wisatawan', 'paket', 'guide'])
            ->where('status', $tab)
            ->when($request->q, fn ($q, $kw) => $q->where('kode_booking', 'like', "%{$kw}%"))
            ->orderByDesc('tanggal_transaksi')
            ->paginate(10)
            ->withQueryString();

        return view('admin.booking.index', compact('bookings', 'tab', 'jumlah'));
    }

    /** Pembayaran valid -> booking dikonfirmasi (proposal 6.2.2.3 / 6.2.2.4). */
    public function konfirmasi(Request $request, $id)
    {
        $booking = Booking::with(['wisatawan.user', 'paket', 'guide'])->findOrFail($id);

        if ($booking->status !== 'sudah_bayar') {
            return back()->with('error', 'Hanya booking berstatus Sudah Bayar yang bisa dikonfirmasi.');
        }

        $booking->update([
            'status'             => 'dikonfirmasi',
            'dikonfirmasi_oleh'  => $request->user()->id,
            'tanggal_konfirmasi' => now(),
        ]);

        $tgl = $booking->tanggal_booking->format('d M Y') . ' pukul ' . substr($booking->jam_mulai, 0, 5);
        $rp  = 'Rp' . number_format($booking->total_bayar, 0, ',', '.');

        // Wisatawan: bukti bayar + data guide
        if ($booking->wisatawan?->id_user) {
            $this->notif($booking->wisatawan->id_user, $booking, 'dikonfirmasi', 'Booking dikonfirmasi',
                "Booking {$booking->kode_booking} dikonfirmasi. Guide: {$booking->guide->nama_guide} ({$booking->guide->no_wa}). Tour {$tgl}.");
        }

        // Guide: tugas tour
        if ($booking->guide?->id_user) {
            $this->notif($booking->guide->id_user, $booking, 'dikonfirmasi', 'Tugas tour baru',
                "{$booking->paket->nama_paket} pada {$tgl}, {$booking->jumlah_peserta} peserta ({$booking->wisatawan->nama_wisatawan}).");
        }

        $terkirim = $this->kirimEmail($booking->wisatawan->email, "Booking {$booking->kode_booking} dikonfirmasi",
            "Halo {$booking->wisatawan->nama_wisatawan},\n\n"
            . "Pembayaran kamu sudah diverifikasi dan booking dikonfirmasi.\n\n"
            . "Kode booking : {$booking->kode_booking}\n"
            . "Paket        : {$booking->paket->nama_paket}\n"
            . "Tanggal tour : {$tgl}\n"
            . "Peserta      : {$booking->jumlah_peserta} orang\n"
            . "Total bayar  : {$rp}\n"
            . 'Dibayar      : ' . optional($booking->tanggal_bayar)->format('d M Y H:i') . " ({$booking->metode_pembayaran})\n\n"
            . "Guide kamu   : {$booking->guide->nama_guide}\n"
            . "WhatsApp     : {$booking->guide->no_wa}\n\n"
            . 'Detail: ' . route('bookings.show', $booking->id_booking) . "\n\nSalam,\nGo Guide");

        return back()->with('success', "Booking {$booking->kode_booking} dikonfirmasi."
            . ($terkirim ? ' Bukti bayar dan data guide dikirim ke email wisatawan.' : ' Email ke wisatawan gagal terkirim (lihat laravel.log).'));
    }

    /** Pembayaran tidak valid -> booking dibatalkan beserta alasan. */
    public function tolak(Request $request, $id)
    {
        $booking = Booking::with(['wisatawan', 'paket', 'guide'])->findOrFail($id);

        if (! in_array($booking->status, ['sudah_bayar', 'dikonfirmasi'], true)) {
            return back()->with('error', 'Booking ini tidak bisa ditolak.');
        }

        $data = $request->validate(
            ['alasan' => ['required', 'string', 'min:5', 'max:200']],
            ['alasan.required' => 'Alasan wajib diisi.', 'alasan.min' => 'Alasan minimal 5 karakter.']
        );

        $booking->update(['status' => 'dibatalkan', 'alasan_batal' => $data['alasan']]);

        $isi = "Booking {$booking->kode_booking} dibatalkan oleh admin. Alasan: {$data['alasan']}. "
             . 'Kamu dapat menjadwalkan ulang atau mendapat pengembalian dana penuh (diproses manual oleh admin).';

        if ($booking->wisatawan?->id_user) {
            $this->notif($booking->wisatawan->id_user, $booking, 'dibatalkan', 'Booking dibatalkan', $isi);
        }
        if ($booking->guide?->id_user) {
            $this->notif($booking->guide->id_user, $booking, 'dibatalkan', 'Booking dibatalkan admin',
                "Booking {$booking->kode_booking} pada " . $booking->tanggal_booking->format('d M Y') . " dibatalkan. Alasan: {$data['alasan']}");
        }

        $this->kirimEmail($booking->wisatawan->email, "Booking {$booking->kode_booking} dibatalkan",
            "Halo {$booking->wisatawan->nama_wisatawan},\n\n{$isi}\n\nSalam,\nGo Guide");

        return back()->with('success', "Booking {$booking->kode_booking} dibatalkan dan wisatawan diberi tahu. Proses refund manual bila sudah dibayar.");
    }

    /** Tour sudah dilaksanakan -> selesai. */
    public function selesai(Request $request, $id)
    {
        $booking = Booking::with(['wisatawan', 'guide'])->findOrFail($id);

        if ($booking->status !== 'dikonfirmasi') {
            return back()->with('error', 'Hanya booking Dikonfirmasi yang bisa ditandai selesai.');
        }
        if ($booking->tanggal_booking->isFuture()) {
            return back()->with('error', 'Tanggal tour belum tiba, belum bisa ditandai selesai.');
        }

        $booking->update(['status' => 'selesai']);

        if ($booking->wisatawan?->id_user) {
            $this->notif($booking->wisatawan->id_user, $booking, 'selesai', 'Tour selesai', "Tour {$booking->kode_booking} telah selesai. Terima kasih sudah memakai Go Guide.");
        }
        if ($booking->guide?->id_user) {
            $this->notif($booking->guide->id_user, $booking, 'selesai', 'Tour selesai', "Tour {$booking->kode_booking} ditandai selesai oleh admin.");
        }

        return back()->with('success', "Booking {$booking->kode_booking} ditandai selesai.");
    }

    private function notif(int $idUser, Booking $booking, string $jenis, string $judul, string $isi): void
    {
        Notifikasi::create([
            'id_user' => $idUser, 'id_booking' => $booking->id_booking,
            'jenis' => $jenis, 'judul' => $judul, 'isi' => $isi, 'dibaca' => false,
        ]);
    }

    private function kirimEmail(string $tujuan, string $subjek, string $isi): bool
    {
        try {
            Mail::raw($isi, fn ($m) => $m->to($tujuan)->subject($subjek));

            return true;
        } catch (\Throwable $e) {
            Log::warning('Email booking gagal dikirim: ' . $e->getMessage(), ['tujuan' => $tujuan]);

            return false;
        }
    }
}