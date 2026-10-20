@extends('layouts.site')

@section('title', 'Reviews - Go Go Guide')

@section('content')
<section class="py-12 sm:py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <div class="text-center max-w-2xl mx-auto">
            <span class="text-brand-orange font-bold text-sm uppercase tracking-wider">Reviews</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-1">Kata Mereka yang Sudah Jalan</h1>
            <p class="text-slate-500 text-sm mt-2">Lihat pengalaman traveler berdasarkan tempat dan guide.</p>
        </div>

        {{-- Ringkasan --}}
        <div class="mt-8 max-w-md mx-auto bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center justify-center gap-5">
            <p class="text-5xl font-extrabold text-brand-blue">{{ $count ? number_format($avg, 1) : '-' }}</p>
            <div>
                <div class="text-amber-400 text-sm">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star {{ $i <= round($avg) ? '' : 'text-slate-200' }}"></i>
                    @endfor
                </div>
                <p class="text-xs text-slate-500 mt-1">dari {{ $count }} review</p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="mt-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('reviews.index', array_filter(['guide' => request('guide')])) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ !request('region') ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua tempat
                </a>
                @foreach($regions as $region)
                    <a href="{{ route('reviews.index', array_filter(['region' => $region->slug, 'guide' => request('guide')])) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('region') == $region->slug ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $region->name }}
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('reviews.index') }}">
                @if(request('region'))
                    <input type="hidden" name="region" value="{{ request('region') }}">
                @endif
                <select name="guide" onchange="this.form.submit()"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 focus:border-brand-blue focus:ring-brand-blue">
                    <option value="">Semua guide</option>
                    @foreach($guides as $g)
                        <option value="{{ $g->id }}" @selected((string) request('guide') === (string) $g->id)>
                            {{ $g->user->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- Daftar review --}}
        <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($reviews as $review)
                @php
                    $initials = collect(explode(' ', $review->reviewer_name))
                        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');
                @endphp
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex flex-col">
                    <div class="text-amber-400 text-sm">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star {{ $i <= $review->rating ? '' : 'text-slate-200' }}"></i>
                        @endfor
                    </div>

                    <p class="text-sm text-slate-600 mt-3 flex-1">"{{ $review->comment }}"</p>

                    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand-blue text-white flex items-center justify-center text-xs font-extrabold shrink-0">
                            {{ $initials }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-sm text-slate-900 truncate">{{ $review->reviewer_name }}</p>
                            <p class="text-xs text-slate-400">{{ $review->created_at->format('d M Y') }}</p>
                        </div>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2 text-xs">
                        <a href="{{ route('tours.show', $review->tour->slug) }}"
                           class="px-2.5 py-1 rounded-lg bg-orange-50 text-brand-orange font-semibold hover:bg-orange-100">
                            <i class="fa-solid fa-location-dot mr-1"></i>{{ $review->tour->title }}
                        </a>
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-semibold">
                            <i class="fa-solid fa-user-check mr-1"></i>{{ $review->guide->user->name }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="sm:col-span-2 lg:col-span-3 text-center text-slate-500 py-10">
                    Belum ada review untuk pilihan ini.
                </p>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $reviews->links() }}
        </div>

    </div>
</section>
@endsection