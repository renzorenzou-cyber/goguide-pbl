@extends('layouts.site')

@section('title', ($judul ?? 'Halaman') . ' - GoGuide')

@section('content')
<section class="py-20">
    <div class="max-w-xl mx-auto px-4 text-center">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-orange-100 text-brand-orange flex items-center justify-center text-2xl mb-5">
            <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900">{{ $judul ?? 'Halaman' }}</h1>
        <p class="text-slate-500 mt-2 text-sm">Halaman ini sedang disesuaikan dengan alur baru dan akan segera tersedia.</p>
        <a href="{{ route('home') }}" class="inline-block mt-6 px-5 py-2.5 rounded-xl bg-brand-blue text-white font-bold text-sm">
            Kembali ke beranda
        </a>
    </div>
</section>
@endsection