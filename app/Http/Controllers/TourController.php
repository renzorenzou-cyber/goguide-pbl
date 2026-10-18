<?php

namespace App\Http\Controllers;

use App\Models\Tour;

class TourController extends Controller
{
    public function show(string $slug)
    {
        $tour = Tour::with(['region', 'guide.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('tours.show', compact('tour'));
    }
}