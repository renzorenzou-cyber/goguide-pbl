<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $guide = $request->user()->guide;

        // Akun role guide tapi profil guide belum dibuat
        if (! $guide) {
            return view('guide.dashboard', ['guide' => null]);
        }

        $tours   = $guide->tours()->with('region')->get();
        $tourIds = $tours->pluck('id');

        $base = Booking::whereIn('tour_id', $tourIds);

        $stats = [
            'tours'    => $tours->count(),
            'upcoming' => (clone $base)->where('status', 'paid')->whereDate('tour_date', '>=', today())->count(),
            'pending'  => (clone $base)->where('status', 'pending')->count(),
            'value'    => (clone $base)->where('status', 'paid')->sum('total_price'),
        ];

        // Hanya booking yang sudah dibayar, jadi kontak customer tidak terbuka untuk booking yang belum jadi
        $upcoming = Booking::with(['user', 'tour'])
            ->whereIn('tour_id', $tourIds)
            ->where('status', 'paid')
            ->whereDate('tour_date', '>=', today())
            ->orderBy('tour_date')
            ->take(10)
            ->get();

        return view('guide.dashboard', compact('guide', 'tours', 'stats', 'upcoming'));
    }
}