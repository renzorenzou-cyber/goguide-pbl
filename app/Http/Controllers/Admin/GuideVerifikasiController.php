<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GuideVerifikasiController extends Controller
{
    private const TAB = ['Menunggu', 'Terverifikasi', 'Ditolak'];

    public function index(Request $request)
    {
        $tab = in_array($request->query('status'), self::TAB, true) ? $request->query('status') : 'Menunggu';

        $jumlah = Guide::selectRaw('status_verifikasi, count(*) as n')->groupBy('status_verifikasi')->pluck('n', 'status_verifikasi');

        $guides = Guide::where('status_verifikasi', $tab)
            ->when($request->q, fn ($q, $kw) => $q->where(fn ($w) => $w->where('nama_guide', 'like', "%{$kw}%")->orWhere('email', 'like', "%{$kw}%")))
            ->orderByDesc('created_at')
            ->paginate(8)
            ->withQueryString();

        return view('admin.guide.index', compact('guides', 'tab', 'jumlah'));
    }

    public function show($id)
    {
        $guide = Guide::with(['bahasa', 'penyetuju'])->findOrFail($id);
        [$indikator, $layak] = $this->indikator($guide, false);
        [$indikatorWaOk, $layakJikaWaOk] = $this->indikator($guide, true);   // kondisi setelah admin mencentang konfirmasi WA

        return view('admin.guide.show', compact('guide', 'indikator', 'layak', 'indikatorWaOk', 'layakJikaWaOk'));
    }

    public function bio($id)
    {
        $guide = Guide::findOrFail($id);
        abort_unless($guide->bio_lengkap_file && Storage::exists($guide->bio_lengkap_file), 404, 'Berkas biografi tidak ditemukan.');

        return Storage::download($guide->bio_lengkap_file, 'bio-' . Str::slug($guide->nama_guide) . '.' . pathinfo($guide->bio_lengkap_file, PATHINFO_EXTENSION));
    }

    /** Tampilkan sertifikat (gambar/PDF) langsung di browser, hanya untuk admin. */
public function sertifikat($id)
{
    $guide = Guide::findOrFail($id);
    abort_unless($guide->sertifikat_file && Storage::exists($guide->sertifikat_file), 404, 'Sertifikat tidak diunggah.');

    return Storage::response($guide->sertifikat_file);
}

    /* ================= Setujui ================= */

    public function setujui(Request $request, $id)
    {
        $guide = Guide::with('bahasa')->findOrFail($id);

        if ($guide->status_verifikasi !== 'Menunggu') {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        $waOk = $request->boolean('wa_dikonfirmasi');
        [, $layak] = $this->indikator($guide, $waOk);

        // Bila ada indikator wajib yang belum terpenuhi, admin harus menyetujui secara sadar.
        if (! $layak && ! $request->boolean('tetap_setuju')) {
            return back()->with('error', 'Ada indikator wajib yang belum terpenuhi. Centang "Tetap setujui" bila kamu yakin, atau tolak pendaftaran ini.');
        }

        if (User::where('email', $guide->email)->exists()) {
            return back()->with('error', 'Email ini sudah dipakai akun lain, jadi akun guide tidak bisa dibuat.');
        }

        $password = $this->passwordSementara();
        $username = $this->buatUsername($guide->nama_guide);

        DB::transaction(function () use ($guide, $password, $username, $request) {
            $user = User::create([
                'name'                 => $guide->nama_guide,
                'email'                => $guide->email,
                'password'             => $password,          // di-hash otomatis oleh cast 'hashed' pada model User
                'role'                 => 'guide',
                'phone'                => $guide->no_wa,
                'photo'                => $guide->foto,
                'username'             => $username,
                'wajib_ganti_password' => true,
            ]);

            $guide->update([
                'id_user'             => $user->id,
                'status_verifikasi'   => 'Terverifikasi',
                'status'              => 'Aktif',
                'alasan_penolakan'    => null,
                'disetujui_oleh'      => $request->user()->id,
                'tanggal_persetujuan' => now(),
            ]);
        });

        $terkirim = $this->kirimEmail($guide->email, 'Akun Go Guide kamu sudah aktif',
            "Halo {$guide->nama_guide},\n\n"
            . "Pendaftaran kamu sebagai guide di Go Guide telah disetujui.\n\n"
            . "Login di: " . route('login') . "\n"
            . "Username: {$username}\n"
            . "Password sementara: {$password}\n\n"
            . "Kamu akan diminta mengganti password saat login pertama. Setelah itu lengkapi paket yang kamu tangani dan jadwal layananmu agar bisa dipesan wisatawan.\n\n"
            . "Salam,\nGo Guide");

        return redirect()->route('admin.guide.show', $guide->id_guide)
            ->with('success', "{$guide->nama_guide} disetujui dan akun guide dibuat.")
            ->with('kredensial', [
                'username' => $username,
                'password' => $password,
                'email'    => $guide->email,
                'terkirim' => $terkirim,
            ]);
    }

    /* ================= Tolak ================= */

    public function tolak(Request $request, $id)
    {
        $guide = Guide::findOrFail($id);

        if ($guide->status_verifikasi !== 'Menunggu') {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        $data = $request->validate([
            'alasan' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'alasan.required' => 'Alasan penolakan wajib diisi.',
            'alasan.min'      => 'Alasan penolakan minimal 10 karakter, supaya jelas bagi calon guide.',
        ]);

        $guide->update([
            'status_verifikasi'   => 'Ditolak',
            'alasan_penolakan'    => $data['alasan'],
            'disetujui_oleh'      => $request->user()->id,
            'tanggal_persetujuan' => now(),
        ]);

        $terkirim = $this->kirimEmail($guide->email, 'Pendaftaran guide Go Guide belum dapat disetujui',
            "Halo {$guide->nama_guide},\n\n"
            . "Terima kasih sudah mendaftar sebagai guide di Go Guide. Setelah diperiksa, pendaftaran kamu belum dapat disetujui.\n\n"
            . "Alasan: {$data['alasan']}\n\n"
            . "Kamu dapat mendaftar kembali setelah melengkapi data di: " . route('guide.daftar') . "\n\n"
            . "Salam,\nGo Guide");

        return redirect()->route('admin.guide.show', $guide->id_guide)
            ->with('success', $terkirim
                ? "Pendaftaran {$guide->nama_guide} ditolak dan alasan dikirim lewat email."
                : "Pendaftaran {$guide->nama_guide} ditolak, tetapi email gagal terkirim. Hubungi calon guide lewat WhatsApp.");
    }

    /* ================= Indikator kelayakan (proposal 6.3.4) ================= */

    /** @return array{0: array<int, array>, 1: bool}  [daftar indikator, layak?] */
    private function indikator(Guide $g, bool $waDikonfirmasi): array
    {
        $min = (int) config('goguide.min_pengalaman_tahun', 1);

        $identitasLengkap = filled($g->nama_guide) && filled($g->alamat) && filled($g->email) && filled($g->no_wa);
        $emailBebas = ! User::where('email', $g->email)->exists();

        $tahun = preg_match('/(\d+)\s*(?:tahun|thn)/i', (string) $g->pengalaman, $m) ? (int) $m[1] : 0;
        $bersertifikat = filled($g->sertifikat_file);   // dihitung hanya bila berkasnya benar-benar diunggah
        $pengalamanOk = filled($g->pengalaman) && ($tahun >= $min || $bersertifikat);

        $bahasaOk = $g->bahasa->contains(fn ($b) => in_array($b->pivot->tingkat_kemampuan, ['Menengah', 'Mahir', 'Fasih'], true));

        $daftar = [
            ['no' => 1, 'nama' => 'Kelengkapan identitas', 'kriteria' => 'Nama, alamat, email, dan nomor WA terisi', 'wajib' => true,
             'ok' => $identitasLengkap, 'catatan' => $identitasLengkap ? 'Lengkap' : 'Ada data identitas yang kosong'],
            ['no' => 2, 'nama' => 'Keabsahan kontak', 'kriteria' => 'Email belum terdaftar; WA aktif (dikonfirmasi admin)', 'wajib' => true,
             'ok' => $emailBebas && $waDikonfirmasi,
             'catatan' => ! $emailBebas ? 'Email sudah dipakai akun lain'
                        : ($waDikonfirmasi ? 'Email belum terdaftar, WA dikonfirmasi' : 'Email belum terdaftar. WA belum dikonfirmasi: hubungi calon guide, lalu centang di formulir keputusan')],
            ['no' => 3, 'nama' => 'Kelengkapan profil', 'kriteria' => 'Bio singkat terisi dan bio lengkap terunggah', 'wajib' => true,
             'ok' => filled($g->bio_singkat) && filled($g->bio_lengkap_file),
             'catatan' => (filled($g->bio_singkat) && filled($g->bio_lengkap_file)) ? 'Lengkap' : 'Bio singkat atau berkas bio lengkap belum ada'],
            ['no' => 4, 'nama' => 'Pengalaman', 'kriteria' => "Terisi dan minimal {$min} tahun, atau ada berkas sertifikat pemandu", 'wajib' => true,
             'ok' => $pengalamanOk,
             'catatan' => $bersertifikat ? "Sertifikat diunggah ({$tahun} tahun)" : ($tahun ? "{$tahun} tahun, tanpa sertifikat" : 'Lama pengalaman tidak terbaca')],
            ['no' => 5, 'nama' => 'Bahasa', 'kriteria' => 'Minimal satu bahasa dengan tingkat Menengah ke atas', 'wajib' => true,
             'ok' => $bahasaOk, 'catatan' => $bahasaOk ? 'Terpenuhi' : 'Belum ada bahasa tingkat Menengah ke atas'],
            ['no' => 6, 'nama' => 'Riwayat', 'kriteria' => 'Belum pernah ditolak karena pelanggaran', 'wajib' => false,
             'ok' => blank($g->alasan_penolakan),
             'catatan' => blank($g->alasan_penolakan) ? 'Tidak ada riwayat penolakan' : 'Pernah ditolak: ' . Str::limit($g->alasan_penolakan, 120)],
        ];

        $layak = collect($daftar)->where('wajib', true)->every(fn ($i) => $i['ok']);

        return [$daftar, $layak];
    }

    /* ================= Pembantu ================= */

    private function buatUsername(string $nama): string
    {
        $dasar = Str::limit(Str::slug($nama, '.') ?: 'guide', 80, '');
        $u = $dasar;
        $i = 1;

        while (User::where('username', $u)->exists()) {
            $u = $dasar . $i++;
        }

        return $u;
    }

    /** 10 karakter tanpa huruf/angka yang mudah tertukar (0/O, 1/l/I). */
    private function passwordSementara(): string
    {
        $abjad = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $p = '';
        for ($i = 0; $i < 10; $i++) {
            $p .= $abjad[random_int(0, strlen($abjad) - 1)];
        }

        return $p;
    }

    /** Mengembalikan true bila email berhasil dikirim. Kegagalan dicatat di log, tidak menggagalkan proses. */
    private function kirimEmail(string $tujuan, string $subjek, string $isi): bool
    {
        try {
            Mail::raw($isi, fn ($m) => $m->to($tujuan)->subject($subjek));

            return true;
        } catch (\Throwable $e) {
            Log::warning('Email guide gagal dikirim: ' . $e->getMessage(), ['tujuan' => $tujuan]);

            return false;
        }
    }
}