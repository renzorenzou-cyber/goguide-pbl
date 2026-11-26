@extends('layouts.site')

@section('title', 'Daftar Guide - GoGuide')

@section('content')
<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Beranda
        </a>

        <div class="mt-3">
            <span class="text-brand-orange font-bold text-sm uppercase tracking-wider">Guide Kami</span>
            <h1 class="text-3xl font-extrabold text-slate-900 mt-1">Pilih Guide Lokal Terverifikasi</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $guides->total() }} guide ditemukan</p>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('guides.index') }}" class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama guide"
                   class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20">

            <select name="bahasa" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white">
                <option value="">Semua bahasa</option>
                @foreach($daftarBahasa as $b)
                    <option value="{{ $b->id_bahasa }}" @selected(request('bahasa') == $b->id_bahasa)>{{ $b->nama_bahasa }}</option>
                @endforeach
            </select>

            <select name="paket" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white">
                <option value="">Semua paket</option>
                @foreach($daftarPaket as $p)
                    <option value="{{ $p->id_paket }}" @selected(request('paket') == $p->id_paket)>{{ $p->nama_paket }}</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button class="flex-1 px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-darkBlue text-white text-sm font-bold">
                    <i class="fa-solid fa-magnifying-glass mr-1"></i> Cari
                </button>
                @if(request()->hasAny(['q', 'bahasa', 'paket']))
                    <a href="{{ route('guides.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-sm font-bold">Reset</a>
                @endif
            </div>
        </form>

        {{-- Kartu guide --}}
        <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($guides as $g)
                @php
                    $inisial = collect(explode(' ', $g->nama_guide))->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');
                    $hargaMin = $g->paket->min('harga_paket');
                @endphp
                <a href="{{ route('guides.show', $g) }}"
                   class="block bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-xl transition-all">
                    <div class="flex items-center gap-4">
                        @if($g->foto_url)
                            <img src="{{ $g->foto_url }}" alt="{{ $g->nama_guide }}" class="w-20 h-20 rounded-full object-cover guide-avatar-ring shrink-0">
                        @else
                            <div class="w-20 h-20 rounded-full guide-avatar-ring shrink-0 bg-brand-blue text-white flex items-center justify-center text-2xl font-extrabold">{{ $inisial }}</div>
                        @endif
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-900 text-lg truncate">{{ $g->nama_guide }}</h3>
                            <p class="text-xs font-bold text-slate-700 mt-1">
                                <i class="fa-solid fa-star text-amber-400"></i> {{ number_format($g->rating, 1) }}
                            </p>
                        </div>
                    </div>

                    <p class="text-sm text-slate-500 mt-4 line-clamp-3">{{ $g->bio_singkat }}</p>

                    <div class="flex flex-wrap gap-1.5 mt-4">
                        @foreach($g->bahasa as $b)
                            <span class="px-2.5 py-1 rounded-full bg-blue-100 text-brand-blue text-[11px] font-semibold">
                                {{ $b->nama_bahasa }} &middot; {{ $b->pivot->tingkat_kemampuan }}
                            </span>
                        @endforeach
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-500">{{ $g->paket->count() }} paket ditangani</span>
                        @if($hargaMin)
                            <span class="font-bold text-brand-blue">mulai Rp{{ number_format($hargaMin, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </a>
            @empty
                <p class="sm:col-span-2 lg:col-span-3 text-center text-slate-500 py-10">Belum ada guide untuk pilihan ini.</p>
            @endforelse
        </div>

        <div class="mt-10">{{ $guides->links('admin.partials.pagination') }}</div>
    </div>
</section>
@endsection