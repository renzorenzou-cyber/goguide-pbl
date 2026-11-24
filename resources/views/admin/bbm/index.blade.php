@extends('layouts.site')

@section('title', 'Catatan BBM - Go Go Guide')

@section('content')
@php
    $rp  = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $in  = 'mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20';
    $lbl = 'text-sm font-semibold text-slate-700';
@endphp
<section class="py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard admin
        </a>
        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Catatan BBM Operasional</h1>
            <a href="{{ route('admin.fee.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold hover:border-brand-orange hover:text-brand-orange">
                Rekap fee guide
            </a>
        </div>
        <p class="text-sm text-slate-500 mt-1">Biaya BBM dicatat per booking untuk rekap admin dan tidak mengubah total bayar wisatawan.</p>

        @include('admin.partials.flash')

        {{-- Ringkasan bulan terpilih --}}
        <div class="grid sm:grid-cols-3 gap-4 mt-6">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Total biaya BBM bulan ini</p>
                <p class="text-2xl font-extrabold text-brand-orange mt-1">{{ $rp($ringkas['total_biaya']) }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Total liter</p>
                <p class="text-2xl font-extrabold text-brand-blue mt-1">{{ rtrim(rtrim(number_format($ringkas['total_liter'], 2, ',', '.'), '0'), ',') }} L</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Jumlah catatan</p>
                <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ $ringkas['jumlah'] }}</p>
            </div>
        </div>

        {{-- Form tambah catatan --}}
        <form method="POST" action="{{ route('admin.bbm.store') }}" class="mt-6 bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
            @csrf
            <h2 class="font-bold text-slate-900">Tambah catatan BBM</h2>

            <div class="mt-4 grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="id_booking" class="{{ $lbl }}">Booking</label>
                    <select id="id_booking" name="id_booking" required class="{{ $in }}">
                        <option value="">Pilih booking (dikonfirmasi atau selesai)</option>
                        @foreach ($bookings as $b)
                            <option value="{{ $b->id_booking }}" @selected((string) old('id_booking') === (string) $b->id_booking)>
                                {{ $b->kode_booking }} &middot; {{ $b->paket?->nama_paket }} &middot; {{ $b->guide?->nama_guide }} &middot; {{ $b->tanggal_booking->format('d M Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="jenis_bahan_bakar" class="{{ $lbl }}">Jenis bahan bakar</label>
                    <input id="jenis_bahan_bakar" name="jenis_bahan_bakar" list="jenis-bbm" required maxlength="50" class="{{ $in }}"
                           value="{{ old('jenis_bahan_bakar') }}" placeholder="Pertalite, Pertamax, ...">
                    <datalist id="jenis-bbm">
                        @foreach (['Pertalite', 'Pertamax', 'Pertamax Turbo', 'Dexlite', 'Pertamina Dex', 'Solar'] as $j)
                            <option value="{{ $j }}">
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label for="tanggal_isi" class="{{ $lbl }}">Tanggal pengisian</label>
                    <input id="tanggal_isi" name="tanggal_isi" type="date" required class="{{ $in }}" value="{{ old('tanggal_isi', now()->toDateString()) }}">
                </div>
                <div>
                    <label for="jumlah_liter" class="{{ $lbl }}">Jumlah liter</label>
                    <input id="jumlah_liter" name="jumlah_liter" type="number" step="0.01" min="0.1" required class="{{ $in }}" value="{{ old('jumlah_liter') }}">
                </div>
                <div>
                    <label for="harga_per_liter" class="{{ $lbl }}">Harga per liter (Rp)</label>
                    <input id="harga_per_liter" name="harga_per_liter" type="number" step="1" min="1" required class="{{ $in }}" value="{{ old('harga_per_liter') }}" placeholder="10000">
                </div>
                <div class="sm:col-span-2">
                    <label for="catatan" class="{{ $lbl }}">Catatan (opsional)</label>
                    <input id="catatan" name="catatan" maxlength="255" class="{{ $in }}" value="{{ old('catatan') }}" placeholder="Misalnya rute antar jemput">
                </div>
            </div>

            <div class="mt-5 flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100">
                <p class="text-sm text-slate-600">Total biaya: <span id="prakiraan-total" class="font-extrabold text-brand-blue">Rp0</span></p>
                <button class="px-6 py-2.5 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-bold text-sm shadow">Simpan catatan</button>
            </div>
        </form>

        {{-- Filter dan daftar --}}
        <form method="GET" class="mt-8 flex flex-wrap items-end gap-3">
            <div>
                <label for="bulan" class="text-xs font-semibold text-slate-600">Bulan</label>
                <input id="bulan" name="bulan" type="month" value="{{ $bulan }}" onchange="this.form.submit()"
                       class="mt-1 block rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-orange">
            </div>
            <div class="flex-1 min-w-[12rem] max-w-sm">
                <label for="q" class="text-xs font-semibold text-slate-600">Cari kode booking</label>
                <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="GGG-..."
                       class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-orange">
            </div>
            <button class="px-4 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold hover:border-brand-orange hover:text-brand-orange">Terapkan</button>
        </form>

        <div class="mt-4 bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                        <tr>
                            <th class="text-left px-5 py-3">Tanggal isi</th>
                            <th class="text-left px-5 py-3">Booking</th>
                            <th class="text-left px-5 py-3">Bahan bakar</th>
                            <th class="text-right px-5 py-3">Total</th>
                            <th class="text-left px-5 py-3">Catatan</th>
                            <th class="text-center px-5 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($catatan as $c)
                            <tr class="align-top">
                                <td class="px-5 py-3 whitespace-nowrap">{{ $c->tanggal_isi->format('d M Y') }}</td>
                                <td class="px-5 py-3">
                                    <p class="font-mono text-xs">{{ $c->booking?->kode_booking }}</p>
                                    <p class="font-semibold">{{ $c->booking?->paket?->nama_paket }}</p>
                                    <p class="text-xs text-slate-500">{{ $c->booking?->guide?->nama_guide }}</p>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <p class="font-semibold">{{ $c->jenis_bahan_bakar }}</p>
                                    <p class="text-xs text-slate-500">{{ rtrim(rtrim(number_format($c->jumlah_liter, 2, ',', '.'), '0'), ',') }} L &times; {{ $rp($c->harga_per_liter) }}</p>
                                </td>
                                <td class="px-5 py-3 text-right whitespace-nowrap font-semibold">{{ $rp($c->total_biaya) }}</td>
                                <td class="px-5 py-3 text-slate-500">{{ $c->catatan ?: '-' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <form method="POST" action="{{ route('admin.bbm.destroy', $c->id_bahan_bakar) }}"
                                          onsubmit="return confirm('Hapus catatan BBM ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg text-xs font-bold border border-red-300 text-red-700 hover:bg-red-50">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">Belum ada catatan BBM pada bulan ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">{{ $catatan->links('admin.partials.pagination') }}</div>
    </div>
</section>

<script>
    (function () {
        const liter = document.getElementById('jumlah_liter');
        const harga = document.getElementById('harga_per_liter');
        const total = document.getElementById('prakiraan-total');

        function hitung() {
            const v = (parseFloat(liter.value) || 0) * (parseInt(harga.value) || 0);
            total.textContent = 'Rp' + Math.round(v).toLocaleString('id-ID');
        }
        liter.addEventListener('input', hitung);
        harga.addEventListener('input', hitung);
        hitung();
    })();
</script>
@endsection