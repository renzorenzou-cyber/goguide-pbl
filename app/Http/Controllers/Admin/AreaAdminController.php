<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaAdminController extends Controller
{
    public function index()
    {
        $areas = Area::withCount('objek')->orderBy('nama_area')->paginate(5);

        return view('admin.area.index', compact('areas'));
    }

    public function store(Request $request)
    {
        Area::create($this->validated($request));

        return redirect()->route('admin.area.index')->with('success', 'Area baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $area = Area::findOrFail($id);
        $area->update($this->validated($request));

        return back()->with('success', "Area \"{$area->nama_area}\" diperbarui.");
    }

    public function destroy($id)
    {
        $area = Area::findOrFail($id);

        if ($area->objek()->exists()) {
            return back()->with('error',
                "Area \"{$area->nama_area}\" masih dipakai {$area->objek()->count()} objek wisata. Pindahkan atau hapus objeknya dulu.");
        }

        $area->delete();

        return redirect()->route('admin.area.index')->with('success', 'Area berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama_area' => ['required', 'string', 'max:100'],
            'kabupaten' => ['required', 'string', 'max:100'],
            'alamat'    => ['nullable', 'string'],
            'latitude'  => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ], [
            'nama_area.required' => 'Nama area wajib diisi.',
            'kabupaten.required' => 'Kabupaten wajib diisi.',
            'latitude.between'   => 'Latitude harus di antara -90 dan 90.',
            'longitude.between'  => 'Longitude harus di antara -180 dan 180.',
        ]);
    }
}