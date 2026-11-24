<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Guide;
use App\Models\PaketWisata;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /** Jumlah paket yang tampil di halaman utama. Sisanya ada di halaman /paket. */
    private const PAKET_DI_HOME = 6;

    public function index(Request $request)
    {
        $areas = Area::orderBy('nama_area')->get();

        // Filter dasar (dipakai untuk menghitung total dan mengambil data)
        $dasar = PaketWisata::aktif()
            // Filter area (chip)
            ->when($request->area, fn ($q, $idArea) =>
                $q->whereHas('objek', fn ($o) => $o->where('objek_wisata.id_area', $idArea)))
            // Pencarian: nama paket, deskripsi, atau nama objek
            ->when($request->q, function ($q, $kata) {
                $q->where(function ($w) use ($kata) {
                    $w->where('nama_paket', 'like', "%{$kata}%")
                      ->orWhere('deskripsi', 'like', "%{$kata}%")
                      ->orWhereHas('objek', fn ($o) => $o->where('nama_objek', 'like', "%{$kata}%"));
                });
            });

        $totalPaket = (clone $dasar)->count();

        $pakets = (clone $dasar)
            ->with('objek.area')
            ->withCount(['guide as jumlah_guide' => fn ($q) => $q->where('guide.status_verifikasi', 'Terverifikasi')->where('guide.status', 'Aktif')])
            ->orderByDesc('paket_wisata.id_paket')   // terbaru dulu, paket baru dari admin langsung tampil
            ->take(self::PAKET_DI_HOME)
            ->get();

        $guides = Guide::disetujui()->withCount('paket')->orderByDesc('rating')->take(4)->get();

        return view('home', compact('areas', 'pakets', 'totalPaket', 'guides'));
    }
}