@extends('layouts.site')

@section('title', 'Semua Paket Wisata - GoGuide')

@section('content')
<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Beranda
        </a>

        <div class="mt-3 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <span class="text-brand-orange font-bold text-sm uppercase tracking-wider">Paket Wisata</span>
                <h1 class="text-3xl font-extrabold text-slate-900 mt-1">Semua Paket Perjalanan</h1>
                <p class="text-sm text-slate-500 mt-1">{{ $pakets->total() }} paket tersedia</p>
            </div>

            <form method="GET" action="{{ route('paket.index') }}" class="flex gap-2 w-full md:w-auto">
                @if(request('area')) <input type="hidden" name="area" value="{{ request('area') }}"> @endif
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari paket atau objek wisata"
                       class="w-full md:w-72 rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20">
                <button class="px-4 rounded-xl bg-brand-blue hover:bg-brand-darkBlue text-white text-sm font-bold">
                    <i class="fa-solid fa-magnifying-glass"></i><span class="sr-only">Cari</span>
                </button>
            </form>
        </div>

        {{-- Filter area --}}
        <div class="mt-6 flex flex-wrap gap-2">
            <a href="{{ route('paket.index', request()->only('q')) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold {{ !request('area') ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Semua</a>
            @foreach($areas as $area)
                <a href="{{ route('paket.index', array_merge(request()->only('q'), ['area' => $area->id_area])) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold {{ request('area') == $area->id_area ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $area->nama_area }}
                </a>
            @endforeach
        </div>

        @if(request('q'))
            <p class="mt-5 text-sm text-slate-500">
                Hasil untuk <span class="font-bold text-slate-800">"{{ request('q') }}"</span> &middot;
                <a href="{{ route('paket.index', request()->only('area')) }}" class="text-brand-blue font-semibold hover:underline">Reset</a>
            </p>
        @endif

        <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($pakets as $p)
                @include('partials.paket-card', ['p' => $p])
            @empty
                <p class="sm:col-span-2 lg:col-span-3 text-center text-slate-500 py-10">Belum ada paket untuk pilihan ini.</p>
            @endforelse
        </div>

        <div class="mt-10">{{ $pakets->links('admin.partials.pagination') }}</div>
    </div>
</section>
@endsection