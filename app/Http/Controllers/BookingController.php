<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{

    public function index(Request $request)
{
    $bookings = Booking::with('tour.region')
        ->where('user_id', $request->user()->id)
        ->latest()
        ->get();

    return view('bookings.index', compact('bookings'));
}
    public function store(Request $request, string $slug)
    {
        $tour = Tour::where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'tour_date'    => ['required', 'date', 'after_or_equal:today'],
            'participants' => ['required', 'integer', 'min:1', 'max:20'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ]);

        // Cek guide sudah ke-book di tanggal yang sama atau belum
        $guideBusy = Booking::whereHas('tour', fn ($q) => $q->where('guide_id', $tour->guide_id))
            ->whereDate('tour_date', $data['tour_date'])
            ->whereIn('status', ['pending', 'paid'])
            ->exists();

        if ($guideBusy) {
            return back()
                ->withErrors(['tour_date' => 'Guide sudah ter-booking di tanggal ini, pilih tanggal lain.'])
                ->withInput();
        }

        // Total dihitung di server, jangan percaya angka dari browser
        $total = $tour->price * $data['participants'];

        $booking = Booking::create([
            'booking_code' => 'GGG-' . strtoupper(Str::random(8)),
            'user_id'      => $request->user()->id,
            'tour_id'      => $tour->id,
            'tour_date'    => $data['tour_date'],
            'participants' => $data['participants'],
            'total_price'  => $total,
            'status'       => 'pending',
            'notes'        => $data['notes'] ?? null,
        ]);

        return redirect()->route('bookings.show', $booking);
    }

    public function show(Request $request, Booking $booking)
    {
        // Booking cuma boleh dilihat pemiliknya
        abort_unless($booking->user_id === $request->user()->id, 403);

        $booking->load('tour.region', 'tour.guide.user');

        return view('bookings.show', compact('booking'));
    }
}