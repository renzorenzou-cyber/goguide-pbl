<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriWisata;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriAdminController extends Controller
{
    public function index()
    {
        $kategoris = KategoriWisata::withCount('objek')->orderBy('nama_kategori')->paginate(5);

        return view('admin.kategori.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        KategoriWisata::create($this->validated($request));

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriWisata::findOrFail($id);
        $kategori->update($this->validated($request, $kategori->id_kategori));

        return back()->with('success', "Kategori \"{$kategori->nama_kategori}\" diperbarui.");
    }

    public function destroy($id)
    {
        $kategori = KategoriWisata::findOrFail($id);

        if ($kategori->objek()->exists()) {
            return back()->with('error',
                "Kategori \"{$kategori->nama_kategori}\" masih dipakai {$kategori->objek()->count()} objek wisata. Ubah kategori objeknya dulu.");
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nama_kategori' => [
                'required', 'string', 'max:100',
                Rule::unique('kategori_wisata', 'nama_kategori')->ignore($ignoreId, 'id_kategori'),
            ],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique'   => 'Kategori dengan nama itu sudah ada.',
        ]);
    }
}