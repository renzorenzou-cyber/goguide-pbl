@if (session('success'))
    <div role="status" class="mt-5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div role="alert" class="mt-5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div role="alert" class="mt-5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3">
        <p class="font-bold mb-1">Data belum bisa disimpan:</p>
        <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif