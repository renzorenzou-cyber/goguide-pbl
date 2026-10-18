@extends('layouts.site')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden py-16 lg:py-28">
    <div class="absolute inset-0 bg-cover bg-center"
         style="background-image: url('{{ asset('images/cta.jpg') }}'), linear-gradient(135deg, #133961 0%, #1e5288 100%);"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-slate-900/85 via-slate-900/55 to-slate-900/20"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-12 gap-12 items-center">

            {{-- Kiri --}}
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-orange-100 border border-orange-200 text-brand-orange text-xs font-bold tracking-wide">
                    <i class="fa-solid fa-star"></i> Guide Lokal Terpercaya di Bali
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight drop-shadow-lg">
                    Explore Bali with <span class="text-sky-300">Certified Local</span>
                    <span class="text-orange-400 underline decoration-wavy decoration-orange-300">Experts.</span>
                </h1>

                <p class="text-slate-100 text-base sm:text-lg max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                    Skip tourist traps! Go Go Guide connects you with vetted, friendly local guides who show you
                    secret spots, rich culture, and authentic experiences tailored just for you.
                </p>

                {{-- Search bar --}}
                <form method="GET" action="{{ route('home') }}#destinations" class="pt-4 max-w-xl mx-auto lg:mx-0">
                    <div class="bg-white p-2 sm:p-3 rounded-2xl shadow-xl border border-slate-100 flex flex-col sm:flex-row gap-2">
                        <div class="flex-1 flex items-center px-3 py-2 bg-slate-50 rounded-xl border border-slate-200/60">
                            <i class="fa-solid fa-location-dot text-brand-orange text-lg mr-3"></i>
                            <input type="text" name="q" value="{{ request('q') }}"
                                   placeholder="Mau ke mana? (Ubud, Canggu, waterfall...)"
                                   class="w-full bg-transparent border-none focus:ring-0 text-slate-800 text-sm focus:outline-none placeholder-slate-400 font-medium p-0">
                        </div>
                        <button type="submit"
                                class="px-6 py-3.5 bg-brand-blue hover:bg-brand-darkBlue text-white font-bold rounded-xl shadow-md transition-all flex items-center justify-center gap-2 text-sm">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <span>Find Guide</span>
                        </button>
                    </div>
                </form>

                {{-- Ikon kategori --}}
                <div class="pt-6 flex flex-wrap items-center justify-center lg:justify-start gap-6 sm:gap-8">
                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-white/90"><i class="fa-solid fa-mountain text-lg"></i> Nature</span>
                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-white/90"><i class="fa-solid fa-landmark text-lg"></i> Culture</span>
                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-white/90"><i class="fa-solid fa-umbrella-beach text-lg"></i> Beach</span>
                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-white/90"><i class="fa-solid fa-water text-lg"></i> Waterfall</span>
                </div>
            </div>

            {{-- Kanan: kartu tour unggulan --}}
            @if($featured)
            <div class="lg:col-span-5 relative flex justify-center">
                <div class="relative w-full max-w-md">

                    <a href="{{ route('tours.show', $featured->slug) }}"
                       class="block relative z-10 bg-white p-4 rounded-3xl shadow-2xl border border-slate-100 transform rotate-1 hover:rotate-0 transition-transform duration-300">
                        <img src="{{ $featured->image ? asset($featured->image) : 'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?auto=format&fit=crop&w=800&q=80' }}"
                             alt="{{ $featured->title }}" class="rounded-2xl w-full h-80 object-cover">

                        <div class="mt-4 flex items-center justify-between gap-3">
                            <div>
                                <h4 class="font-bold text-slate-800 text-base">{{ $featured->title }}</h4>
                                <p class="text-xs text-slate-500 font-medium">
                                    <i class="fa-solid fa-user-check text-green-500 mr-1"></i>
                                    Guide: {{ $featured->guide->user->name }}
                                </p>
                            </div>
                            <span class="px-3 py-1 bg-green-100 text-green-700 font-bold text-xs rounded-full whitespace-nowrap">Top Pick</span>
                        </div>
                    </a>

                    <div class="absolute -top-6 -left-6 z-20 bg-white p-3.5 rounded-2xl shadow-lg border border-slate-100 flex items-center gap-3 animate-bounce" style="animation-duration: 3s;">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-brand-orange flex items-center justify-center">
                            <i class="fa-solid fa-shield-halved text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800">Vetted Guides</p>
                            <p class="text-[10px] text-slate-400">Verified identity & licenses</p>
                        </div>
                    </div>

                    <div class="absolute -bottom-6 -right-4 z-20 bg-brand-blue text-white p-3.5 rounded-2xl shadow-xl flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                            <i class="fa-solid fa-face-smile text-lg text-yellow-300"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold">{{ $avgRating }} / 5 Rating</p>
                            <p class="text-[10px] text-blue-200">{{ $tourCount }} tour di Bali</p>
                        </div>
                    </div>

                </div>
            </div>
            @endif

        </div>
    </div>
