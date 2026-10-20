<?php

namespace App\Http\Controllers;

use App\Models\Guide;

class GuideController extends Controller
{
    public function show(Guide $guide)
    {
        $guide->load(['user', 'tours.region']);

        return view('guides.show', compact('guide'));
    }
}