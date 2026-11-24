<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\PaketWisata;
use App\Models\TourGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaketController extends Controller
{
    public function index(Request $request)
    {
        $guide = $request->user()->guide;
        abort_unless($guide, 403, 'Data guide belum terhubung. Hubungi admin.');

        $pakets = PaketWisata::aktif()->with('objek')->orderBy('nama_paket')->get();

        $dipilih = TourGuide::where('id_guide', $guide->id_guide)
            ->where('status', 'Aktif')
            ->pluck('id_paket')
            ->all();

        return view('guide.paket', compact('guide', 'pakets', 'dipilih'));
    }

    public function update(Request $request)
    {
        $guide = $request->user()->guide;
        abort_unless($guide, 403, 'Data guide belum terhubung. Hubungi admin.');

        $data = $request->validate([
            'paket'   => ['nullable', 'array'],
            'paket.*' => ['integer', 'exists:paket_wisata,id_paket'],
        ]);

        $dipilih = collect($data['paket'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();
        $valid = PaketWisata::aktif()->whereIn('id_paket', $dipilih)->pluck('id_paket');

        DB::transaction(function () use ($guide, $valid) {
            // Yang tidak dicentang dinonaktifkan (bukan dihapus) supaya riwayat aman
            TourGuide::where('id_guide', $guide->id_guide)
                ->whereNotIn('id_paket', $valid)
                ->update(['status' => 'Nonaktif']);

            foreach ($valid as $idPaket) {
                TourGuide::updateOrCreate(
                    ['id_guide' => $guide->id_guide, 'id_paket' => $idPaket],
                    ['status' => 'Aktif']
                );
            }
        });

        return redirect()->route('guide.paket')
            ->with('success', $valid->isEmpty()
                ? 'Semua paket dinonaktifkan. Kamu tidak akan tampil di pilihan wisatawan sampai memilih paket lagi.'
                : 'Paket yang kamu tangani berhasil disimpan (' . $valid->count() . ' paket).');
    }
}