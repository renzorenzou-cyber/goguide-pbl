<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Tour;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()9999
    {
        $stats = [
            'bookings'  => Booking::count(),
            'pending'   => Booking::where('status', 'pending')->count(),
            'paid'      => Booking::where('status', 'paid')->count(),
            'revenue'   => Booking::where('status', 'paid')->sum('total_price'),
            'customers' => User::where('role', 'customer')->count(),
            'tours'     => Tour::count(),
        ];

        $recent = Booking::with(['user', 'tour'])->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recent'));
    }
}