@extends('layouts.site')

@section('title', 'Dashboard Administrator - GoGuide')

@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <div class="rounded-3xl p-6 sm:p-8 text-white bg-slate-900">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/10 border border-white/20">
                <i class="fa-solid fa-user-shield"></i> Administrator
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold mt-3">Halo, {{ auth()->user()->name }}</h1>
            <p class="text-slate-300 text-sm mt-1">Ringkasan data GoGuide.</p>
            <a href="{{ route('profile.edit') }}" class="inline-block mt-4 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 font-bold text-sm">
                <i class="fa-solid fa-user-pen mr-1"></i> Edit Profil
            </a>
        </div>

        {{-- Ringkasan jumlah --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Wisatawan terdaftar</p>
                <p class="text-3xl font-extrabold text-brand-blue mt-1">{{ $stat['wisatawan'] }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Pemandu terverifikasi</p>
                <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $stat['guide'] }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Objek wisata / paket</p>
                <p class="text-3xl font-extrabold text-brand-orange mt-1">{{ $stat['objek'] }} <span class="text-lg text-slate-400">/ {{ $stat['paket'] }}</span></p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Total booking</p>
                <p class="text-3xl font-extrabold text-brand-blue mt-1">{{ $stat['booking'] }}</p>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4 mt-4">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Pembayaran masuk (sudah bayar dan dikonfirmasi)</p>
                <p class="text-2xl font-extrabold text-green-600 mt-1">Rp {{ number_format($stat['pemasukan'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Menunggu approve pembayaran</p>
                <p class="text-2xl font-extrabold text-yellow-500 mt-1">{{ $stat['perlu_approve'] }}</p>
            </div>
        </div>

                {{-- Pengelolaan data --}}
        <div class="mt-8">
            <h2 class="font-bold text-slate-900 mb-3">Pengelolaan Data</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">   {{-- <== BERUBAH: dari sm:grid-cols-3 --}}
                @foreach([
                    ['fa-user-group', 'Akun dan data pemandu', 'Periksa pendaftar, indikator kelayakan, dan sertifikat', route('admin.guide.index'),
                        'PERIKSA PENDAFTAR' . ($menunggu->count() ? ' (' . $menunggu->count() . ' menunggu)' : '')],
                    ['fa-mountain-sun', 'Destinasi dan kategori', 'Kelola paket wisata, objek wisata, kategori, dan area', route('admin.paket.index'),
                        'KELOLA PAKET'],
                    ['fa-money-check-dollar', 'Booking dan pembayaran', 'Verifikasi pembayaran, konfirmasi booking, dan tandai tour selesai', route('admin.booking.index'),
                        'KELOLA BOOKING' . ($stat['perlu_approve'] ? ' (' . $stat['perlu_approve'] . ' perlu verifikasi)' : '')],
                    ['fa-hand-holding-dollar', 'Fee guide', 'Rekap fee tour selesai dan tandai sudah dibayar ke guide', route('admin.fee.index'), 'REKAP FEE'],   {{-- <== BARU --}}
                ] as [$ikon, $judul, $isi, $url, $label])
                
                    <a href="{{ $url }}" class="block bg-white rounded-2xl p-5 border border-slate-200 hover:border-brand-orange hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-xl bg-orange-50 text-brand-orange flex items-center justify-center mb-3">
                            <i class="fa-solid {{ $ikon }}"></i>
                        </div>
                        <p class="font-bold text-slate-800 text-sm">{{ $judul }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $isi }}</p>
                        <span class="inline-block mt-3 text-[11px] font-bold text-brand-orange">{{ $label }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Pendaftaran pemandu menunggu verifikasi --}}
        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900">Pendaftaran Pemandu Menunggu Verifikasi</h2>
                    <p class="text-xs text-slate-500">Request dari calon pemandu yang perlu diperiksa.</p>
                </div>
                <span class="px-3 py-1 rounded-lg text-xs font-bold bg-yellow-100 text-yellow-700">{{ $menunggu->count() }} menunggu</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                        <tr>
                            <th class="text-left px-5 py-3">Nama</th>
                            <th class="text-left px-5 py-3">Email</th>
                            <th class="text-left px-5 py-3">WhatsApp</th>
                            <th class="text-left px-5 py-3">Bio singkat</th>
                            <th class="text-left px-5 py-3">Mendaftar</th>
                            <th class="text-center px-5 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($menunggu as $g)
                            <tr>
                                <td class="px-5 py-3 font-semibold">{{ $g->nama_guide }}</td>
                                <td class="px-5 py-3">{{ $g->email }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">{{ $g->no_wa ?: '-' }}</td>
                                <td class="px-5 py-3 text-slate-500">{{ \Illuminate\Support\Str::limit($g->bio_singkat, 50) }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">{{ $g->created_at->format('d M Y') }}</td>
                                <td class="px-5 py-3 text-center">
                                    <a href="{{ route('admin.guide.show', $g->id_guide) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-orange text-white hover:bg-orange-600">Periksa</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">Tidak ada pendaftaran yang menunggu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pemantauan booking dan pembayaran --}}
        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-bold text-slate-900">Pemantauan Booking dan Pembayaran</h2>
                <div class="mt-2 flex flex-wrap gap-2 text-xs">
                    @foreach(['belum_bayar', 'sudah_bayar', 'dikonfirmasi', 'dibatalkan'] as $s)
                        <span class="inline-flex items-center gap-1.5">
                            @include('partials.badge-status', ['status' => $s])
                            <span class="font-bold text-slate-700">{{ $perStatus[$s] ?? 0 }}</span>
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                        <tr>
                            <th class="text-left px-5 py-3">Kode</th>
                            <th class="text-left px-5 py-3">Wisatawan</th>
                            <th class="text-left px-5 py-3">Paket</th>
                            <th class="text-left px-5 py-3">Guide</th>
                            <th class="text-left px-5 py-3">Tanggal tour</th>
                            <th class="text-right px-5 py-3">Total</th>
                            <th class="text-left px-5 py-3">Pembayaran</th>
                            <th class="text-center px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($bookingTerbaru as $b)
                            <tr>
                                <td class="px-5 py-3 font-mono text-xs">{{ $b->kode_booking }}</td>
                                <td class="px-5 py-3">{{ $b->wisatawan->nama_wisatawan }}</td>
                                <td class="px-5 py-3">{{ $b->paket->nama_paket }}</td>
                                <td class="px-5 py-3">{{ $b->guide->nama_guide }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">{{ $b->tanggal_booking->format('d M Y') }}</td>
                                <td class="px-5 py-3 text-right whitespace-nowrap">Rp {{ number_format($b->total_bayar, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-xs text-slate-500">
                                    @if($b->tanggal_bayar)
                                        Dibayar {{ $b->tanggal_bayar->format('d M H:i') }}
                                    @elseif($b->status === 'belum_bayar')
                                        VA {{ $b->kode_va ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center">@include('partials.badge-status', ['status' => $b->status])</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-5 py-8 text-center text-slate-500">Belum ada booking.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        
                

    </div>
</section>
@endsection