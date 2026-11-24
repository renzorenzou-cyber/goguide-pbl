<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\PaketWisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaketController extends Controller
{
    /** Daftar semua paket aktif: pencarian, filter area, dan pagination. */
    public function index(Request $request)
    {
        $pakets = PaketWisata::aktif()
            ->with('objek.area')
            ->withCount(['guide as jumlah_guide' => fn ($q) => $q->where('guide.status_verifikasi', 'Terverifikasi')->where('guide.status', 'Aktif')])
            ->when($request->area, fn ($q, $idArea) =>
                $q->whereHas('objek', fn ($o) => $o->where('objek_wisata.id_area', $idArea)))
            ->when($request->q, function ($q, $kata) {
                $q->where(function ($w) use ($kata) {
                    $w->where('nama_paket', 'like', "%{$kata}%")
                      ->orWhere('deskripsi', 'like', "%{$kata}%")
                      ->orWhereHas('objek', fn ($o) => $o->where('nama_objek', 'like', "%{$kata}%"));
                });
            })
            ->orderByDesc('paket_wisata.id_paket')
            ->paginate(9)
            ->withQueryString();

        $areas = Area::orderBy('nama_area')->get();

        return view('paket.index', compact('pakets', 'areas'));
    }

    public function show(Request $request, PaketWisata $paket)
    {
        abort_unless($paket->status === 'Aktif', 404);

        $paket->load('objek.area', 'objek.kategori');

        $guides = $paket->guide()->disetujui()->orderByDesc('rating')->get();
        foreach ($guides as $g) {
            $g->tanggal_terdekat = $g->tanggalTersedia();
        }

        // Cek ketersediaan (dijalankan bila guide atau tanggal dikirim lewat form)
        $hasil = null;
        $guidePilihan = null;

        if ($request->filled('guide') || $request->filled('tanggal')) {
            $v = Validator::make($request->query(), [
                'guide'   => ['required', 'integer'],
                'tanggal' => ['required', 'date_format:Y-m-d'],
            ], [
                'guide.required'   => 'Pilih guide terlebih dahulu.',
                'tanggal.required' => 'Pilih tanggal terlebih dahulu.',
                'date_format'      => 'Format tanggal tidak valid.',
            ]);

            if ($v->fails()) {
                $hasil = [false, $v->errors()->first()];
            } else {
                $guidePilihan = $guides->firstWhere('id_guide', (int) $request->query('guide'));
                $hasil = $guidePilihan
                    ? $guidePilihan->cekKetersediaan($request->query('tanggal'))
                    : [false, 'Guide tersebut tidak menangani paket ini.'];
            }
        }

        return view('paket.show', compact('paket', 'guides', 'hasil', 'guidePilihan'));
    }
}