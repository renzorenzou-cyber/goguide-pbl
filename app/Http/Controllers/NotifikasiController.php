<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $notifikasi = Notifikasi::with('booking')
            ->where('id_user', $request->user()->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id_notifikasi')
            ->paginate(15);

        $belumDibaca = Notifikasi::where('id_user', $request->user()->id)
            ->where('dibaca', false)
            ->count();

        return view('notifikasi.index', [
            'notifikasi'  => $notifikasi,
            'belumDibaca' => $belumDibaca,
        ]);
    }

    /** Tandai dibaca, lalu buka halaman terkait. */
    public function baca(Request $request, $id)
    {
        $notif = Notifikasi::where('id_user', $request->user()->id)->findOrFail($id);

        if (! $notif->dibaca) {
            $notif->update(['dibaca' => true]);
        }

        return redirect($this->tujuan($request, $notif));
    }

    public function bacaSemua(Request $request)
    {
        Notifikasi::where('id_user', $request->user()->id)
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /** bookings.show cuma bisa dibuka pemilik, jadi admin dan guide diarahkan ke halaman mereka. */
    private function tujuan(Request $request, Notifikasi $n): string
    {
        $role = $request->user()->role;

        if ($role === 'admin') {
            if ($n->jenis === 'guide_daftar') {
                return route('admin.guide.index');
            }
            if ($n->booking) {
                return route('admin.booking.index', ['status' => $n->booking->status, 'q' => $n->booking->kode_booking]);
            }

            return route('admin.dashboard');
        }

        if ($role === 'guide') {
            return route('guide.dashboard');
        }

        return $n->id_booking ? route('bookings.show', $n->id_booking) : route('bookings.index');
    }
}