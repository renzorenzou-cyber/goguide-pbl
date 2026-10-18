<?php

namespace App\Http\Controllers;

use App\Models\{Guide, Region, Tour};
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $regions = Region::orderBy('name')->get();

        $tours = Tour::with(['region', 'guide.user'])
            // Filter daerah (chip)
            ->when($request->region, fn ($query, $slug) =>
                $query->whereHas('region', fn ($r) => $r->where('slug', $slug)))
            // Search bar: judul, deskripsi, atau nama daerah
            ->when($request->q, function ($query, $term) {
                $query->where(function ($w) use ($term) {
                    $w->where('title', 'like', "%{$term}%")
                      ->orWhere('description', 'like', "%{$term}%")
                      ->orWhereHas('region', fn ($r) => $r->where('name', 'like', "%{$term}%"));
                });
            })
            ->get();

        $guides = Guide::with(['user', 'tours'])->orderByDesc('rating')->take(4)->get();

        // Data buat kartu hero
        $featured  = Tour::with(['region', 'guide.user'])->orderByDesc('rating')->first();
        $tourCount = Tour::count();
        $avgRating = round(Tour::avg('rating') ?? 0, 1);

        return view('home', compact('regions', 'tours', 'guides', 'featured', 'tourCount', 'avgRating'));
    }
}   