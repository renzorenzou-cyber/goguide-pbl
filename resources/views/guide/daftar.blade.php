@extends('layouts.site')

@section('title', 'Daftar sebagai Guide - Go Guide')

@section('content')
@php
    $in  = 'mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20';
    $lbl = 'text-sm font-semibold text-slate-700';
    $err = fn ($f) => $errors->first($f) ? '<p class="mt-1 text-xs font-semibold text-red-600">' . e($errors->first($f)) . '</p>' : '';
@endphp

<section class="py-10 sm:py-14">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <a href="{{ route('home') }}#daftar-guide" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Beranda
        </a>
        <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900">Daftar sebagai Guide</h1>
        <p class="text-sm text-slate-500 mt-1">Isi datamu dengan lengkap. Admin akan memeriksa, dan akun login dikirim ke email setelah disetujui.</p>

        @if (session('berhasil'))
            <div role="status" class="mt-6 rounded-2xl bg-emerald-50 border border-emerald-200 p-5 text-emerald-900">
                <p class="font-bold"><i class="fa-solid fa-circle-check mr-1"></i> Pendaftaran terkirim</p>
                <p class="text-sm mt-1">{{ session('berhasil') }}</p>
                <a href="{{ route('home') }}" class="inline-block mt-3 text-sm font-bold text-emerald-800 underline">Kembali ke beranda</a>
            </div>
        @else
            @if ($errors->any())
                <div role="alert" class="mt-6 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3">
                    <p class="font-bold mb-1">Pendaftaran belum bisa dikirim:</p>
                    <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('guide.daftar.store') }}" enctype="multipart/form-data"
                  class="mt-6 bg-white rounded-2xl border border-slate-200 p-5 sm:p-7 space-y-6">
                @csrf

                <div>
                    <h2 class="font-bold text-slate-900">Data diri</h2>
                    <div class="mt-3 grid sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label for="nama_guide" class="{{ $lbl }}">Nama lengkap</label>
                            <input id="nama_guide" name="nama_guide" required class="{{ $in }}" value="{{ old('nama_guide') }}">
                        </div>
                        <div>
                            <label for="email" class="{{ $lbl }}">Email</label>
                            <input id="email" name="email" type="email" required class="{{ $in }}" value="{{ old('email') }}">
                            <p class="mt-1 text-xs text-slate-500">Akun login dikirim ke email ini.</p>
                        </div>
                        <div>
                            <label for="no_wa" class="{{ $lbl }}">Nomor WhatsApp</label>
                            <input id="no_wa" name="no_wa" required inputmode="tel" class="{{ $in }}" value="{{ old('no_wa') }}" placeholder="08xxxxxxxxxx">
                            <p class="mt-1 text-xs text-slate-500">Admin akan menghubungi nomor ini untuk konfirmasi.</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="alamat" class="{{ $lbl }}">Alamat</label>
                            <input id="alamat" name="alamat" required class="{{ $in }}" value="{{ old('alamat') }}">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="foto" class="{{ $lbl }}">Foto profil (opsional)</label>
                            <input id="foto" name="foto" type="file" accept="image/*" class="mt-1 block w-full text-sm">
                            <p class="mt-1 text-xs text-slate-500">JPG, PNG, atau WebP, maksimal 2 MB.</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="font-bold text-slate-900">Profil dan pengalaman</h2>
                    <div class="mt-3 space-y-4">
                        <div>
                            <label for="bio_singkat" class="{{ $lbl }}">Bio singkat</label>
                            <textarea id="bio_singkat" name="bio_singkat" rows="3" maxlength="500" required class="{{ $in }}"
                                      placeholder="Ringkasan dirimu, tampil di kartu profil guide.">{{ old('bio_singkat') }}</textarea>
                        </div>
                        <div>
                            <label for="bio_lengkap" class="{{ $lbl }}">Biografi lengkap (PDF atau Word)</label>
                            <input id="bio_lengkap" name="bio_lengkap" type="file" required accept=".pdf,.doc,.docx" class="mt-1 block w-full text-sm">
                            <p class="mt-1 text-xs text-slate-500">Maksimal 2 MB. Hanya dilihat admin saat verifikasi.</p>
                        </div>
                        <div class="grid sm:grid-cols-3 gap-4">
                            <div>
                                <label for="pengalaman_tahun" class="{{ $lbl }}">Lama pengalaman (tahun)</label>
                                <input id="pengalaman_tahun" name="pengalaman_tahun" type="number" min="0" max="60" required class="{{ $in }}" value="{{ old('pengalaman_tahun') }}">
                            </div>
                            <div class="sm:col-span-2">
    <label for="sertifikat_file" class="{{ $lbl }}">Sertifikat pemandu wisata (opsional)</label>
    <input id="sertifikat_file" name="sertifikat_file" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="mt-1 block w-full text-sm">
    <p class="mt-1 text-xs text-slate-500">Foto atau scan sertifikat (JPG, PNG, WebP, atau PDF), maksimal 2 MB. Hanya dilihat admin saat verifikasi.</p>
</div>
                        </div>
                        <div>
                            <label for="pengalaman_uraian" class="{{ $lbl }}">Ceritakan pengalamanmu</label>
                            <textarea id="pengalaman_uraian" name="pengalaman_uraian" rows="4" maxlength="2000" required class="{{ $in }}"
                                      placeholder="Daerah yang kamu kuasai, jenis wisata yang pernah dipandu, dll.">{{ old('pengalaman_uraian') }}</textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="font-bold text-slate-900">Bahasa yang dikuasai</h2>
                    <p class="text-xs text-slate-500">Centang bahasa lalu pilih tingkat kemampuannya. Minimal satu bahasa.</p>
                    <div class="mt-3 rounded-xl border border-slate-200 divide-y divide-slate-100">
                        @foreach ($bahasas as $b)
                            @php $dipilih = in_array($b->id_bahasa, (array) old('bahasa', [])); @endphp
                            <div class="flex flex-wrap items-center gap-3 px-3 py-2.5">
                                <label class="flex items-center gap-2 flex-1 min-w-0 cursor-pointer">
                                    <input type="checkbox" name="bahasa[]" value="{{ $b->id_bahasa }}" @checked($dipilih) class="rounded border-slate-300 text-brand-orange focus:ring-brand-orange">
                                    <span class="text-sm font-semibold">{{ $b->nama_bahasa }}</span>
                                </label>
                                <select name="tingkat[{{ $b->id_bahasa }}]" aria-label="Tingkat {{ $b->nama_bahasa }}" class="rounded-lg border border-slate-300 px-2 py-1 text-sm">
                                    @foreach ($tingkat as $t)
                                        <option value="{{ $t }}" @selected(old("tingkat.{$b->id_bahasa}", 'Menengah') === $t)>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button class="px-6 py-3 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-bold text-sm shadow">Kirim pendaftaran</button>
                </div>
            </form>
        @endif
    </div>
</section>
@endsection