@php
    $peta = [
        'belum_bayar'  => ['Belum Bayar', 'bg-yellow-100 text-yellow-700'],
        'sudah_bayar'  => ['Sudah Bayar', 'bg-blue-100 text-blue-700'],
        'dikonfirmasi' => ['Dikonfirmasi', 'bg-green-100 text-green-700'],
        'selesai'      => ['Selesai', 'bg-slate-200 text-slate-700'],
        'dibatalkan'   => ['Dibatalkan', 'bg-red-100 text-red-700'],
    ];
    [$label, $kelas] = $peta[$status] ?? [$status, 'bg-slate-100 text-slate-700'];
@endphp
<span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold {{ $kelas }}">{{ $label }}</span>