</section>

{{-- STATISTIK --}}
<section class="bg-white border-b border-slate-100">
    <div class="max-w-4xl mx-auto px-4 py-10 grid grid-cols-3 gap-4 text-center">
        <div>
            <p class="text-3xl font-extrabold text-brand-blue">{{ $tourCount }}</p>
            <p class="text-xs text-slate-500 font-medium mt-1">Tour tersedia</p>
        </div>
        <div>
            <p class="text-3xl font-extrabold text-brand-orange">{{ $regions->count() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-1">Daerah di Bali</p>
        </div>
        <div>
            <p class="text-3xl font-extrabold text-brand-blue">{{ $avgRating }}</p>
            <p class="text-xs text-slate-500 font-medium mt-1">Rating rata-rata</p>
        </div>
    </div>
</section>

{{-- POPULAR DESTINATIONS --}}
<section id="destinations" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
            <div>
                <span class="text-brand-orange font-bold text-sm uppercase tracking-wider">Top Destinations</span>
                <h2 class="text-3xl font-extrabold text-slate-900 mt-1">Explore Popular Places</h2>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('home') }}#destinations"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ !request('region') ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    All
                </a>
                @foreach($regions as $region)
                    <a href="{{ route('home', ['region' => $region->slug]) }}#destinations"
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('region') == $region->slug ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $region->name }}
                    </a>
                @endforeach
            </div>
        </div>

        @if(request('q'))
            <p class="mb-6 text-sm text-slate-500">
                Hasil untuk <span class="font-bold text-slate-800">"{{ request('q') }}"</span>
                ({{ $tours->count() }} tour) &middot;
                <a href="{{ route('home') }}#destinations" class="text-brand-blue font-semibold hover:underline">Reset</a>
            </p>
        @endif

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($tours as $tour)
                <div class="group bg-slate-50 rounded-2xl overflow-hidden border border-slate-100 hover:shadow-xl transition-all duration-300 flex flex-col">
                    <div class="relative overflow-hidden h-52">
                        @if($tour->image)
                            <img src="{{ asset($tour->image) }}" alt="{{ $tour->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-sky-300 via-sky-200 to-emerald-200 flex items-center justify-center">
                                <i class="fa-solid fa-umbrella-beach text-5xl text-white/80"></i>
                            </div>
                        @endif
                        <span class="absolute top-3 right-3 bg-white/90 backdrop-blur-md px-2.5 py-1 rounded-lg text-xs font-bold text-slate-700 shadow-sm">
                            <i class="fa-solid fa-star text-amber-400 mr-1"></i> {{ $tour->rating }} ({{ $tour->review_count }})
                        </span>
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold text-brand-orange uppercase">{{ $tour->region->name }}</span>
                            <h3 class="text-xl font-bold text-slate-900 mt-0.5">
                                <a href="{{ route('tours.show', $tour->slug) }}" class="hover:underline">{{ $tour->title }}</a>
                            </h3>
                            <p class="text-slate-500 text-xs mt-2 line-clamp-2">{{ $tour->description }}</p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-slate-200/60 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-slate-400 font-medium">Starting from</span>
                                <p class="text-lg font-extrabold text-brand-blue">
                                    Rp {{ number_format($tour->price, 0, ',', '.') }}
                                    <span class="text-xs font-normal text-slate-500">/ orang</span>
                                </p>
                            </div>
                            <a href="{{ route('tours.show', $tour->slug) }}"
                               class="px-4 py-2 bg-brand-blue/10 hover:bg-brand-blue hover:text-white text-brand-blue font-bold text-xs rounded-xl transition-colors">
                                Book Guide
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="sm:col-span-2 lg:col-span-3 text-center text-slate-500">Belum ada tour di daerah ini.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- TOP GUIDES --}}
<section id="guides" class="py-16 bg-slate-100/70 border-y border-slate-200/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-brand-orange font-bold text-sm uppercase tracking-wider">Meet The Experts</span>
            <h2 class="text-3xl font-extrabold text-slate-900 mt-1">Top Rated Local Guides</h2>
            <p class="text-slate-600 text-sm mt-2">Passionate locals ready to make your journey unforgettable.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($guides as $guide)
                @php
                    $name = $guide->user->name;
                    $initials = collect(explode(' ', $name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                    $firstTour = $guide->tours->first();
                @endphp

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center text-center hover:-translate-y-1 transition-transform">
                    @if($guide->photo)
                        <img src="{{ asset($guide->photo) }}" alt="{{ $name }}"
                             class="w-24 h-24 rounded-full object-cover guide-avatar-ring mb-4">
                    @else
                        <div class="w-24 h-24 rounded-full guide-avatar-ring mb-4 bg-brand-blue text-white flex items-center justify-center text-2xl font-extrabold">
                            {{ $initials }}
                        </div>
                    @endif

                    <h4 class="font-bold text-slate-800 text-lg">{{ $name }}</h4>
                    <p class="text-xs text-brand-blue font-semibold">{{ $guide->specialty }}</p>

                    <div class="flex items-center gap-1 my-2 text-xs font-bold text-slate-700">
                        <i class="fa-solid fa-star text-amber-400"></i>
                        {{ $guide->rating }} ({{ $guide->review_count }} reviews)
                    </div>

                    <p class="text-xs text-slate-500 mb-4 line-clamp-3">"{{ $guide->bio }}"</p>

                    @if($firstTour)
                        <a href="{{ route('tours.show', $firstTour->slug) }}"
                           class="w-full py-2 border border-slate-200 hover:border-brand-orange hover:text-brand-orange rounded-xl text-xs font-bold text-slate-700 transition-colors">
                            View Profile
                        </a>
                    @endif
                </div>
            @empty
                <p class="sm:col-span-2 lg:col-span-4 text-center text-slate-500">Belum ada guide.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- WHY GO GO GUIDE --}}
