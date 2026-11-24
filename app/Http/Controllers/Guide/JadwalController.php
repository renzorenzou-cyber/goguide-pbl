<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\JadwalGuide;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    private const MAKS_HARI = 62;

    public function index(Request $request)
    {
        $guide = $request->user()->guide;
        abort_unless($guide, 403, 'Data guide belum terhubung. Hubungi admin.');

        $jadwal = $guide->jadwal()
            ->whereDate('tanggal_aktif', '>=', today())
            ->orderBy('tanggal_aktif')
            ->get();

        $terpesan = $guide->booking()
            ->where('status', '!=', 'dibatalkan')
            ->whereDate('tanggal_booking', '>=', today())
            ->get()
            ->map(fn ($b) => $b->tanggal_booking->toDateString())
            ->all();

        return view('guide.jadwal', [
            'guide'      => $guide,
            'jadwal'     => $jadwal,
            'terpesan'   => $terpesan,
            'jamMulai'   => '08:00',
            'jamSelesai' => '17:00',
            'besok'      => today()->addDay()->toDateString(),
        ]);
    }

    public function store(Request $request)
    {
        $guide = $request->user()->guide;
        abort_unless($guide, 403, 'Data guide belum terhubung. Hubungi admin.');

        $besok = today()->addDay()->toDateString();

        $data = $request->validate([
            'dari'        => ['required', 'date', 'after_or_equal:' . $besok],
            'sampai'      => ['nullable', 'date', 'after_or_equal:dari'],
            'jam_mulai'   => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'keterangan'  => ['nullable', 'string', 'max:255'],
        ], [
            'dari.required'           => 'Tanggal mulai wajib diisi.',
            'dari.after_or_equal'     => 'Tanggal minimal besok.',
            'sampai.after_or_equal'   => 'Tanggal akhir tidak boleh sebelum tanggal mulai.',
            'jam_selesai.after'       => 'Jam selesai harus setelah jam mulai.',
            'jam_mulai.date_format'   => 'Format jam mulai harus JJ:MM.',
            'jam_selesai.date_format' => 'Format jam selesai harus JJ:MM.',
        ]);

        $dari   = Carbon::parse($data['dari'])->startOfDay();
        $sampai = Carbon::parse($data['sampai'] ?? $data['dari'])->startOfDay();

        if ($dari->diffInDays($sampai) + 1 > self::MAKS_HARI) {
            return back()->withInput()->withErrors(['sampai' => 'Rentang maksimal ' . self::MAKS_HARI . ' hari sekali input.']);
        }

        $sudahAda = $guide->jadwal()
            ->whereBetween('tanggal_aktif', [$dari->toDateString(), $sampai->toDateString()])
            ->get()
            ->keyBy(fn ($j) => $j->tanggal_aktif->toDateString());

        $baru = 0;
        $diperbarui = 0;

        for ($t = $dari->copy(); $t->lte($sampai); $t->addDay()) {
            $tgl = $t->toDateString();
            $isi = [
                'jam_mulai'   => $data['jam_mulai'],
                'jam_selesai' => $data['jam_selesai'],
                'status'      => 'Tersedia',
                'keterangan'  => $data['keterangan'] ?? null,
            ];

            if ($ada = $sudahAda->get($tgl)) {
                $ada->update($isi);
                $diperbarui++;
            } else {
                JadwalGuide::create(['id_guide' => $guide->id_guide, 'tanggal_aktif' => $tgl] + $isi);
                $baru++;
            }
        }

        return redirect()->route('guide.jadwal')
            ->with('success', "Jadwal disimpan: {$baru} tanggal baru" . ($diperbarui ? ", {$diperbarui} tanggal diperbarui." : '.'));
    }

    public function destroy(Request $request, $id)
    {
        $guide = $request->user()->guide;
        abort_unless($guide, 403);

        $j = JadwalGuide::where('id_guide', $guide->id_guide)->findOrFail($id);

        $adaBooking = $guide->booking()
            ->where('status', '!=', 'dibatalkan')
            ->whereDate('tanggal_booking', $j->tanggal_aktif->toDateString())
            ->exists();

        if ($adaBooking) {
            return back()->with('error', 'Tanggal ' . $j->tanggal_aktif->format('d M Y') . ' sudah dipesan wisatawan, jadi tidak bisa dihapus.');
        }

        $j->delete();

        return back()->with('success', 'Tanggal ' . $j->tanggal_aktif->format('d M Y') . ' dihapus dari jadwal.');
    }
}