@extends('layouts.site')

@section('title', 'Booking ' . $booking->kode_booking . ' - GoGuide')

@section('content')
@php
    $rp = fn ($n) => 'Rp' . number_format($n, 0, ',', '.');
    $wa = $booking->guide?->no_wa ? preg_replace('/\D/', '', $booking->guide->no_wa) : null;
    if ($wa && str_starts_with($wa, '0')) { $wa = '62' . substr($wa, 1); }
    $langkah = ['belum_bayar' => 'Menunggu bayar', 'sudah_bayar' => 'Verifikasi admin', 'dikonfirmasi' => 'Dikonfirmasi', 'selesai' => 'Selesai'];
    $urutan  = array_keys($langkah);
    $posisi  = array_search($booking->status, $urutan, true);
@endphp

<section class="py-10 sm:py-14">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        <a href="{{ route('bookings.index') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Semua booking saya
        </a>

        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-mono text-slate-500">{{ $booking->kode_booking }}</p>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $booking->paket?->nama_paket }}</h1>
            </div>
            @include('partials.badge-status', ['status' => $booking->status])
        </div>

        @if (session('success'))
            <div role="status" class="mt-5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div role="alert" class="mt-5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3">{{ session('error') }}</div>
        @endif

        {{-- Tahapan --}}
        @if ($booking->status !== 'dibatalkan')
            <ol class="mt-6 grid grid-cols-4 gap-2" aria-label="Tahapan booking">
                @foreach ($langkah as $kode => $label)
                    @php $i = array_search($kode, $urutan, true); $capai = $posisi !== false && $i <= $posisi; @endphp
                    <li class="text-center">
                        <div class="h-1.5 rounded-full {{ $capai ? 'bg-brand-orange' : 'bg-slate-200' }}"></div>
                        <p class="mt-1.5 text-[11px] font-semibold {{ $capai ? 'text-slate-800' : 'text-slate-400' }}">{{ $label }}</p>
                    </li>
                @endforeach
            </ol>
        @endif

        <div class="mt-6 grid lg:grid-cols-3 gap-6">

            {{-- Kiri: detail --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
                    <h2 class="font-bold text-slate-900">Detail tour</h2>
                    <dl class="mt-4 grid sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div><dt class="text-xs text-slate-500">Tanggal tour</dt><dd class="font-semibold">{{ $booking->tanggal_booking->translatedFormat('l, d F Y') }}</dd></div>
                        <div><dt class="text-xs text-slate-500">Waktu</dt><dd class="font-semibold">{{ substr($booking->jam_mulai, 0, 5) }} - {{ $estimasiSelesai }} <span class="font-normal text-slate-500">({{ $booking->paket?->durasi }})</span></dd></div>
                        <div><dt class="text-xs text-slate-500">Guide</dt><dd class="font-semibold">{{ $booking->guide?->nama_guide }}</dd></div>
                        <div><dt class="text-xs text-slate-500">Jumlah peserta</dt><dd class="font-semibold">{{ $booking->jumlah_peserta }} orang</dd></div>
                        @if ($booking->catatan)
                            <div class="sm:col-span-2"><dt class="text-xs text-slate-500">Catatan</dt><dd>{{ $booking->catatan }}</dd></div>
                        @endif
                    </dl>

                    @if (in_array($booking->status, ['dikonfirmasi', 'selesai'], true) && $booking->guide)
                        <div class="mt-5 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm">
                            <p class="font-bold text-emerald-800">Kontak guide kamu</p>
                            <p class="mt-1 text-emerald-900">{{ $booking->guide->nama_guide }} @if($booking->guide->no_wa)&middot; {{ $booking->guide->no_wa }}@endif</p>
                            @if ($wa)
                                <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
                                   class="inline-block mt-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">
                                    <i class="fa-brands fa-whatsapp mr-1"></i> Hubungi lewat WhatsApp
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
                    <h2 class="font-bold text-slate-900">Rincian biaya</h2>
                    <div class="mt-3 text-sm space-y-1.5">
                        <div class="flex justify-between text-slate-600"><span>{{ $rp($booking->harga_satuan) }} &times; {{ $booking->jumlah_peserta }} peserta</span><span>{{ $rp($booking->total_bayar) }}</span></div>
                        <div class="flex justify-between pt-2 border-t border-slate-100 text-base font-extrabold text-brand-blue"><span>Total bayar</span><span>{{ $rp($booking->total_bayar) }}</span></div>
                    </div>
                    @if ($booking->tanggal_bayar)
                        <p class="mt-3 text-xs text-slate-500">Dibayar {{ $booking->tanggal_bayar->translatedFormat('d M Y H:i') }} &middot; {{ $booking->metode_pembayaran }}</p>
                    @endif
                </div>
            </div>

            {{-- Kanan: panel sesuai status --}}
            <aside class="space-y-4 self-start">

                @if ($booking->status === 'belum_bayar')
                    <div class="bg-white rounded-2xl border-2 border-brand-orange p-5">
                        <h2 class="font-bold text-slate-900">Pembayaran Virtual Account</h2>
                        <p class="mt-3 text-xs text-slate-500">Kode VA</p>
                        <div class="flex items-center gap-2">
                            <p id="kode-va" class="text-xl font-extrabold tracking-wider text-slate-900 break-all">{{ $booking->kode_va }}</p>
                            <button type="button" id="salin-va" class="shrink-0 px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs font-bold hover:border-brand-orange hover:text-brand-orange">Salin</button>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">Nominal</p>
                        <p class="font-extrabold text-brand-blue">{{ $rp($booking->total_bayar) }}</p>
                        <p class="mt-3 text-xs text-slate-500">Batas bayar</p>
                        <p class="text-sm font-semibold">{{ $booking->batas_bayar->translatedFormat('d M Y H:i') }}</p>
                        <p class="text-xs font-bold text-amber-700" id="hitung-mundur" data-batas="{{ $booking->batas_bayar->toIso8601String() }}"></p>

                        <form method="POST" action="{{ route('bookings.simulate', $booking->id_booking) }}" class="mt-4">
                            @csrf
                            <button class="w-full py-3 rounded-xl bg-brand-blue hover:bg-brand-darkBlue text-white font-bold text-sm">Simulasikan pembayaran</button>
                        </form>
                        <p class="mt-2 text-[11px] text-slate-500">Mode simulasi: belum terhubung ke payment gateway. Tombol ini mencatat pembayaran seolah sudah dilakukan.</p>
                    </div>
                @elseif ($booking->status === 'sudah_bayar')
                    <div class="rounded-2xl bg-blue-50 border border-blue-200 p-5 text-sm text-blue-900">
                        <p class="font-bold">Pembayaran tercatat</p>
                        <p class="mt-1">Admin sedang memverifikasi pembayaran kamu. Kamu akan dapat notifikasi setelah booking dikonfirmasi.</p>
                    </div>
                @elseif ($booking->status === 'dikonfirmasi')
                    <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-5 text-sm text-emerald-900">
                        <p class="font-bold">Booking dikonfirmasi</p>
                        <p class="mt-1">Sampai jumpa pada {{ $booking->tanggal_booking->translatedFormat('d F Y') }}, pukul {{ substr($booking->jam_mulai, 0, 5) }}.</p>
                    </div>
                @elseif ($booking->status === 'selesai')
                    <div class="rounded-2xl bg-slate-100 border border-slate-200 p-5 text-sm text-slate-700">
                        <p class="font-bold">Tour selesai</p>
                        <p class="mt-1">Terima kasih sudah memakai Go Guide.</p>
                        @include('partials.ulasan-booking')
                    </div>
                @elseif ($booking->status === 'dibatalkan')
                    <div class="rounded-2xl bg-red-50 border border-red-200 p-5 text-sm text-red-900">
                        <p class="font-bold">Booking dibatalkan</p>
                        <p class="mt-1">{{ $booking->alasan_batal ?: 'Tidak ada alasan tercatat.' }}</p>
                    </div>
                @endif

                {{-- Pembatalan --}}
                @if ($bisaBatal)
                    <details class="bg-white rounded-2xl border border-slate-200 p-5">
                        <summary class="cursor-pointer text-sm font-bold text-red-700">Batalkan booking</summary>
                        <p class="mt-3 text-sm text-slate-600">
                            @if ($persenRefund === null)
                                Booking belum dibayar, jadi pembatalan <strong>tanpa biaya</strong>.
                            @elseif ($persenRefund === 0)
                                Sesuai S&amp;K, pembatalan pada waktu ini <strong>tidak mendapat pengembalian dana</strong>.
                            @else
                                Sesuai S&amp;K, kamu mendapat pengembalian dana <strong>{{ $persenRefund }}%</strong>
                                ({{ $rp($booking->total_bayar * $persenRefund / 100) }}), diproses manual oleh admin.
                            @endif
                        </p>
                        <form method="POST" action="{{ route('bookings.cancel', $booking->id_booking) }}" class="mt-3"
                              onsubmit="return confirm('Yakin batalkan booking ini? Tindakan ini tidak bisa dibatalkan.')">
                            @csrf
                            <label for="alasan" class="text-xs font-semibold text-slate-700">Alasan (opsional)</label>
                            <input id="alasan" name="alasan" maxlength="120"
                                   class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20">
                            <button class="mt-3 w-full py-2.5 rounded-xl border border-red-300 text-red-700 hover:bg-red-50 text-sm font-bold">Ya, batalkan booking</button>
                        </form>
                    </details>
                @endif
            </aside>
        </div>
    </div>
</section>

<script>
    (function () {
        const salin = document.getElementById('salin-va');
        if (salin) {
            salin.addEventListener('click', function () {
                const teks = document.getElementById('kode-va').textContent.trim();
                navigator.clipboard?.writeText(teks).then(() => {
                    salin.textContent = 'Tersalin';
                    setTimeout(() => salin.textContent = 'Salin', 1500);
                });
            });
        }

        const el = document.getElementById('hitung-mundur');
        if (el) {
            const batas = new Date(el.dataset.batas).getTime();
            const tick = () => {
                const sisa = batas - Date.now();
                if (sisa <= 0) { el.textContent = 'Waktu pembayaran habis'; setTimeout(() => location.reload(), 1200); return; }
                const j = Math.floor(sisa / 3600000), m = Math.floor(sisa % 3600000 / 60000), d = Math.floor(sisa % 60000 / 1000);
                el.textContent = 'Sisa waktu ' + j + ' jam ' + m + ' menit ' + d + ' detik';
                setTimeout(tick, 1000);
            };
            tick();
        }
    })();
</script>
@endsection