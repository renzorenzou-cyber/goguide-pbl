@php
    $in  = 'mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20';
    $lbl = 'text-sm font-semibold text-slate-700';
    $err = fn ($f) => $errors->first($f) ? '<p class="mt-1 text-xs font-semibold text-red-600">' . e($errors->first($f)) . '</p>' : '';
@endphp

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5">
        <div>
            <label for="nama_paket" class="{{ $lbl }}">Nama paket</label>
            <input id="nama_paket" name="nama_paket" type="text" required class="{{ $in }}"
                   value="{{ old('nama_paket', $paket->nama_paket) }}" placeholder="Contoh: Sunrise Trekking Gunung Batur">
            {!! $err('nama_paket') !!}
        </div>

        <div class="grid sm:grid-cols-3 gap-5">
            <div>
                <label for="harga_paket" class="{{ $lbl }}">Harga per peserta (Rp)</label>
                <input id="harga_paket" name="harga_paket" type="number" min="0" required class="{{ $in }}"
                       value="{{ old('harga_paket', $paket->harga_paket) }}">
                {!! $err('harga_paket') !!}
            </div>
            <div>
                <label for="persen_fee_guide" class="{{ $lbl }}">Fee guide (%)</label>
                <input id="persen_fee_guide" name="persen_fee_guide" type="number" min="0" max="100" required class="{{ $in }}"
                       value="{{ old('persen_fee_guide', $paket->persen_fee_guide ?? 70) }}">
                {!! $err('persen_fee_guide') !!}
            </div>
            <div>
                <label for="durasi" class="{{ $lbl }}">Durasi</label>
                <input id="durasi" name="durasi" type="text" required class="{{ $in }}"
                       value="{{ old('durasi', $paket->durasi) }}" placeholder="8 jam">
                {!! $err('durasi') !!}
            </div>
        </div>

        <p id="fee-preview" class="text-sm text-slate-500"></p>

        <div>
            <label for="deskripsi" class="{{ $lbl }}">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" class="{{ $in }}">{{ old('deskripsi', $paket->deskripsi) }}</textarea>
            {!! $err('deskripsi') !!}
        </div>

        <div>
            <label for="fasilitas" class="{{ $lbl }}">Fasilitas</label>
            <textarea id="fasilitas" name="fasilitas" rows="3" class="{{ $in }}"
                      placeholder="Satu fasilitas per baris, misalnya: Antar jemput hotel">{{ old('fasilitas', $paket->fasilitas) }}</textarea>
            {!! $err('fasilitas') !!}
        </div>

        <fieldset>
            <legend class="{{ $lbl }}">Objek wisata yang dikunjungi</legend>
            <p class="text-xs text-slate-500 mb-2">Lokasi paket mengikuti area objek yang dicentang. Isi juga urutan kunjungan dan lama kunjungan (menit).</p>

            @if ($objeks->isEmpty())
                <p class="text-sm text-slate-500">Belum ada objek wisata aktif.</p>
            @else
                <div class="rounded-xl border border-slate-200 divide-y divide-slate-100 bg-white">
                    @foreach ($objeks as $o)
                        @php
                            $row   = $terpilih[$o->id_objek] ?? null;
                            $cek   = old('objek') ? in_array($o->id_objek, old('objek')) : (bool) $row;
                            $urut  = old("urutan.{$o->id_objek}", $row->urutan ?? $loop->iteration);
                            $menit = old("durasi_kunjungan.{$o->id_objek}", $row->durasi_kunjungan ?? 60);
                        @endphp
                        <div class="flex flex-wrap items-center gap-3 px-3 py-2.5">
                            <label class="flex items-center gap-2 flex-1 min-w-0">
                                <input type="checkbox" name="objek[]" value="{{ $o->id_objek }}" @checked($cek)
                                       class="rounded border-slate-300 text-brand-orange focus:ring-brand-orange">
                                <span class="min-w-0">
                                    <span class="block text-sm font-semibold truncate">{{ $o->nama_objek }}</span>
                                    <span class="block text-xs text-slate-500 truncate">
                                        <i class="fa-solid fa-location-dot text-brand-orange"></i>
                                        {{ $o->area?->nama_area ?? 'Area belum diisi' }}@if($o->area?->kabupaten), {{ $o->area->kabupaten }}@endif
                                        @if($o->kategori) &middot; {{ $o->kategori->nama_kategori }}@endif
                                    </span>
                                </span>
                            </label>
                            <label class="flex items-center gap-1 text-xs text-slate-500">Urutan
                                <input type="number" min="1" name="urutan[{{ $o->id_objek }}]" value="{{ $urut }}"
                                       class="w-16 rounded-lg border border-slate-300 px-2 py-1 text-sm">
                            </label>
                            <label class="flex items-center gap-1 text-xs text-slate-500">Menit
                                <input type="number" min="5" name="durasi_kunjungan[{{ $o->id_objek }}]" value="{{ $menit }}"
                                       class="w-20 rounded-lg border border-slate-300 px-2 py-1 text-sm">
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif
        </fieldset>
    </div>

    <div class="space-y-5">
        <div>
            <label for="foto" class="{{ $lbl }}">Foto paket</label>
            <img id="foto-preview" alt="Pratinjau foto paket"
                 src="{{ $paket->foto_url ?? '' }}"
                 class="mt-1 w-full aspect-[4/3] object-cover rounded-xl {{ $paket->foto_url ? '' : 'hidden' }}">
            <input id="foto" name="foto" type="file" accept="image/*" class="mt-2 block w-full text-sm">
            <p class="mt-1 text-xs text-slate-500">JPG, PNG, atau WebP. Maksimal 2 MB.</p>
            {!! $err('foto') !!}
        </div>

        <div>
            <label for="status" class="{{ $lbl }}">Status</label>
            <select id="status" name="status" class="{{ $in }}">
                @foreach (['Aktif', 'Nonaktif'] as $s)
                    <option value="{{ $s }}" @selected(old('status', $paket->status) === $s)>{{ $s }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-500">Paket Nonaktif tidak tampil di website.</p>
        </div>
    </div>
</div>

<script>
    document.getElementById('foto')?.addEventListener('change', function () {
        const img = document.getElementById('foto-preview');
        if (this.files[0]) { img.src = URL.createObjectURL(this.files[0]); img.classList.remove('hidden'); }
    });

    (function () {
        const h = document.getElementById('harga_paket'), p = document.getElementById('persen_fee_guide'),
              out = document.getElementById('fee-preview');
        const rp = n => 'Rp' + Math.round(n).toLocaleString('id-ID');
        function hitung() {
            const harga = parseFloat(h.value) || 0, persen = parseFloat(p.value) || 0;
            out.textContent = harga ? `Fee guide per peserta: ${rp(harga * persen / 100)} (${persen}% dari ${rp(harga)})` : '';
        }
        h.addEventListener('input', hitung); p.addEventListener('input', hitung); hitung();
    })();
</script>