<section id="features" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-brand-orange font-bold text-sm uppercase tracking-wider">Why Choose Go Go Guide</span>
            <h2 class="text-3xl font-extrabold text-slate-900 mt-1">Travel Like a Local, Not a Tourist</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col items-start">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-brand-blue flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Vetted & Licensed Guides</h3>
                <p class="text-slate-600 text-sm mt-2 leading-relaxed">Setiap guide melewati pengecekan latar belakang, wawancara, dan tes kemampuan bahasa.</p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col items-start">
                <div class="w-12 h-12 rounded-xl bg-orange-100 text-brand-orange flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-sliders"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Customized Itineraries</h3>
                <p class="text-slate-600 text-sm mt-2 leading-relaxed">Atur tour sesuai preferensi, budget, dan minat perjalananmu lewat catatan saat booking.</p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col items-start">
                <div class="w-12 h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Secure & Fair Pricing</h3>
                <p class="text-slate-600 text-sm mt-2 leading-relaxed">Harga transparan tanpa biaya tersembunyi, pembayaran aman lewat payment gateway.</p>
            </div>
        </div>
    </div>
</section>

{{-- BANNER AJAKAN (tanpa foto) --}}
<section class="relative py-20 overflow-hidden"
         style="background: linear-gradient(135deg, #133961 0%, #1e5288 100%);">
    <div class="relative z-10 max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white">
            Siap Jelajahi Bali Bareng Guide Lokal?
        </h2>
        <p class="text-blue-100 mt-3 text-sm sm:text-base">
            Pilih tour, tentukan tanggal, dan biarkan guide lokal kami yang atur sisanya.
        </p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('home') }}#destinations"
               class="px-6 py-3 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-bold shadow-lg transition-colors">
                Lihat Semua Tour
            </a>
            @guest
                <a href="{{ route('register') }}"
                   class="px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 border border-white/30 text-white font-bold transition-colors">
                    Daftar Gratis
                </a>
            @endguest
        </div>
    </div>
</section>

@endsection