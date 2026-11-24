<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus tabel dari rancangan lama (data di dalamnya ikut hilang)
        Schema::disableForeignKeyConstraints();
        foreach (['reviews', 'payments', 'tour_images', 'bookings', 'tours', 'guides', 'regions'] as $lama) {
            Schema::dropIfExists($lama);
        }
        Schema::enableForeignKeyConstraints();

        // 2. users tetap standar Laravel (id, name, phone), ditambah dua kolom untuk alur guide
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username', 100)->nullable()->unique();
            }
            if (! Schema::hasColumn('users', 'wajib_ganti_password')) {
                $table->boolean('wajib_ganti_password')->default(false);
            }
        });

        // 3. Tabel sesuai arahan dosen
        Schema::create('kategori_wisata', function (Blueprint $table) {
            $table->id('id_kategori');
            $table->string('nama_kategori', 100)->unique();
            $table->timestamps();
        });

        Schema::create('area', function (Blueprint $table) {
            $table->id('id_area');
            $table->string('nama_area', 100);
            $table->string('kabupaten', 100);
            $table->timestamps();
        });

        Schema::create('objek_wisata', function (Blueprint $table) {
            $table->id('id_objek');
            $table->string('nama_objek');
            $table->foreignId('id_kategori')->constrained('kategori_wisata', 'id_kategori')->restrictOnDelete();
            $table->foreignId('id_area')->constrained('area', 'id_area')->restrictOnDelete();
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });

        Schema::create('paket_wisata', function (Blueprint $table) {
            $table->id('id_paket');
            $table->string('nama_paket');
            $table->unsignedInteger('harga_paket');            // harga per peserta (Rupiah)
            $table->unsignedTinyInteger('persen_fee_guide')->default(0); // 0-100
            $table->string('durasi', 50)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('detail_paket_wisata', function (Blueprint $table) {
            $table->id('id_detail_paket');
            $table->foreignId('id_paket')->constrained('paket_wisata', 'id_paket')->cascadeOnDelete();
            $table->foreignId('id_objek')->constrained('objek_wisata', 'id_objek')->restrictOnDelete();
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->unique(['id_paket', 'id_objek']);
            $table->timestamps();
        });

        Schema::create('guide', function (Blueprint $table) {
            $table->id('id_guide');
            $table->foreignId('id_user')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->string('nama_guide');
            $table->string('email');
            $table->string('no_wa', 20)->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->string('alamat')->nullable();
            $table->string('bio_singkat', 500)->nullable();
            $table->string('bio_lengkap_file')->nullable();     // path file yang diunggah
            $table->decimal('rating', 3, 2)->default(0);
            $table->string('status_pendaftaran', 20)->default('menunggu'); // menunggu, disetujui, ditolak
            $table->text('alasan_penolakan')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_persetujuan')->nullable();
            $table->timestamps();
        });

        Schema::create('jadwal_guide', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->foreignId('id_guide')->constrained('guide', 'id_guide')->cascadeOnDelete();
            $table->date('tanggal_aktif');
            $table->unique(['id_guide', 'tanggal_aktif']);
            $table->timestamps();
        });

        Schema::create('tour_guide', function (Blueprint $table) {
            $table->id('id_tourguide');
            $table->foreignId('id_guide')->constrained('guide', 'id_guide')->cascadeOnDelete();
            $table->foreignId('id_paket')->constrained('paket_wisata', 'id_paket')->cascadeOnDelete();
            $table->unique(['id_guide', 'id_paket']);
            $table->timestamps();
        });

        Schema::create('booking', function (Blueprint $table) {
            $table->id('id_booking');
            $table->string('kode_booking', 20)->unique();
            $table->foreignId('id_wisatawan')->constrained('users')->restrictOnDelete();
            $table->foreignId('id_paket')->constrained('paket_wisata', 'id_paket')->restrictOnDelete();
            $table->foreignId('id_guide')->constrained('guide', 'id_guide')->restrictOnDelete();
            $table->dateTime('tanggal_transaksi');
            $table->date('tanggal_booking');
            $table->unsignedInteger('jumlah_peserta');
            $table->text('catatan')->nullable();
            $table->unsignedInteger('harga_satuan');
            $table->unsignedInteger('total_bayar');
            $table->unsignedInteger('fee_guide')->default(0);
            $table->string('kode_va', 30)->nullable();
            $table->string('id_transaksi_gateway', 100)->nullable();
            $table->dateTime('batas_bayar')->nullable();
            $table->dateTime('tanggal_bayar')->nullable();
            $table->string('status', 20)->default('belum_bayar'); // belum_bayar, sudah_bayar, dikonfirmasi, dibatalkan
            $table->string('alasan_batal')->nullable();
            $table->foreignId('dikonfirmasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('tanggal_konfirmasi')->nullable();
            $table->string('status_bayar_guide', 20)->default('belum'); // belum, sudah
            $table->dateTime('tanggal_bayar_guide')->nullable();
            $table->index(['id_guide', 'tanggal_booking']);
            $table->timestamps();
        });

        // 4. Tabel tambahan: notifikasi
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('id_notifikasi');
            $table->foreignId('id_user')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_booking')->nullable()->constrained('booking', 'id_booking')->cascadeOnDelete();
            $table->string('jenis', 30);
            $table->string('judul');
            $table->text('isi');
            $table->boolean('dibaca')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach (['notifikasi', 'booking', 'tour_guide', 'jadwal_guide', 'guide', 'detail_paket_wisata',
                  'paket_wisata', 'objek_wisata', 'area', 'kategori_wisata'] as $tabel) {
            Schema::dropIfExists($tabel);
        }
        Schema::enableForeignKeyConstraints();
        // Tabel lama tidak dibuat ulang. Untuk kembali ke rancangan lama, pulihkan dari backup database.
    }
};
