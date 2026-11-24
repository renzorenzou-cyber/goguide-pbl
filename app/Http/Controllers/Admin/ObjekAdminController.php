<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\KategoriWisata;
use App\Models\ObjekWisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ObjekAdminController extends Controller
{
    private const FOLDER_FOTO = 'images/objek';

    public function index(Request $request)
    {
        $objeks = ObjekWisata::with(['area', 'kategori'])
            ->withCount('paket')
            ->when($request->q, fn ($q, $kw) => $q->where('nama_objek', 'like', "%{$kw}%"))
            ->when($request->area, fn ($q, $a) => $q->where('id_area', $a))
            ->orderBy('nama_objek')
            ->paginate(6)
            ->withQueryString();

        return view('admin.objek.index', [
            'objeks' => $objeks,
            'areas'  => Area::orderBy('nama_area')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.objek.create', $this->formData(new ObjekWisata(['status' => 'Aktif'])));
    }

    public function store(Request $request)
    {
        $data  = $this->validated($request);
        $objek = new ObjekWisata($data);

        if ($request->hasFile('foto')) {
            $objek->foto = $this->simpanFoto($request, $data['nama_objek']);
        }

        $objek->save();

        return redirect()->route('admin.objek.index')->with('success', 'Objek wisata baru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return view('admin.objek.edit', $this->formData(ObjekWisata::findOrFail($id)));
    }

    public function update(Request $request, $id)
    {
        $objek = ObjekWisata::findOrFail($id);
        $data  = $this->validated($request);

        $objek->fill($data);

        if ($request->hasFile('foto')) {
            $this->hapusFoto($objek->foto);
            $objek->foto = $this->simpanFoto($request, $data['nama_objek']);
        }

        $objek->save();

        return redirect()->route('admin.objek.index')->with('success', 'Objek wisata berhasil diperbarui.');
    }

    public function toggleStatus($id)
    {
        $objek = ObjekWisata::findOrFail($id);
        $objek->status = $objek->status === 'Aktif' ? 'Nonaktif' : 'Aktif';
        $objek->save();

        return back()->with('success', "Objek \"{$objek->nama_objek}\" sekarang {$objek->status}.");
    }

    public function destroy($id)
    {
        $objek = ObjekWisata::findOrFail($id);

        $dipakai = DB::table('detail_paket_wisata')
            ->join('paket_wisata', 'paket_wisata.id_paket', '=', 'detail_paket_wisata.id_paket')
            ->where('detail_paket_wisata.id_objek', $objek->id_objek)
            ->pluck('paket_wisata.nama_paket');

        if ($dipakai->isNotEmpty()) {
            return back()->with('error',
                "Objek \"{$objek->nama_objek}\" masih dipakai di paket: " . $dipakai->implode(', ')
                . '. Lepas dari paket itu dulu, atau nonaktifkan saja objeknya.');
        }

        $this->hapusFoto($objek->foto);
        $objek->delete();

        return redirect()->route('admin.objek.index')->with('success', 'Objek wisata berhasil dihapus.');
    }

    /* ------------------------------------------------------------ */

    private function formData(ObjekWisata $objek): array
    {
        return [
            'objek'     => $objek,
            'areas'     => Area::orderBy('nama_area')->get(),
            'kategoris' => KategoriWisata::orderBy('nama_kategori')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $v = $request->validate([
            'nama_objek'  => ['required', 'string', 'max:255'],
            'id_kategori' => ['required', 'exists:kategori_wisata,id_kategori'],
            'id_area'     => ['required', 'exists:area,id_area'],
            'deskripsi'   => ['nullable', 'string'],
            'alamat'      => ['nullable', 'string'],
            'harga_tiket' => ['nullable', 'numeric', 'min:0'],
            'status'      => ['required', 'in:Aktif,Nonaktif'],
            'foto'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'nama_objek.required'  => 'Nama objek wisata wajib diisi.',
            'id_kategori.required' => 'Pilih kategori.',
            'id_area.required'     => 'Pilih area.',
            'harga_tiket.numeric'  => 'Harga tiket harus berupa angka.',
            'foto.image'           => 'File foto harus berupa gambar.',
            'foto.max'             => 'Ukuran foto maksimal 2 MB.',
        ]);

        return collect($v)->only([
            'nama_objek', 'id_kategori', 'id_area', 'deskripsi', 'alamat', 'harga_tiket', 'status',
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

    private function hapusFoto(?string $path): void
    {
        if ($path && str_starts_with($path, self::FOLDER_FOTO . '/') && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}