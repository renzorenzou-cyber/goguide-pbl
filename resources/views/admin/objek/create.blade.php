@extends('layouts.site')

@section('title', 'Tambah Objek Wisata - GoGuide')

@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <a href="{{ route('admin.objek.index') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Daftar objek wisata
        </a>
        <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900">Tambah objek wisata</h1>

        @include('admin.partials.flash')

        <form method="POST" action="{{ route('admin.objek.store') }}" enctype="multipart/form-data"
              class="mt-6 bg-white rounded-2xl border border-slate-200 p-5 sm:p-7 space-y-6">
            @csrf
            @include('admin.objek._form')

            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.objek.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold hover:border-slate-400">Batal</a>
                <button class="px-5 py-2.5 rounded-xl bg-brand-orange hover:bg-orange-600 text-white text-sm font-bold shadow">Simpan objek</button>
            </div>
        </form>
    </div>
</section>
@endsection