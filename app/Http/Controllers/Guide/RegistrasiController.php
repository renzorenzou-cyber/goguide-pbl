<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\Bahasa;
use App\Models\Guide;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegistrasiController extends Controller
{
    public const TINGKAT = ['Dasar', 'Menengah', 'Mahir', 'Fasih'];

    public function create()
    {
        return view('guide.daftar', [
            'bahasas' => Bahasa::orderBy('nama_bahasa')->get(),
            'tingkat' => self::TINGKAT,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_guide'        => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255'],
            'no_wa'             => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'alamat'            => ['required', 'string', 'max:255'],
            'bio_singkat'       => ['required', 'string', 'max:500'],
            'bio_lengkap'       => ['required', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
            'pengalaman_tahun'  => ['required', 'integer', 'min:0', 'max:60'],
            'pengalaman_uraian' => ['required', 'string', 'max:2000'],
            'sertifikat_file'   => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:2048'],
            'foto'              => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'bahasa'            => ['required', 'array', 'min:1'],
            'bahasa.*'          => ['integer', 'exists:bahasa,id_bahasa'],
            'tingkat'           => ['nullable', 'array'],
            'tingkat.*'         => ['in:' . implode(',', self::TINGKAT)],
        ], [
            'nama_guide.required'        => 'Nama lengkap wajib diisi.',
            'email.required'             => 'Email wajib diisi.',
            'email.email'                => 'Format email belum benar.',
            'no_wa.required'             => 'Nomor WhatsApp wajib diisi.',
            'no_wa.regex'                => 'Nomor WhatsApp hanya boleh berisi angka, spasi, tanda + atau -.',
            'alamat.required'            => 'Alamat wajib diisi.',
            'bio_singkat.required'       => 'Bio singkat wajib diisi.',
            'bio_lengkap.required'       => 'Unggah biografi lengkap (PDF atau Word).',
            'bio_lengkap.mimes'          => 'Biografi lengkap harus berformat PDF, DOC, atau DOCX.',
            'bio_lengkap.max'            => 'Ukuran biografi lengkap maksimal 2 MB.',
            'sertifikat_file.mimes'      => 'Sertifikat harus berformat JPG, PNG, WebP, atau PDF.',
            'sertifikat_file.max'        => 'Ukuran sertifikat maksimal 2 MB.',
            'pengalaman_tahun.required'  => 'Isi lama pengalaman (boleh 0).',
            'pengalaman_uraian.required' => 'Ceritakan pengalaman kamu sebagai pemandu.',
            'bahasa.required'            => 'Pilih minimal satu bahasa yang kamu kuasai.',
            'bahasa.min'                 => 'Pilih minimal satu bahasa yang kamu kuasai.',
        ]);

        $email = Str::lower(trim($data['email']));

        if (User::where('email', $email)->exists()) {
            return back()->withInput()->withErrors(['email' => 'Email ini sudah terdaftar sebagai akun. Gunakan email lain.']);
        }

        $lama = Guide::where('email', $email)->first();
        if ($lama && $lama->status_verifikasi !== 'Ditolak') {
            return back()->withInput()->withErrors(['email' => 'Email ini sudah mendaftar dan sedang/ sudah diproses admin.']);
        }

        // Berkas
        $pathBio = $request->file('bio_lengkap')->store('guide-bio');   // privat, hanya admin yang bisa mengunduh
        $pathFoto = $request->hasFile('foto') ? $this->simpanFoto($request, $data['nama_guide']) : ($lama->foto ?? null);
        $pathSertifikat = $request->hasFile('sertifikat_file')
    ? $request->file('sertifikat_file')->store('guide-sertifikat')   // privat, hanya admin yang bisa melihat
    : ($lama->sertifikat_file ?? null);
        $pengalaman = "{$data['pengalaman_tahun']} tahun. " . trim($data['pengalaman_uraian']);

        $isi = [
            'nama_guide'        => trim($data['nama_guide']),
            'email'             => $email,
            'no_wa'             => trim($data['no_wa']),
            'alamat'            => trim($data['alamat']),
            'bio_singkat'       => trim($data['bio_singkat']),
            'bio_lengkap_file'  => $pathBio,
            'sertifikat_file'   => $pathSertifikat,
            'pengalaman'        => $pengalaman,
            'foto'              => $pathFoto,
            'status_verifikasi' => 'Menunggu',
            'status'            => 'Aktif',
        ];

        $guide = DB::transaction(function () use ($lama, $isi, $data, $request) {
            if ($lama) {
                // Daftar ulang setelah ditolak: data diperbarui, alasan penolakan lama dibiarkan sebagai riwayat
                if ($request->hasFile('sertifikat_file') && $lama->sertifikat_file) {
    Storage::delete($lama->sertifikat_file);   // diganti berkas baru
}
                $lama->update($isi);
                $guide = $lama;
                DB::table('detail_bahasa_guide')->where('id_guide', $guide->id_guide)->delete();
            } else {
                $guide = Guide::create($isi);
            }

            $now = now();
            $baris = collect($data['bahasa'])->unique()->map(fn ($idBahasa) => [
                'id_guide'          => $guide->id_guide,
                'id_bahasa'         => (int) $idBahasa,
                'tingkat_kemampuan' => $data['tingkat'][$idBahasa] ?? 'Menengah',
                'created_at'        => $now,
                'updated_at'        => $now,
            ])->all();
            DB::table('detail_bahasa_guide')->insert($baris);

            return $guide;
        });

        // Notifikasi dalam aplikasi untuk semua admin
        User::where('role', 'admin')->pluck('id')->each(fn ($idAdmin) => Notifikasi::create([
            'id_user' => $idAdmin, 'id_booking' => null, 'jenis' => 'guide_daftar',
            'judul'   => 'Pendaftaran guide baru',
            'isi'     => "{$guide->nama_guide} ({$guide->email}) mendaftar sebagai guide dan menunggu verifikasi.",
            'dibaca'  => false,
        ]));

        return redirect()->route('guide.daftar')
            ->with('berhasil', 'Pendaftaran terkirim. Admin akan memeriksa datamu, dan akun akan dikirim lewat email setelah disetujui.');
    }

    private function simpanFoto(Request $request, string $nama): string
    {
        $file = $request->file('foto');
        $nama = Str::slug($nama) . '-' . time() . '.' . $file->getClientOriginalExtension();

        File::ensureDirectoryExists(public_path('images/guide'));
        $file->move(public_path('images/guide'), $nama);

        return 'images/guide/' . $nama;
    }
}