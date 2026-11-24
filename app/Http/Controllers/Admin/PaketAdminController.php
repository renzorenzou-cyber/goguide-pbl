<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ObjekWisata;
use App\Models\PaketWisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PaketAdminController extends Controller
{
    /** Foto disimpan di public/images/paket supaya cocok dengan PaketWisata::foto_url (pakai public_path). */
    private const FOLDER_FOTO = 'images/paket';

    public function index(Request $request)
    {
        $pakets = PaketWisata::query()
            ->withCount('booking')
            ->with('objek.area')
            ->when($request->q, fn ($q, $kw) => $q->where('nama_paket', 'like', "%{$kw}%"))
            ->orderByDesc('id_paket')
            ->paginate(6)
            ->withQueryString();

        return view('admin.paket.index', compact('pakets'));
    }

    public function create()
    {
        return view('admin.paket.create', [
            'paket'    => new PaketWisata(['status' => 'Aktif', 'persen_fee_guide' => 70]),
            'objeks'   => $this->objeks(),
            'terpilih' => collect(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $data) {
            $paket = new PaketWisata($data);

            if ($request->hasFile('foto')) {
                $paket->foto = $this->simpanFoto($request, $data['nama_paket']);
            }

            $paket->save();
            $this->syncObjek($paket->id_paket, $request);
        });

        return redirect()->route('admin.paket.index')
            ->with('success', 'Paket baru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $paket = PaketWisata::findOrFail($id);

        $terpilih = DB::table('detail_paket_wisata')
            ->where('id_paket', $paket->id_paket)
            ->get()
            ->keyBy('id_objek');

        return view('admin.paket.edit', [
            'paket'    => $paket,
            'objeks'   => $this->objeks(),
            'terpilih' => $terpilih,
        ]);
    }

    public function update(Request $request, $id)
    {
        $paket = PaketWisata::findOrFail($id);
        $data  = $this->validated($request);

        DB::transaction(function () use ($request, $paket, $data) {
            $paket->fill($data);

            if ($request->hasFile('foto')) {
                $this->hapusFoto($paket->foto);
                $paket->foto = $this->simpanFoto($request, $data['nama_paket']);
            }

            $paket->save();
            $this->syncObjek($paket->id_paket, $request);
        });

        return redirect()->route('admin.paket.index')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    public function toggleStatus($id)
    {
        $paket = PaketWisata::findOrFail($id);
        $paket->status = $paket->status === 'Aktif' ? 'Nonaktif' : 'Aktif';
        $paket->save();

        return back()->with('success', "Paket \"{$paket->nama_paket}\" sekarang {$paket->status}.");
    }

    public function destroy($id)
    {
        $paket = PaketWisata::findOrFail($id);

        // Paket yang sudah pernah dipesan tidak boleh dihapus supaya histori booking tetap utuh.
        if ($paket->booking()->exists()) {
            return back()->with('error',
                'Paket ini sudah punya booking, jadi tidak bisa dihapus. Nonaktifkan saja supaya tidak tampil di website.');
        }

        $this->hapusFoto($paket->foto);
        $paket->delete();

        return redirect()->route('admin.paket.index')
            ->with('success', 'Paket berhasil dihapus.');
    }

    /* ------------------------------------------------------------ */

    private function objeks()
    {
        return ObjekWisata::with(['area', 'kategori'])->where('status', 'Aktif')->orderBy('nama_objek')->get();
    }

    private function validated(Request $request): array
    {
        $v = $request->validate([
            'nama_paket'       => ['required', 'string', 'max:255'],
            'harga_paket'      => ['required', 'integer', 'min:0'],
            'persen_fee_guide' => ['required', 'integer', 'min:0', 'max:100'],
            'durasi'           => ['required', 'string', 'max:50'],
            'deskripsi'        => ['nullable', 'string'],
            'fasilitas'        => ['nullable', 'string'],
            'status'           => ['required', 'in:Aktif,Nonaktif'],
            'foto'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'objek'            => ['nullable', 'array'],
            'objek.*'          => ['integer'],
        ], [
            'nama_paket.required'  => 'Nama paket wajib diisi.',
            'harga_paket.required' => 'Harga per peserta wajib diisi.',
            'durasi.required'      => 'Durasi wajib diisi, misalnya "8 jam".',
            'foto.image'           => 'File foto harus berupa gambar.',
            'foto.max'             => 'Ukuran foto maksimal 2 MB.',
        ]);

        return collect($v)->only([
            'nama_paket', 'harga_paket', 'persen_fee_guide',
            'durasi', 'deskripsi', 'fasilitas', 'status',
        ])->all();
    }

    private function simpanFoto(Request $request, string $nama): string
    {
        $file = $request->file('foto');
        $nama = Str::slug($nama) . '-' . time() . '.' . $file->getClientOriginalExtension();

        File::ensureDirectoryExists(public_path(self::FOLDER_FOTO));
        $file->move(public_path(self::FOLDER_FOTO), $nama);

        return self::FOLDER_FOTO . '/' . $nama;
    }

    /** Hanya hapus foto buatan CRUD ini; foto bawaan seeder (images/tours/...) dibiarkan. */
    private function hapusFoto(?string $path): void
    {
        if ($path && str_starts_with($path, self::FOLDER_FOTO . '/') && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }

    /** Objek yang dikunjungi paket + urutan + lama kunjungan (tabel detail_paket_wisata). */
    private function syncObjek(int $idPaket, Request $request): void
    {
        DB::table('detail_paket_wisata')->where('id_paket', $idPaket)->delete();

        $now   = now();
        $rows  = [];
        $urut  = $request->input('urutan', []);
        $menit = $request->input('durasi_kunjungan', []);

        foreach ((array) $request->input('objek', []) as $idObjek) {
            $rows[] = [
                'id_paket'         => $idPaket,
                'id_objek'         => (int) $idObjek,
                'urutan'           => max(1, (int) ($urut[$idObjek] ?? 1)),
                'durasi_kunjungan' => max(5, (int) ($menit[$idObjek] ?? 60)),
                'keterangan'       => null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }

        usort($rows, fn ($a, $b) => $a['urutan'] <=> $b['urutan']);

        if ($rows) {
            DB::table('detail_paket_wisata')->insert($rows);
        }
    }
}