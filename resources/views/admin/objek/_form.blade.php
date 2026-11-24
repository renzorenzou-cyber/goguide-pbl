@php
    $in  = 'mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20';
    $lbl = 'text-sm font-semibold text-slate-700';
    $fotoUrl = ($objek->foto && file_exists(public_path($objek->foto))) ? asset($objek->foto) : null;
@endphp

@if ($areas->isEmpty() || $kategoris->isEmpty())
    <div role="alert" class="rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm px-4 py-3">
        Objek wisata butuh minimal satu
        @if ($areas->isEmpty()) <a class="font-bold underline" href="{{ route('admin.area.index') }}">area</a> @endif
        @if ($areas->isEmpty() && $kategoris->isEmpty()) dan @endif
        @if ($kategoris->isEmpty()) <a class="font-bold underline" href="{{ route('admin.kategori.index') }}">kategori</a> @endif
        terlebih dahulu.
    </div>
@endif

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5">
        <div>
            <label for="nama_objek" class="{{ $lbl }}">Nama objek wisata</label>
            <input id="nama_objek" name="nama_objek" required class="{{ $in }}"
                   value="{{ old('nama_objek', $objek->nama_objek) }}" placeholder="Contoh: Pantai Lovina">
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label for="id_area" class="{{ $lbl }}">Area</label>
                <select id="id_area" name="id_area" required class="{{ $in }}">
                    <option value="">Pilih area</option>
                    @foreach ($areas as $a)
                        <option value="{{ $a->id_area }}" @selected(old('id_area', $objek->id_area) == $a->id_area)>{{ $a->nama_area }} ({{ $a->kabupaten }})</option>
                    @endforeach
                </select>
                <a href="{{ route('admin.area.index') }}" class="text-xs font-semibold text-brand-blue hover:text-brand-orange">Area belum ada? Tambah di sini</a>
            </div>
            <div>
                <label for="id_kategori" class="{{ $lbl }}">Kategori</label>
                <select id="id_kategori" name="id_kategori" required class="{{ $in }}">
                    <option value="">Pilih kategori</option>
                    @foreach ($kategoris as $k)
                        <option value="{{ $k->id_kategori }}" @selected(old('id_kategori', $objek->id_kategori) == $k->id_kategori)>{{ $k->nama_kategori }}</option>
                    @endforeach
                </select>
                <a href="{{ route('admin.kategori.index') }}" class="text-xs font-semibold text-brand-blue hover:text-brand-orange">Kategori belum ada? Tambah di sini</a>
            </div>
        </div>

        <div>
            <label for="deskripsi" class="{{ $lbl }}">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" class="{{ $in }}">{{ old('deskripsi', $objek->deskripsi) }}</textarea>
        </div>

        <div>
            <label for="alamat" class="{{ $lbl }}">Alamat</label>
            <textarea id="alamat" name="alamat" rows="2" class="{{ $in }}">{{ old('alamat', $objek->alamat) }}</textarea>
        </div>

        <div>
            <label for="harga_tiket" class="{{ $lbl }}">Harga tiket masuk (Rp, opsional)</label>
            <input id="harga_tiket" name="harga_tiket" type="number" min="0" step="1000" class="{{ $in }} max-w-xs"
                   value="{{ old('harga_tiket', $objek->harga_tiket !== null ? (int) $objek->harga_tiket : '') }}">
            <p class="mt-1 text-xs text-slate-500">Kosongkan jika belum diketahui.</p>
        </div>
    </div>

    <div class="space-y-5">
        <div>
            <label for="foto" class="{{ $lbl }}">Foto</label>
            <img id="foto-preview" alt="Pratinjau foto objek" src="{{ $fotoUrl ?? '' }}"
                 class="mt-1 w-full aspect-[4/3] object-cover rounded-xl {{ $fotoUrl ? '' : 'hidden' }}">
            <input id="foto" name="foto" type="file" accept="image/*" class="mt-2 block w-full text-sm">
            <p class="mt-1 text-xs text-slate-500">JPG, PNG, atau WebP. Maksimal 2 MB.</p>
        </div>

        <div>
            <label for="status" class="{{ $lbl }}">Status</label>
            <select id="status" name="status" class="{{ $in }}">
                @foreach (['Aktif', 'Nonaktif'] as $s)
                    <option value="{{ $s }}" @selected(old('status', $objek->status) === $s)>{{ $s }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-500">Objek Nonaktif tidak muncul di pilihan form paket.</p>
        </div>
    </div>
</div>

<script>
    document.getElementById('foto')?.addEventListener('change', function () {
        const img = document.getElementById('foto-preview');
        if (this.files[0]) { img.src = URL.createObjectURL(this.files[0]); img.classList.remove('hidden'); }
    });
</script>