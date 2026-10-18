<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking {{ $booking->booking_code }} - Go Go Guide</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

<div class="max-w-xl mx-auto px-4 py-10">
    <div class="bg-white rounded-xl shadow p-6">

        <p class="text-green-600 font-semibold">✓ Booking berhasil dibuat</p>
        <h1 class="text-2xl font-bold mt-1">{{ $booking->booking_code }}</h1>

        @php
            $badge = [
                'pending'   => 'bg-yellow-100 text-yellow-700',
                'paid'      => 'bg-green-100 text-green-700',
                'cancelled' => 'bg-red-100 text-red-700',
            ][$booking->status] ?? 'bg-gray-100 text-gray-700';
        @endphp
        <span class="inline-block mt-2 px-3 py-1 rounded text-xs font-semibold uppercase {{ $badge }}">
            {{ $booking->status }}
        </span>

        <div class="mt-6 space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Tour</span>
                <span class="font-medium text-right">{{ $booking->tour->title }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Daerah</span>
                <span>{{ $booking->tour->region->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Guide</span>
                <span>{{ $booking->tour->guide->user->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Tanggal</span>
                <span>{{ $booking->tour_date->format('d M Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Jumlah orang</span>
                <span>{{ $booking->participants }}</span>
            </div>
            @if($booking->notes)
                <div class="flex justify-between">
                    <span class="text-gray-500">Catatan</span>
                    <span class="text-right">{{ $booking->notes }}</span>
                </div>
            @endif
            <div class="flex justify-between border-t pt-3 text-base">
                <span class="font-semibold">Total</span>
                <span class="font-bold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        @if($booking->status === 'pending')
    <button id="pay-button" type="button"
            class="w-full mt-6 bg-orange-500 text-white py-2 rounded hover:bg-orange-600">
        Bayar Sekarang
    </button>
    @if(app()->environment('local'))
    <form method="POST" action="{{ route('bookings.simulate', $booking) }}" class="mt-2">
        @csrf
        <button type="submit" class="w-full border border-dashed border-gray-400 text-gray-500 py-2 rounded text-sm hover:bg-gray-50">
            Simulasi Bayar (testing)
        </button>
    </form>
@endif
@endif

        <div class="flex justify-between mt-4 text-sm">
    <a href="{{ route('home') }}" class="text-blue-900 hover:underline">&larr; Beranda</a>
    <a href="{{ route('bookings.index') }}" class="text-blue-900 hover:underline">Semua booking saya &rarr;</a>
</div>
        
    </div>
</div>

</body>
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
document.getElementById('pay-button')?.addEventListener('click', async function () {
    const btn = this;
    const label = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Memproses...';

    try {
        const res = await fetch("{{ route('bookings.pay', $booking) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Terjadi kesalahan');

        window.snap.pay(data.token, {
            onSuccess: () => location.reload(),
            onPending: () => location.reload(),
            onError:   () => alert('Pembayaran gagal, coba lagi.'),
            onClose:   () => { btn.disabled = false; btn.textContent = label; }
        });
    } catch (e) {
        alert(e.message);
        btn.disabled = false;
        btn.textContent = label;
    }
});
</script>
</html>