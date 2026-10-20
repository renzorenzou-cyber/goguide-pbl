<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Region;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $regions = Region::orderBy('name')->get();

        // Dropdown guide: hanya guide yang sudah punya review
        $guides = Guide::with('user')
            ->whereHas('reviews')
            ->get()
            ->sortBy(fn ($g) => $g->user->name);

        $regionSlug = $request->string('region')->toString();
        $guideId    = $request->integer('guide');

        $query = Review::with(['tour.region', 'guide.user'])
            ->when($regionSlug !== '', fn ($q) =>
                $q->whereHas('tour.region', fn ($r) => $r->where('slug', $regionSlug)))
            ->when($guideId > 0, fn ($q) => $q->where('guide_id', $guideId));

        $count = (clone $query)->count();
        $avg   = $count ? round((clone $query)->avg('rating'), 1) : 0;

        $reviews = $query->latest()->paginate(9)->withQueryString();

        return view('reviews.index', compact('regions', 'guides', 'reviews', 'count', 'avg'));
    }
}