@extends('layouts.site')

@section('title', 'Jadwal Layanan - GoGuide')

@section('content')
@php
    $in  = 'mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20';
    $lbl = 'text-sm font-semibold text-slate-700';
@endphp
<section class="py-10 sm:py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <a href="{{ route('guide.dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard
        </a>
        <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900">Jadwal Layanan</h1>
        <p class="text-sm text-slate-500 mt-1">Isi tanggal ketika kamu siap menerima tour. Wisatawan hanya bisa memesanmu pada tanggal aktif yang belum dipesan orang lain.</p>

        @include('admin.partials.flash')

        <form method="POST" action="{{ route('guide.jadwal.store') }}" class="mt-6 bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
            @csrf
            <h2 class="font-bold text-slate-900">Tambah tanggal aktif</h2>
            <p class="text-xs text-slate-500">Isi satu tanggal, atau rentang tanggal. Tanggal yang sudah ada akan diperbarui jam layanannya.</p>

            <div class="mt-4 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="dari" class="{{ $lbl }}">Dari tanggal</label>
                    <input id="dari" name="dari" type="date" min="{{ $besok }}" required value="{{ old('dari') }}" class="{{ $in }}">
                </div>
                <div>
                    <label for="sampai" class="{{ $lbl }}">Sampai tanggal (opsional)</label>
                    <input id="sampai" name="sampai" type="date" min="{{ $besok }}" value="{{ old('sampai') }}" class="{{ $in }}">
                </div>
                <div>
                    <label for="jam_mulai" class="{{ $lbl }}">Jam mulai layanan</label>
                    <input id="jam_mulai" name="jam_mulai" type="time" required value="{{ old('jam_mulai', $jamMulai) }}" class="{{ $in }}">
                </div>
                <div>
                    <label for="jam_selesai" class="{{ $lbl }}">Jam selesai layanan</label>
                    <input id="jam_selesai" name="jam_selesai" type="time" required value="{{ old('jam_selesai', $jamSelesai) }}" class="{{ $in }}">
                </div>
                <div class="sm:col-span-2 lg:col-span-4">
                    <label for="keterangan" class="{{ $lbl }}">Catatan (opsional)</label>
                    <input id="keterangan" name="keterangan" maxlength="255" value="{{ old('keterangan') }}" class="{{ $in }}" placeholder="Misalnya: hanya area Ubud">
                </div>
            </div>

            <div class="flex justify-end mt-5">
                <button class="px-6 py-3 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-bold text-sm shadow">Simpan jadwal</button>
            </div>
        </form>

        <div class="mt-8 bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-bold text-slate-900">Tanggal aktif yang akan datang</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                        <tr>
                            <th class="text-left px-5 py-3">Tanggal</th>
                            <th class="text-left px-5 py-3">Jam layanan</th>
                            <th class="text-left px-5 py-3">Catatan</th>
                            <th class="text-center px-5 py-3">Status</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($jadwal as $j)
                            @php $dipesan = in_array($j->tanggal_aktif->toDateString(), $terpesan, true); @endphp
                            <tr>
                                <td class="px-5 py-3 whitespace-nowrap font-semibold">{{ $j->tanggal_aktif->translatedFormat('D, d M Y') }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</td>
                                <td class="px-5 py-3 text-slate-500">{{ $j->keterangan ?: '-' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold {{ $dipesan ? 'bg-orange-100 text-orange-700' : 'bg-emerald-50 text-emerald-700' }}">
                                        {{ $dipesan ? 'Dipesan' : 'Tersedia' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    @unless($dipesan)
                                        <form method="POST" action="{{ route('guide.jadwal.destroy', $j->id_jadwal) }}" onsubmit="return confirm('Hapus tanggal ini dari jadwal?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs font-bold text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">Belum ada tanggal aktif. Tambahkan di atas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection