<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Guide;
use App\Models\Notifikasi;
use App\Models\PaketWisata;
use App\Models\User;
use App\Models\Wisatawan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    /* ===================== Daftar booking ===================== */

    public function index(Request $request)
    {
        $this->batalkanKedaluwarsa();

        $wisatawan = $this->wisatawanDari($request->user());

        $bookings = Booking::with(['paket', 'guide'])
            ->where('id_wisatawan', $wisatawan->id_wisatawan)
            ->orderByDesc('tanggal_transaksi')
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    /* ===================== Form booking ===================== */

    public function create(Request $request)
    {
        $this->hanyaWisatawan($request);
        $this->batalkanKedaluwarsa();

        $q = $request->validate([
            'paket'   => ['required', 'integer'],
            'guide'   => ['required', 'integer'],
            'tanggal' => ['required', 'date_format:Y-m-d'],
        ]);

        $paket = PaketWisata::aktif()->findOrFail($q['paket']);
        $guide = $this->guideUntukPaket($paket, (int) $q['guide']);

        // Bila guide / tanggal tidak lagi tersedia, kembali ke halaman paket
        [$tersedia] = $guide->cekKetersediaan($q['tanggal']);
        if (! $tersedia) {
            return redirect()->route('paket.show', ['paket' => $paket->id_paket, 'guide' => $guide->id_guide, 'tanggal' => $q['tanggal']]);
        }

        return view('bookings.create', [
    'paket'     => $paket,
    'guide'     => $guide,
    'tanggal'   => Carbon::createFromFormat('Y-m-d', $q['tanggal'])->startOfDay(),
    'durasiJam' => $this->durasiJam($paket),
    'telepon'   => $this->wisatawanDari($request->user())->telepon ?: $request->user()->phone,
]);
    }

    /* ===================== Simpan booking ===================== */

    public function store(Request $request)
    {
        $this->hanyaWisatawan($request);
        // Rapikan nomor HP: buang spasi, strip, kurung
        $request->merge([
        'telepon' => preg_replace('/[\s\-().]/', '', (string) $request->input('telepon')),
]);
        $data = $request->validate([
            'paket'          => ['required', 'integer'],
            'guide'          => ['required', 'integer'],
            'tanggal'        => ['required', 'date_format:Y-m-d'],
            'jam_mulai'      => ['required', 'date_format:H:i'],
            'jumlah_peserta' => ['required', 'integer', 'min:1', 'max:' . config('goguide.max_peserta')],
            'telepon'        => ['required', 'string', 'regex:/^(\+62|62|0)8[0-9]{8,11}$/'],
            'catatan'        => ['nullable', 'string', 'max:500'],
            'setuju_syarat'  => ['accepted'],
        ], [
            'setuju_syarat.accepted'  => 'Kamu harus menyetujui Syarat dan Ketentuan sebelum kode VA diterbitkan.',
            'jam_mulai.required'      => 'Jam mulai tour wajib diisi.',
            'jumlah_peserta.required' => 'Jumlah peserta wajib diisi.',
            'jumlah_peserta.max'      => 'Jumlah peserta maksimal ' . config('goguide.max_peserta') . ' orang.',
            'telepon.required' => 'Nomor HP wajib diisi supaya guide bisa menghubungi kamu.',
            'telepon.regex'    => 'Nomor HP tidak valid. Contoh: 081234567890 atau +6281234567890.',
        ]);

        $paket = PaketWisata::aktif()->findOrFail($data['paket']);
        $durasiMenit = (int) round($this->durasiJam($paket) * 60);
        $wisatawan = $this->wisatawanDari($request->user());

        try {
            $booking = DB::transaction(function () use ($data, $paket, $durasiMenit, $wisatawan) {
                // Kunci baris guide supaya dua wisatawan tidak memesan guide yang sama bersamaan
                $guide = Guide::whereKey($data['guide'])->lockForUpdate()->firstOrFail();
                $this->guideUntukPaket($paket, $guide->id_guide);

                $this->batalkanKedaluwarsa();

                [$tersedia, $pesan] = $guide->cekKetersediaan($data['tanggal']);
                if (! $tersedia) {
                    throw ValidationException::withMessages(['tanggal' => $pesan]);
                }

                // Jam mulai + durasi paket harus berada dalam jam layanan guide (proposal 6.3.3 poin 5)
                $jadwal = $guide->jadwal()->whereDate('tanggal_aktif', $data['tanggal'])->where('status', 'Tersedia')->first();
                $mulai   = $this->menit($data['jam_mulai']);
                $selesai = $mulai + $durasiMenit;

                if ($jadwal && ($mulai < $this->menit($jadwal->jam_mulai) || $selesai > $this->menit($jadwal->jam_selesai))) {
                    throw ValidationException::withMessages(['jam_mulai' =>
                        'Jam layanan guide pada tanggal ini ' . substr($jadwal->jam_mulai, 0, 5) . ' - ' . substr($jadwal->jam_selesai, 0, 5)
                        . ', sedangkan tour kamu ' . $data['jam_mulai'] . ' - ' . $this->jamDariMenit($selesai) . '. Ubah jam mulai.']);
                }

                // Harga dihitung di server, bukan dari browser
                $harga = (int) $paket->harga_paket;
                $total = $harga * (int) $data['jumlah_peserta'];

                $booking = Booking::create([
                    'kode_booking'       => $this->kodeBooking(),
                    'id_wisatawan'       => $wisatawan->id_wisatawan,
                    'id_paket'           => $paket->id_paket,
                    'id_guide'           => $guide->id_guide,
                    'tanggal_transaksi'  => now(),
                    'tanggal_booking'    => $data['tanggal'],
                    'jam_mulai'          => $data['jam_mulai'],
                    'jumlah_peserta'     => $data['jumlah_peserta'],
                    'catatan'            => $data['catatan'] ?? null,
                    'harga_satuan'       => $harga,
                    'total_bayar'        => $total,
                    'fee_guide'          => intdiv($total * (int) $paket->persen_fee_guide, 100),
                    'batas_bayar'        => now()->addHours((int) config('goguide.batas_bayar_jam')),
                    'status'             => 'belum_bayar',
                    'status_bayar_guide' => 'belum',
                ]);

                // Kode VA unik berdasarkan id booking
                $booking->update(['kode_va' => '88081234' . str_pad((string) $booking->id_booking, 5, '0', STR_PAD_LEFT)]);

                return $booking;
            });
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }
        // Simpan nomor HP supaya booking berikutnya tidak perlu mengisi lagi
        $wisatawan->update(['telepon' => $data['telepon']]);
        $request->user()->update(['phone' => $data['telepon']]);
        $booking->load(['paket', 'guide']);
        $rp = 'Rp' . number_format($booking->total_bayar, 0, ',', '.');

        $this->notif($request->user()->id, $booking, 'booking_baru', 'Booking dibuat',
            "Booking {$booking->kode_booking} dibuat. Bayar {$rp} ke kode VA {$booking->kode_va} sebelum " . $booking->batas_bayar->format('d M Y H:i') . '.');

        $this->notifAdmin($booking, 'booking_baru', 'Booking baru belum dibayar',
            "{$request->user()->name} membuat booking {$booking->kode_booking} ({$booking->paket->nama_paket}), menunggu pembayaran.");

        $this->kirimEmail($request->user()->email, 'Booking Go Guide ' . $booking->kode_booking,
            "Halo {$request->user()->name},\n\n"
            . "Booking kamu berhasil dibuat.\n\n"
            . "Kode booking : {$booking->kode_booking}\n"
            . "Paket        : {$booking->paket->nama_paket}\n"
            . "Guide        : {$booking->guide->nama_guide}\n"
            . 'Tanggal tour : ' . $booking->tanggal_booking->format('d M Y') . ' pukul ' . substr($booking->jam_mulai, 0, 5) . "\n"
            . "Total bayar  : {$rp}\n"
            . "Kode VA      : {$booking->kode_va}\n"
            . 'Batas bayar  : ' . $booking->batas_bayar->format('d M Y H:i') . "\n\n"
            . 'Lihat detail: ' . route('bookings.show', $booking->id_booking) . "\n\nSalam,\nGo Guide");

        return redirect()->route('bookings.show', $booking->id_booking)
            ->with('success', 'Booking dibuat. Selesaikan pembayaran sebelum batas waktu.');
    }

    /* ===================== Detail booking ===================== */

    public function show(Request $request, Booking $booking)
    {
        $this->batalkanKedaluwarsa();
        $booking->refresh();
        $this->pastikanPemilik($request, $booking);

        $booking->load(['paket', 'guide']);

        $durasiMenit = $booking->paket ? (int) round($this->durasiJam($booking->paket) * 60) : 0;
        $estimasiSelesai = $this->jamDariMenit($this->menit($booking->jam_mulai) + $durasiMenit);

        $bisaBatal = in_array($booking->status, ['belum_bayar', 'sudah_bayar', 'dikonfirmasi'], true)
            && $booking->tanggal_booking->gte(today());

        return view('bookings.show', [
            'booking'         => $booking,
            'estimasiSelesai' => $estimasiSelesai,
            'bisaBatal'       => $bisaBatal,
            'persenRefund'    => $this->persenRefund($booking),
        ]);
    }

    /* ===================== Simulasi pembayaran ===================== */

    public function simulasiBayar(Request $request, Booking $booking)
    {
        $this->pastikanPemilik($request, $booking);

        if ($booking->status !== 'belum_bayar') {
            return back()->with('error', 'Booking ini tidak sedang menunggu pembayaran.');
        }
        if ($booking->batas_bayar && $booking->batas_bayar->isPast()) {
            $this->batalkanKedaluwarsa();
            return back()->with('error', 'Batas waktu pembayaran sudah lewat, booking dibatalkan.');
        }

        $booking->update([
            'status'               => 'sudah_bayar',
            'tanggal_bayar'        => now(),
            'metode_pembayaran'    => 'Virtual Account (simulasi)',
            'id_transaksi_gateway' => 'SIM-' . $booking->kode_booking,
        ]);

        $rp = 'Rp' . number_format($booking->total_bayar, 0, ',', '.');

        $this->notif($request->user()->id, $booking, 'pembayaran_diterima', 'Pembayaran tercatat',
            "Pembayaran {$rp} untuk {$booking->kode_booking} tercatat. Menunggu verifikasi admin.");
        $this->notifAdmin($booking, 'pembayaran_diterima', 'Pembayaran perlu diverifikasi',
            "Booking {$booking->kode_booking} sudah dibayar ({$rp}). Silakan verifikasi.");

        return redirect()->route('bookings.show', $booking->id_booking)
            ->with('success', 'Pembayaran tercatat. Admin akan memverifikasinya.');
    }

    /* ===================== Pembatalan ===================== */

    public function cancel(Request $request, Booking $booking)
    {
        $this->pastikanPemilik($request, $booking);

        $data = $request->validate(['alasan' => ['nullable', 'string', 'max:120']]);

        if (! in_array($booking->status, ['belum_bayar', 'sudah_bayar', 'dikonfirmasi'], true) || $booking->tanggal_booking->lt(today())) {
            return back()->with('error', 'Booking ini tidak bisa dibatalkan.');
        }

        $persen = $this->persenRefund($booking);   // dihitung sebelum status berubah
        $alasan = trim($data['alasan'] ?? '') ?: 'Dibatalkan oleh wisatawan';

        $booking->update(['status' => 'dibatalkan', 'alasan_batal' => $alasan]);
        $booking->load(['paket', 'guide.user']);

        $info = $persen === null ? 'Booking belum dibayar, tanpa biaya.'
              : ($persen > 0 ? "Pengembalian dana {$persen}% diproses manual oleh admin."
                             : 'Sesuai S&K, tidak ada pengembalian dana.');

        $this->notif($request->user()->id, $booking, 'dibatalkan', 'Booking dibatalkan',
            "Booking {$booking->kode_booking} dibatalkan. {$info}");

        if ($booking->guide?->id_user) {
            $this->notif($booking->guide->id_user, $booking, 'dibatalkan', 'Booking dibatalkan wisatawan',
                "Booking {$booking->kode_booking} pada " . $booking->tanggal_booking->format('d M Y') . " dibatalkan. Alasan: {$alasan}");
        }
        $this->notifAdmin($booking, 'dibatalkan', 'Booking dibatalkan wisatawan',
            "{$booking->kode_booking} dibatalkan. {$info}" . ($persen ? ' Perlu diproses refund.' : ''));

        return redirect()->route('bookings.show', $booking->id_booking)
            ->with('success', 'Booking dibatalkan. ' . $info);
    }

    /* ===================== Pembantu ===================== */

    /** Baris wisatawan dibuat otomatis saat pertama memesan (register hanya membuat baris di users). */
    private function wisatawanDari(User $user): Wisatawan
    {
        return Wisatawan::firstOrCreate(
            ['id_user' => $user->id],
            [
                'nama_wisatawan' => $user->name,
                'email'          => $user->email,
                'telepon'        => $user->phone,
                'tanggal_daftar' => now(),
                'status'         => 'Aktif',
            ]
        );
    }

    private function hanyaWisatawan(Request $request): void
    {
        abort_if(in_array($request->user()->role, ['admin', 'guide'], true), 403, 'Booking hanya untuk akun wisatawan.');
    }

    private function pastikanPemilik(Request $request, Booking $booking): void
    {
        $wisatawan = $this->wisatawanDari($request->user());
        abort_unless((int) $booking->id_wisatawan === (int) $wisatawan->id_wisatawan, 403);
    }

    /** Guide harus terverifikasi, aktif, dan menangani paket tersebut. */
    private function guideUntukPaket(PaketWisata $paket, int $idGuide): Guide
    {
        $guide = $paket->guide()->disetujui()->where('guide.id_guide', $idGuide)->first();
        abort_unless($guide, 404, 'Guide tidak menangani paket ini.');

        return $guide;
    }

    /** "8 jam" -> 8, "5" -> 5. Bila tidak terbaca dipakai 8 jam. */
    private function durasiJam(PaketWisata $paket): float
    {
        return preg_match('/\d+(?:[.,]\d+)?/', (string) $paket->durasi, $m)
            ? max(0.5, (float) str_replace(',', '.', $m[0]))
            : 8.0;
    }

    private function menit(string $jam): int
    {
        [$h, $m] = array_map('intval', explode(':', substr($jam, 0, 5)));

        return $h * 60 + $m;
    }

    private function jamDariMenit(int $menit): string
    {
        return sprintf('%02d:%02d', intdiv($menit, 60) % 24, $menit % 60);
    }

    /** null bila belum dibayar (tanpa biaya); selain itu persen refund sesuai config/goguide.php. */
    private function persenRefund(Booking $booking): ?int
    {
        if ($booking->status === 'belum_bayar') {
            return null;
        }

        $hari = today()->diffInDays($booking->tanggal_booking->copy()->startOfDay(), false);

        foreach (config('goguide.refund') as $aturan) {
            if ($hari >= $aturan['min_hari']) {
                return (int) $aturan['persen'];
            }
        }

        return 0;
    }

    private function kodeBooking(): string
    {
        do {
            $kode = 'GGG-' . strtoupper(Str::random(8));
        } while (Booking::where('kode_booking', $kode)->exists());

        return $kode;
    }

    /** Sama dengan command bookings:cancel-expired, dijalankan juga di sini supaya tidak bergantung pada scheduler. */
    private function batalkanKedaluwarsa(): void
    {
        Booking::where('status', 'belum_bayar')
            ->whereNotNull('batas_bayar')
            ->where('batas_bayar', '<', now())
            ->update(['status' => 'dibatalkan', 'alasan_batal' => 'Batas waktu pembayaran terlewati']);
    }

    private function notif(int $idUser, Booking $booking, string $jenis, string $judul, string $isi): void
    {
        Notifikasi::create([
            'id_user' => $idUser, 'id_booking' => $booking->id_booking,
            'jenis' => $jenis, 'judul' => $judul, 'isi' => $isi, 'dibaca' => false,
        ]);
    }

    private function notifAdmin(Booking $booking, string $jenis, string $judul, string $isi): void
    {
        User::where('role', 'admin')->pluck('id')->each(fn ($id) => $this->notif($id, $booking, $jenis, $judul, $isi));
    }

    /** Kegagalan email dicatat di log dan tidak menggagalkan booking. */
    private function kirimEmail(string $tujuan, string $subjek, string $isi): void
    {
        try {
            Mail::raw($isi, fn ($m) => $m->to($tujuan)->subject($subjek));
        } catch (\Throwable $e) {
            Log::warning('Email booking gagal dikirim: ' . $e->getMessage(), ['tujuan' => $tujuan]);
        }
    }
}