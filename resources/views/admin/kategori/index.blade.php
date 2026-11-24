@extends('layouts.site')

@section('title', 'Kelola Kategori - GoGuide')

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
        <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900">Kategori</h1>
        <p class="text-sm text-slate-500 mt-1">Jenis objek wisata, misalnya Gunung, Pantai, atau Budaya.</p>

        @include('admin.partials.destinasi-tabs')
        @include('admin.partials.flash')

        <form method="POST" action="{{ route('admin.kategori.store') }}" class="mt-6 bg-white rounded-2xl border border-slate-200 p-5">
            @csrf
            <input type="hidden" name="_form" value="create">
            <h2 class="font-bold text-slate-900 mb-3">Tambah kategori</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="k_nama" class="{{ $lbl }}">Nama kategori</label>
                    <input id="k_nama" name="nama_kategori" required class="{{ $in }}" placeholder="Contoh: Snorkeling"
                           value="{{ $buatBaru ? old('nama_kategori') : '' }}">
                </div>
                <div>
                    <label for="k_desk" class="{{ $lbl }}">Deskripsi (opsional)</label>
                    <input id="k_desk" name="deskripsi" class="{{ $in }}" value="{{ $buatBaru ? old('deskripsi') : '' }}">
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button class="px-5 py-2.5 rounded-xl bg-brand-orange hover:bg-orange-600 text-white text-sm font-bold shadow">Simpan kategori</button>
            </div>
        </form>

        <div class="mt-6 bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100">
            @forelse ($kategoris as $k)
                @php $buka = old('_edit') == $k->id_kategori; @endphp
                <div class="p-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-900">{{ $k->nama_kategori }}</p>
                            <p class="text-xs text-slate-500">{{ $k->objek_count }} objek wisata @if($k->deskripsi)&middot; {{ $k->deskripsi }}@endif</p>
                        </div>
                        <form method="POST" action="{{ route('admin.kategori.destroy', $k->id_kategori) }}"
                              onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1.5 rounded-lg border border-red-200 text-sm font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                        </form>
                    </div>

                    <details class="mt-3" @if($buka) open @endif>
                        <summary class="cursor-pointer text-sm font-bold text-brand-blue hover:text-brand-orange">Edit kategori</summary>
                        <form method="POST" action="{{ route('admin.kategori.update', $k->id_kategori) }}" class="mt-3 grid sm:grid-cols-2 gap-4">
                            @csrf @method('PUT')
                            <input type="hidden" name="_edit" value="{{ $k->id_kategori }}">
                            <div>
                                <label class="{{ $lbl }}">Nama kategori</label>
                                <input name="nama_kategori" required class="{{ $in }}" value="{{ $buka ? old('nama_kategori') : $k->nama_kategori }}">
                            </div>
                            <div>
                                <label class="{{ $lbl }}">Deskripsi (opsional)</label>
                                <input name="deskripsi" class="{{ $in }}" value="{{ $buka ? old('deskripsi') : $k->deskripsi }}">
                            </div>
                            <div class="sm:col-span-2 flex justify-end">
                                <button class="px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-darkBlue text-white text-sm font-bold">Simpan perubahan</button>
                            </div>
                        </form>
                    </details>
                </div>
            @empty
                <p class="p-8 text-center text-slate-600">Belum ada kategori. Tambahkan yang pertama di atas.</p>
            @endforelse
        </div>

        <div class="mt-6">{{ $kategoris->links('admin.partials.pagination') }}</div>
    </div>
</section>
@endsection