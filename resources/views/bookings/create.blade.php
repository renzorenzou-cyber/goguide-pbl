@extends('layouts.site')

@section('title', 'Booking ' . $paket->nama_paket . ' - GoGuide')

@section('content')
@php
    $in  = 'mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20';
    $lbl = 'text-sm font-semibold text-slate-700';
    $err = fn ($f) => $errors->first($f) ? '<p class="mt-1 text-xs font-semibold text-red-600">' . e($errors->first($f)) . '</p>' : '';
    $jamDefault = old('jam_mulai', config('goguide.jam_mulai_default'));
@endphp

<section class="py-10 sm:py-14">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        <a href="{{ route('paket.show', ['paket' => $paket->id_paket, 'guide' => $guide->id_guide, 'tanggal' => $tanggal->toDateString()]) }}#cek"
           class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Ubah guide atau tanggal
        </a>
        <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900">Lengkapi Booking</h1>

        @if ($errors->any())
            <div role="alert" class="mt-5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3">
                <p class="font-bold mb-1">Booking belum bisa dibuat:</p>
                <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('booking.store') }}" class="mt-6 grid lg:grid-cols-3 gap-6">
            @csrf
            <input type="hidden" name="paket" value="{{ $paket->id_paket }}">
            <input type="hidden" name="guide" value="{{ $guide->id_guide }}">
            <input type="hidden" name="tanggal" value="{{ $tanggal->toDateString() }}">

            {{-- Kiri: data booking + S&K --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 space-y-5">
                    <h2 class="font-bold text-slate-900">Detail perjalanan</h2>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="jam_mulai" class="{{ $lbl }}">Jam mulai tour</label>
                            <input id="jam_mulai" name="jam_mulai" type="time" required class="{{ $in }}" value="{{ $jamDefault }}">
                            <p class="mt-1 text-xs text-slate-500">Estimasi selesai: <span id="estimasi" class="font-bold text-slate-700">-</span></p>
                        </div>
                        <div>
                            <label for="jumlah_peserta" class="{{ $lbl }}">Jumlah peserta</label>
                            <input id="jumlah_peserta" name="jumlah_peserta" type="number" min="1" max="{{ config('goguide.max_peserta') }}"
                                   required class="{{ $in }}" value="{{ old('jumlah_peserta', 1) }}">
                        </div>
                    </div>

                    <div>
    <label for="telepon" class="{{ $lbl }}">Nomor HP / WhatsApp</label>
    <input id="telepon" name="telepon" type="tel" inputmode="tel" required class="{{ $in }}"
           value="{{ old('telepon', $telepon) }}" placeholder="081234567890">
    <p class="mt-1 text-xs text-slate-500">Dipakai guide untuk koordinasi penjemputan.</p>
</div>

                    <div>
                        <label for="catatan" class="{{ $lbl }}">Catatan (opsional)</label>
                        <textarea id="catatan" name="catatan" rows="3" maxlength="500" class="{{ $in }}"
                                  placeholder="Lokasi dan waktu penjemputan, permintaan khusus, dll.">{{ old('catatan') }}</textarea>
                    </div>
                </div>

                {{-- Syarat dan Ketentuan --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
                    <h2 class="font-bold text-slate-900">Syarat dan Ketentuan</h2>

                    <h3 class="mt-4 text-sm font-bold text-brand-blue">A. Waktu tour</h3>
                    <ul class="mt-2 list-decimal pl-5 text-sm text-slate-600 space-y-1">
                        <li>Jam mulai tour dipilih pada form ini (bawaan {{ config('goguide.jam_mulai_default') }}).</li>
                        <li>Durasi mengikuti paket ({{ $paket->durasi }}). Estimasi selesai = jam mulai + durasi.</li>
                        <li>Jam mulai dan estimasi selesai harus berada dalam jam layanan guide pada tanggal tersebut.</li>
                        <li>Toleransi keterlambatan wisatawan {{ config('goguide.toleransi_telat_menit') }} menit; selebihnya waktu tour dapat berkurang tanpa pengurangan biaya.</li>
                        <li>Lokasi dan waktu penjemputan dikonfirmasi pada catatan booking.</li>
                    </ul>

                    <h3 class="mt-5 text-sm font-bold text-brand-blue">B. Pembatalan</h3>
                    <div class="mt-2 overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-left text-xs text-slate-500">
                                <tr><th class="px-3 py-2 font-semibold">Kondisi</th><th class="px-3 py-2 font-semibold">Ketentuan</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                <tr><td class="px-3 py-2">Belum dibayar sampai batas bayar ({{ config('goguide.batas_bayar_jam') }} jam)</td><td class="px-3 py-2">Dibatalkan otomatis, tanpa biaya</td></tr>
                                <tr><td class="px-3 py-2">Dibatalkan minimal {{ config('goguide.refund.0.min_hari') }} hari sebelum tour</td><td class="px-3 py-2">Pengembalian dana {{ config('goguide.refund.0.persen') }}%</td></tr>
                                <tr><td class="px-3 py-2">Dibatalkan {{ config('goguide.refund.1.min_hari') }} sampai {{ config('goguide.refund.0.min_hari') - 1 }} hari sebelum tour</td><td class="px-3 py-2">Pengembalian dana {{ config('goguide.refund.1.persen') }}%</td></tr>
                                <tr><td class="px-3 py-2">Dibatalkan pada hari pelaksanaan atau tidak hadir</td><td class="px-3 py-2">Tidak ada pengembalian dana</td></tr>
                                <tr><td class="px-3 py-2">Dibatalkan oleh admin atau guide</td><td class="px-3 py-2">Penjadwalan ulang atau pengembalian dana penuh</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">Pengembalian dana diproses manual oleh admin. Setiap pembatalan mencatat alasan dan memicu notifikasi.</p>

                    <label class="mt-5 flex items-start gap-3 rounded-xl border border-slate-200 p-3 cursor-pointer hover:border-brand-orange">
                        <input type="checkbox" name="setuju_syarat" value="1" required @checked(old('setuju_syarat'))
                               class="mt-0.5 rounded border-slate-300 text-brand-orange focus:ring-brand-orange">
                        <span class="text-sm text-slate-700">Saya telah membaca dan menyetujui Syarat dan Ketentuan di atas (waktu tour dan pembatalan).</span>
                    </label>
                    {!! $err('setuju_syarat') !!}
                </div>
            </div>

            {{-- Kanan: ringkasan --}}
            <aside class="lg:sticky lg:top-24 self-start bg-white rounded-2xl border border-slate-200 p-5 space-y-3">
                <h2 class="font-bold text-slate-900">Ringkasan</h2>
                <dl class="text-sm space-y-2">
                    <div><dt class="text-xs text-slate-500">Paket</dt><dd class="font-semibold text-slate-900">{{ $paket->nama_paket }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Guide</dt><dd class="font-semibold text-slate-900">{{ $guide->nama_guide }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Tanggal tour</dt><dd class="font-semibold text-slate-900">{{ $tanggal->translatedFormat('l, d F Y') }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Durasi</dt><dd class="font-semibold text-slate-900">{{ $paket->durasi }}</dd></div>
                </dl>
                <div class="pt-3 border-t border-slate-100 text-sm">
                    <div class="flex justify-between text-slate-600"><span>Harga per peserta</span><span>Rp{{ number_format($paket->harga_paket, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between text-slate-600 mt-1"><span>Peserta</span><span id="ringkas-peserta">1</span></div>
                    <div class="flex justify-between mt-3 text-base font-extrabold text-brand-blue"><span>Total bayar</span><span id="total">-</span></div>
                </div>
                <button class="w-full mt-2 py-3 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-bold text-sm shadow">
                    Buat booking dan dapatkan kode VA
                </button>
                <p class="text-[11px] text-slate-500 text-center">Bayar sebelum {{ config('goguide.batas_bayar_jam') }} jam sejak booking dibuat.</p>
            </aside>
        </form>
    </div>
</section>

<script>
    (function () {
        const harga = {{ (int) $paket->harga_paket }};
        const durasiMenit = Math.round({{ $durasiJam }} * 60);
        const jam = document.getElementById('jam_mulai');
        const peserta = document.getElementById('jumlah_peserta');

        function hitung() {
            const n = Math.max(1, parseInt(peserta.value) || 1);
            document.getElementById('ringkas-peserta').textContent = n;
            document.getElementById('total').textContent = 'Rp' + (harga * n).toLocaleString('id-ID');

            const [h, m] = (jam.value || '00:00').split(':').map(Number);
            const total = h * 60 + m + durasiMenit;
            const hh = String(Math.floor(total / 60) % 24).padStart(2, '0');
            const mm = String(total % 60).padStart(2, '0');
            document.getElementById('estimasi').textContent = jam.value ? hh + ':' + mm : '-';
        }
        jam.addEventListener('input', hitung);
        peserta.addEventListener('input', hitung);
        hitung();
    })();
</script>
@endsection