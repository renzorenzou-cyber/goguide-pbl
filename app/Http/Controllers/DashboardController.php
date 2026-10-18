<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $base = Booking::where('user_id', $user->id);

        $stats = [
            'total'   => (clone $base)->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'paid'    => (clone $base)->where('status', 'paid')->count(),
        ];

        $upcoming = Booking::with('tour.region')
            ->where('user_id', $user->id)
            ->where('status', 'paid')
            ->whereDate('tour_date', '>=', today())
            ->orderBy('tour_date')
            ->first();

        $recent = Booking::with('tour.region')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'upcoming', 'recent'));
    }
}