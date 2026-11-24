@extends('layouts.site')

@section('title', 'Verifikasi Guide - Go Guide')

@section('content')
@php
    $tabs = ['Menunggu' => 'Menunggu', 'Terverifikasi' => 'Terverifikasi', 'Ditolak' => 'Ditolak'];
    $warna = ['Menunggu' => 'bg-yellow-100 text-yellow-700', 'Terverifikasi' => 'bg-emerald-100 text-emerald-700', 'Ditolak' => 'bg-red-100 text-red-700'];
@endphp
<section class="py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard admin
        </a>
        <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900">Verifikasi Guide</h1>
        <p class="text-sm text-slate-500 mt-1">Periksa pendaftaran calon guide berdasarkan indikator kelayakan.</p>

        @if (session('success'))
            <div role="status" class="mt-5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div role="alert" class="mt-5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3">{{ session('error') }}</div>
        @endif

        <nav class="mt-5 flex flex-wrap gap-2" aria-label="Status verifikasi">
            @foreach ($tabs as $kode => $label)
                <a href="{{ route('admin.guide.index', ['status' => $kode]) }}" @if($tab === $kode) aria-current="page" @endif
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition
                          {{ $tab === $kode ? 'bg-brand-blue text-white border-brand-blue' : 'bg-white text-slate-600 border-slate-200 hover:border-brand-orange hover:text-brand-orange' }}">
                    {{ $label }}
                    <span class="px-1.5 rounded-md text-[11px] {{ $tab === $kode ? 'bg-white/20' : 'bg-slate-100' }}">{{ $jumlah[$kode] ?? 0 }}</span>
                </a>
            @endforeach
        </nav>

        <form method="GET" class="mt-4 flex gap-2">
            <input type="hidden" name="status" value="{{ $tab }}">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email"
                   class="w-full max-w-sm rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20">
            <button class="px-4 rounded-xl border border-slate-300 bg-white text-sm font-semibold hover:border-brand-orange hover:text-brand-orange">Cari</button>
        </form>

        <div class="mt-5 bg-white rounded-2xl border border-slate-200 overflow-hidden">
            @if ($guides->isEmpty())
                <p class="p-10 text-center text-slate-600">Tidak ada data untuk status ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                            <tr>
                                <th class="text-left px-5 py-3">Nama</th>
                                <th class="text-left px-5 py-3">Email</th>
                                <th class="text-left px-5 py-3">WhatsApp</th>
                                <th class="text-left px-5 py-3">Mendaftar</th>
                                <th class="text-center px-5 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($guides as $g)
                                <tr>
                                    <td class="px-5 py-3 font-semibold">
                                        {{ $g->nama_guide }}
                                        @if ($g->status_verifikasi === 'Menunggu' && filled($g->alasan_penolakan))
                                            <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700">Daftar ulang</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">{{ $g->email }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap">{{ $g->no_wa ?: '-' }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap">{{ $g->created_at?->format('d M Y') }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <a href="{{ route('admin.guide.show', $g->id_guide) }}"
                                           class="inline-block px-3 py-1.5 rounded-lg text-xs font-bold {{ $g->status_verifikasi === 'Menunggu' ? 'bg-brand-orange text-white hover:bg-orange-600' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                                            {{ $g->status_verifikasi === 'Menunggu' ? 'Periksa' : 'Lihat' }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="mt-6">{{ $guides->links('admin.partials.pagination') }}</div>
    </div>
</section>
@endsection