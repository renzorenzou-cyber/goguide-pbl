@extends('layouts.site')

@section('title', 'Periksa Guide - Go Guide')

@section('content')
@php
    $menunggu = $guide->status_verifikasi === 'Menunggu';
    $warna = ['Menunggu' => 'bg-yellow-100 text-yellow-700', 'Terverifikasi' => 'bg-emerald-100 text-emerald-700', 'Ditolak' => 'bg-red-100 text-red-700'];
    $wa = $guide->no_wa ? preg_replace('/\D/', '', $guide->no_wa) : null;
    if ($wa && str_starts_with($wa, '0')) { $wa = '62' . substr($wa, 1); }
@endphp

<section class="py-10 sm:py-14">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <a href="{{ route('admin.guide.index', ['status' => $guide->status_verifikasi]) }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Daftar pendaftar
        </a>

        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $guide->nama_guide }}</h1>
            <span class="px-3 py-1 rounded-lg text-xs font-bold {{ $warna[$guide->status_verifikasi] ?? 'bg-slate-100 text-slate-700' }}">{{ $guide->status_verifikasi }}</span>
        </div>

        @if (session('success'))
            <div role="status" class="mt-5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div role="alert" class="mt-5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div role="alert" class="mt-5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3">
                <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        {{-- Kredensial akun (tampil sekali setelah persetujuan) --}}
        @if (session('kredensial'))
            @php $k = session('kredensial'); @endphp
            <div class="mt-5 rounded-2xl border-2 border-brand-orange bg-orange-50 p-5">
                <p class="font-bold text-slate-900">Akun guide dibuat</p>
                <dl class="mt-2 grid sm:grid-cols-2 gap-3 text-sm">
                    <div><dt class="text-xs text-slate-500">Username</dt><dd class="font-mono font-bold">{{ $k['username'] }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Password sementara</dt><dd class="font-mono font-bold">{{ $k['password'] }}</dd></div>
                </dl>
                <p class="mt-3 text-sm {{ $k['terkirim'] ? 'text-emerald-800' : 'text-red-700 font-semibold' }}">
                    @if ($k['terkirim'])
                        <i class="fa-solid fa-envelope-circle-check mr-1"></i> Email berisi akun ini sudah dikirim ke {{ $k['email'] }}.
                    @else
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Email gagal terkirim. Kirim username dan password ini ke guide secara manual (misalnya lewat WhatsApp).
                    @endif
                </p>
                <p class="mt-1 text-xs text-slate-500">Password hanya ditampilkan sekali di sini. Guide wajib menggantinya saat login pertama.</p>
            </div>
        @endif

        <div class="mt-6 grid lg:grid-cols-3 gap-6">
            {{-- Kiri: data + indikator --}}
            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
                    <h2 class="font-bold text-slate-900">Data pendaftar</h2>
                    <dl class="mt-4 grid sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div><dt class="text-xs text-slate-500">Email</dt><dd class="font-semibold break-all">{{ $guide->email }}</dd></div>
                        <div>
                            <dt class="text-xs text-slate-500">WhatsApp</dt>
                            <dd class="font-semibold">
                                {{ $guide->no_wa ?: '-' }}
                                @if ($wa) <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" class="ml-1 text-xs font-bold text-emerald-700 hover:underline"><i class="fa-brands fa-whatsapp"></i> Hubungi</a> @endif
                            </dd>
                        </div>
                        <div class="sm:col-span-2"><dt class="text-xs text-slate-500">Alamat</dt><dd>{{ $guide->alamat ?: '-' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-xs text-slate-500">Bio singkat</dt><dd>{{ $guide->bio_singkat ?: '-' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-xs text-slate-500">Pengalaman</dt><dd class="whitespace-pre-line">{{ $guide->pengalaman ?: '-' }}</dd></div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs text-slate-500">Bahasa</dt>
                            <dd class="mt-1 flex flex-wrap gap-1.5">
                                @forelse ($guide->bahasa as $b)
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-xs font-semibold">{{ $b->nama_bahasa }} &middot; {{ $b->pivot->tingkat_kemampuan }}</span>
                                @empty
                                    <span class="text-slate-500">Belum ada.</span>
                                @endforelse
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs text-slate-500">Biografi lengkap</dt>
                            <dd>
                                @if ($guide->bio_lengkap_file)
                                    <a href="{{ route('admin.guide.bio', $guide->id_guide) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-blue/10 text-brand-blue text-xs font-bold hover:bg-brand-blue hover:text-white">
                                        <i class="fa-solid fa-download"></i> Unduh berkas
                                    </a>
                                @else
                                    <span class="text-slate-500">Belum diunggah.</span>
                                @endif
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
    <dt class="text-xs text-slate-500">Sertifikat pemandu</dt>
    <dd class="mt-1">
        @if ($guide->sertifikat_file)
            @if (strtolower(pathinfo($guide->sertifikat_file, PATHINFO_EXTENSION)) === 'pdf')
                <a href="{{ route('admin.guide.sertifikat', $guide->id_guide) }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-blue/10 text-brand-blue text-xs font-bold hover:bg-brand-blue hover:text-white">
                    <i class="fa-solid fa-file-pdf"></i> Buka sertifikat (PDF)
                </a>
            @else
                <a href="{{ route('admin.guide.sertifikat', $guide->id_guide) }}" target="_blank" rel="noopener" title="Klik untuk memperbesar">
                    <img src="{{ route('admin.guide.sertifikat', $guide->id_guide) }}" alt="Sertifikat {{ $guide->nama_guide }}"
                         class="max-h-72 rounded-xl border border-slate-200">
                </a>
            @endif
        @else
            <span class="text-slate-500">Tidak diunggah.</span>
        @endif
    </dd>
</div>
                    </dl>
                </div>

                {{-- Indikator kelayakan --}}
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
                        <h2 class="font-bold text-slate-900">Indikator kelayakan</h2>
                        @if ($menunggu)
                            <span id="penanda" class="px-3 py-1 rounded-lg text-xs font-bold {{ $layak ? 'bg-emerald-100 text-emerald-700' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $layak ? 'Layak' : 'Perlu ditinjau' }}
                            </span>
                        @endif
                    </div>
                    <ul class="divide-y divide-slate-100">
                        @foreach ($indikator as $i)
                            <li class="px-5 sm:px-6 py-3 flex items-start gap-3" @if($i['no'] === 2) id="baris-wa" @endif>
                                <span class="mt-0.5 w-6 h-6 shrink-0 rounded-full flex items-center justify-center text-xs
                                    {{ $i['ok'] ? 'bg-emerald-100 text-emerald-700' : ($i['wajib'] ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-500') }}"
                                    @if($i['no'] === 2) id="ikon-wa" @endif>
                                    <i class="fa-solid {{ $i['ok'] ? 'fa-check' : 'fa-xmark' }}"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $i['no'] }}. {{ $i['nama'] }}
                                        <span class="ml-1 text-[10px] font-bold uppercase {{ $i['wajib'] ? 'text-red-600' : 'text-slate-400' }}">{{ $i['wajib'] ? 'wajib' : 'tidak wajib' }}</span>
                                    </p>
                                    <p class="text-xs text-slate-500">{{ $i['kriteria'] }}</p>
                                    <p class="text-xs mt-0.5 {{ $i['ok'] ? 'text-emerald-700' : 'text-slate-700' }}" @if($i['no'] === 2) id="catatan-wa" @endif>{{ $i['catatan'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Kanan: keputusan --}}
            <aside class="space-y-4 self-start">
                @if ($menunggu)
                    <form method="POST" action="{{ route('admin.guide.approve', $guide->id_guide) }}" class="bg-white rounded-2xl border border-slate-200 p-5"
                          onsubmit="return confirm('Setujui pendaftaran ini dan buat akun guide?')">
                        @csrf
                        <h2 class="font-bold text-slate-900">Setujui</h2>
                        <label class="mt-3 flex items-start gap-2 text-sm text-slate-700 cursor-pointer">
                            <input type="checkbox" id="wa_dikonfirmasi" name="wa_dikonfirmasi" value="1" class="mt-0.5 rounded border-slate-300 text-brand-orange focus:ring-brand-orange">
                            Saya sudah menghubungi pendaftar dan nomor WA aktif
                        </label>
                        <div id="blok-tetap" class="mt-3 {{ $layakJikaWaOk ? 'hidden' : '' }}">
                            <label class="flex items-start gap-2 text-sm text-slate-700 cursor-pointer">
                                <input type="checkbox" name="tetap_setuju" value="1" class="mt-0.5 rounded border-slate-300 text-brand-orange focus:ring-brand-orange">
                                Ada indikator wajib yang belum terpenuhi, tetap setujui
                            </label>
                        </div>
                        <button class="mt-4 w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">Setujui dan buat akun</button>
                        <p class="mt-2 text-[11px] text-slate-500">Username dan password sementara dikirim ke email guide.</p>
                    </form>

                    <form method="POST" action="{{ route('admin.guide.reject', $guide->id_guide) }}" class="bg-white rounded-2xl border border-slate-200 p-5"
                          onsubmit="return confirm('Tolak pendaftaran ini?')">
                        @csrf
                        <h2 class="font-bold text-slate-900">Tolak</h2>
                        <label for="alasan" class="mt-3 block text-xs font-semibold text-slate-700">Alasan penolakan (dikirim ke calon guide)</label>
                        <textarea id="alasan" name="alasan" rows="3" required minlength="10" maxlength="1000"
                                  class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20">{{ old('alasan') }}</textarea>
                        <button class="mt-3 w-full py-2.5 rounded-xl border border-red-300 text-red-700 hover:bg-red-50 text-sm font-bold">Tolak pendaftaran</button>
                    </form>
                @else
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 text-sm">
                        <h2 class="font-bold text-slate-900">Keputusan</h2>
                        <p class="mt-2 text-slate-600">
                            {{ $guide->status_verifikasi }}
                            @if ($guide->tanggal_persetujuan) pada {{ $guide->tanggal_persetujuan->translatedFormat('d M Y H:i') }} @endif
                            @if ($guide->penyetuju) oleh {{ $guide->penyetuju->name }} @endif
                        </p>
                        @if ($guide->status_verifikasi === 'Ditolak' && $guide->alasan_penolakan)
                            <p class="mt-3 text-xs text-slate-500">Alasan</p>
                            <p class="text-slate-800">{{ $guide->alasan_penolakan }}</p>
                        @endif
                    </div>
                @endif
            </aside>
        </div>
    </div>
</section>

@if ($menunggu)
<script>
    // Penanda Layak / Perlu ditinjau dan baris indikator 2 ikut berubah saat admin mencentang konfirmasi WA
    (function () {
        const cek = document.getElementById('wa_dikonfirmasi');
        const penanda = document.getElementById('penanda');
        const blokTetap = document.getElementById('blok-tetap');
        const ikon = document.getElementById('ikon-wa');
        const catatan = document.getElementById('catatan-wa');
        const layakJikaWaOk = @json($layakJikaWaOk);
        const layakSekarang = @json($layak);

        // indikator nomor 2 = indeks 1: kondisi awal dan kondisi setelah dicentang
        const awal = { ok: @json($indikator[1]['ok']), catatan: @json($indikator[1]['catatan']) };
        const sesudah = { ok: @json($indikatorWaOk[1]['ok']), catatan: @json($indikatorWaOk[1]['catatan']) };

        function perbarui() {
            const layak = cek.checked ? layakJikaWaOk : layakSekarang;
            penanda.textContent = layak ? 'Layak' : 'Perlu ditinjau';
            penanda.className = 'px-3 py-1 rounded-lg text-xs font-bold ' + (layak ? 'bg-emerald-100 text-emerald-700' : 'bg-yellow-100 text-yellow-800');
            blokTetap.classList.toggle('hidden', layak);

            const r = cek.checked ? sesudah : awal;
            ikon.className = 'mt-0.5 w-6 h-6 shrink-0 rounded-full flex items-center justify-center text-xs '
                + (r.ok ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700');
            ikon.innerHTML = '<i class="fa-solid ' + (r.ok ? 'fa-check' : 'fa-xmark') + '"></i>';
            catatan.textContent = r.catatan;
            catatan.className = 'text-xs mt-0.5 ' + (r.ok ? 'text-emerald-700' : 'text-slate-700');
        }
        cek.addEventListener('change', perbarui);
        perbarui();
    })();
</script>
@endif
@endsection