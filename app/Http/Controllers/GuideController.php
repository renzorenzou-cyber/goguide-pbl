<?php

namespace App\Http\Controllers;

use App\Models\Bahasa;
use App\Models\Guide;
use App\Models\PaketWisata;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    /** Daftar guide yang boleh dilihat publik: Terverifikasi + Aktif + sudah punya paket. */
    public function index(Request $request)
    {
        $guides = Guide::disetujui()
            ->has('paket')
            ->with(['user','bahasa', 'paket'])
            ->when($request->q, fn ($q, $kata) =>
                $q->where('guide.nama_guide', 'like', "%{$kata}%"))
            ->when($request->bahasa, fn ($q, $idBahasa) =>
                $q->whereHas('bahasa', fn ($b) => $b->where('bahasa.id_bahasa', $idBahasa)))
            ->when($request->paket, fn ($q, $idPaket) =>
                $q->whereHas('paket', fn ($p) => $p->where('paket_wisata.id_paket', $idPaket)))
            ->orderByDesc('guide.rating')
            ->orderBy('guide.nama_guide')
            ->paginate(9)
            ->withQueryString();

        $daftarBahasa = Bahasa::orderBy('nama_bahasa')->get();
        $daftarPaket  = PaketWisata::aktif()->orderBy('nama_paket')->get(['id_paket', 'nama_paket']);

        return view('guides.index', compact('guides', 'daftarBahasa', 'daftarPaket'));
    }

    /** Profil lengkap satu guide. Guide yang belum terverifikasi atau nonaktif tidak bisa dibuka. */
    public function show(Guide $guide)
    {
        abort_unless(
            $guide->status_verifikasi === 'Terverifikasi' && $guide->status === 'Aktif',
            404
        );

        $guide->load(['user', 'bahasa', 'paket']);

        // 30 tanggal terdekat yang tersedia (jadwal Tersedia dan belum dipesan)
        $tanggalTersedia = $guide->tanggalTersedia(30);

        return view('guides.show', compact('guide', 'tanggalTersedia'));
    }
}