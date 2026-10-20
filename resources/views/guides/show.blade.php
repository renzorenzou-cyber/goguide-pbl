@extends('layouts.site')

@section('title', $guide->user->name . ' - Guide di Go Go Guide')

@section('content')
@php
    $name      = $guide->user->name;
    $firstName = str($name)->before(' ');
@endphp

<div class="max-w-5xl mx-auto px-4 py-10">

    <a href="{{ url()->previous() === url()->current() ? route('home') : url()->previous() }}"
       class="text-sm text-brand-blue hover:underline">&larr; Kembali</a>

    {{-- HEADER PROFIL --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 mt-4">
        <div class="flex flex-col sm:flex-row gap-6 sm:items-center">
            @include('guides.partials.avatar', ['guide' => $guide, 'size' => 'w-32 h-32 text-4xl'])

            <div class="min-w-0">
                <h1 class="text-3xl font-bold">{{ $name }}</h1>

                @if($guide->specialty)
                    <p class="text-brand-blue font-semibold mt-1">{{ $guide->specialty }}</p>
                @endif

                @if($guide->base_location)
                    <p class="text-sm text-slate-500 mt-2">
                        <i class="fa-solid fa-location-dot text-brand-orange mr-1"></i>{{ $guide->base_location }}
                    </p>
                @endif

                @if(count($guide->language_list))
                    <div class="flex flex-wrap items-center gap-2 mt-3">
                        @foreach($guide->language_list as $lang)
                            <span class="text-xs font-semibold bg-brand-blue/10 text-brand-blue px-3 py-1 rounded-full">{{ $lang }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-3 gap-3 mt-8 text-center">
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xl font-extrabold text-brand-blue">⭐ {{ number_format($guide->rating, 1) }}</p>
                <p class="text-xs text-slate-500">{{ $guide->review_count }} review</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xl font-extrabold text-brand-blue">
                    {{ $guide->experience_years ? $guide->experience_years . ' tahun' : '-' }}
                </p>
                <p class="text-xs text-slate-500">Pengalaman</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xl font-extrabold text-brand-blue">{{ $guide->tours->count() }}</p>
                <p class="text-xs text-slate-500">Tour aktif</p>
            </div>
        </div>

        @if($guide->bio)
            <h2 class="font-bold text-lg mt-8">Tentang {{ $firstName }}</h2>
            <p class="text-slate-600 mt-2 leading-relaxed">{!! nl2br(e($guide->bio)) !!}</p>
        @endif
    </div>

    {{-- TOUR DARI GUIDE INI --}}
    <h2 class="font-bold text-xl mt-10">Tour bareng {{ $firstName }}</h2>

    @if($guide->tours->isEmpty())
        <p class="text-slate-500 mt-3 text-sm">Belum ada tour yang tersedia.</p>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 mt-4">
            @foreach($guide->tours as $t)
                <a href="{{ route('tours.show', $t->slug) }}"
                   class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition">
                    <div class="h-40 bg-slate-200">
                        @if($t->image_url)
                            <img src="{{ $t->image_url }}" alt="{{ $t->title }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="p-4">
                        <p class="text-brand-orange text-xs font-semibold uppercase">{{ $t->region->name ?? '' }}</p>
                        <p class="font-bold mt-1">{{ $t->title }}</p>
                        <div class="flex justify-between items-center text-sm mt-3">
                            <span class="text-slate-500">⭐ {{ $t->rating }} · {{ $t->duration }}</span>
                            <span class="font-bold">Rp {{ number_format($t->price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection