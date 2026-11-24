@extends('layouts.site')

@section('title', 'Kelola Area - Go Guide')

@section('content')
@php
    $in  = 'mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20';
    $lbl = 'text-sm font-semibold text-slate-700';
    $buatBaru = old('_form') === 'create';
@endphp
<section class="py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard admin
        </a>
        <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900">Area</h1>
        <p class="text-sm text-slate-500 mt-1">Wilayah tempat objek wisata berada, misalnya Ubud atau Kintamani.</p>

        @include('admin.partials.destinasi-tabs')
        @include('admin.partials.flash')

        <form method="POST" action="{{ route('admin.area.store') }}" class="mt-6 bg-white rounded-2xl border border-slate-200 p-5">
            @csrf
            <input type="hidden" name="_form" value="create">
            <h2 class="font-bold text-slate-900 mb-3">Tambah area</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="a_nama" class="{{ $lbl }}">Nama area</label>
                    <input id="a_nama" name="nama_area" required class="{{ $in }}" placeholder="Contoh: Lovina"
                           value="{{ $buatBaru ? old('nama_area') : '' }}">
                </div>
                <div>
                    <label for="a_kab" class="{{ $lbl }}">Kabupaten / kota</label>
                    <input id="a_kab" name="kabupaten" required class="{{ $in }}" placeholder="Contoh: Buleleng"
                           value="{{ $buatBaru ? old('kabupaten') : '' }}">
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button class="px-5 py-2.5 rounded-xl bg-brand-orange hover:bg-orange-600 text-white text-sm font-bold shadow">Simpan area</button>
            </div>
        </form>

        <div class="mt-6 bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100">
            @forelse ($areas as $a)
                @php $buka = old('_edit') == $a->id_area; @endphp
                <div class="p-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-900">{{ $a->nama_area }}</p>
                            <p class="text-xs text-slate-500">{{ $a->kabupaten }} &middot; {{ $a->objek_count }} objek wisata</p>
                        </div>
                        <form method="POST" action="{{ route('admin.area.destroy', $a->id_area) }}"
                              onsubmit="return confirm('Hapus area ini?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1.5 rounded-lg border border-red-200 text-sm font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                        </form>
                    </div>

                    <details class="mt-3" @if($buka) open @endif>
                        <summary class="cursor-pointer text-sm font-bold text-brand-blue hover:text-brand-orange">Edit area</summary>
                        <form method="POST" action="{{ route('admin.area.update', $a->id_area) }}" class="mt-3 grid sm:grid-cols-2 gap-4">
                            @csrf @method('PUT')
                            <input type="hidden" name="_edit" value="{{ $a->id_area }}">
                            <div>
                                <label class="{{ $lbl }}">Nama area</label>
                                <input name="nama_area" required class="{{ $in }}" value="{{ $buka ? old('nama_area') : $a->nama_area }}">
                            </div>
                            <div>
                                <label class="{{ $lbl }}">Kabupaten / kota</label>
                                <input name="kabupaten" required class="{{ $in }}" value="{{ $buka ? old('kabupaten') : $a->kabupaten }}">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="{{ $lbl }}">Alamat (opsional)</label>
                                <input name="alamat" class="{{ $in }}" value="{{ $buka ? old('alamat') : $a->alamat }}">
                            </div>
                            <div>
                                <label class="{{ $lbl }}">Latitude (opsional)</label>
                                <input name="latitude" inputmode="decimal" class="{{ $in }}" value="{{ $buka ? old('latitude') : $a->latitude }}">
                            </div>
                            <div>
                                <label class="{{ $lbl }}">Longitude (opsional)</label>
                                <input name="longitude" inputmode="decimal" class="{{ $in }}" value="{{ $buka ? old('longitude') : $a->longitude }}">
                            </div>
                            <div class="sm:col-span-2 flex justify-end">
                                <button class="px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-darkBlue text-white text-sm font-bold">Simpan perubahan</button>
                            </div>
                        </form>
                    </details>
                </div>
            @empty
                <p class="p-8 text-center text-slate-600">Belum ada area. Tambahkan yang pertama di atas.</p>
            @endforelse
        </div>

        <div class="mt-6">{{ $areas->links('admin.partials.pagination') }}</div>
    </div>
</section>
@endsection