<?php

namespace App\Http\Controllers;

use App\Models\Tour;

class TourController extends Controller
{
    public function show(string $slug)
    {
        $tour = Tour::with(['region', 'guide.user', 'guide.tours', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Tour lain dari guide yang sama (buat kartu guide)
        $otherTours = $tour->guide->tours->where('id', '!=', $tour->id)->values();

        return view('tours.show', compact('tour', 'otherTours'));
    }
}