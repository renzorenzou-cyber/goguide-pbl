<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menyelaraskan database dengan rancangan 6.3.3 di proposal.
 * Data yang sudah ada dipertahankan (kolom lama dikonversi, bukan dibuang begitu saja).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ---------- KategoriWisata: deskripsi ----------
        Schema::table('kategori_wisata', function (Blueprint $table) {
            $table->text('deskripsi')->nullable()->after('nama_kategori');
        });

        // ---------- Area: alamat, koordinat ----------
        Schema::table('area', function (Blueprint $table) {
            $table->text('alamat')->nullable()->after('kabupaten');
            $table->decimal('latitude', 10, 7)->nullable()->after('alamat');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });

        // ---------- ObjekWisata: alamat, harga tiket, status ----------
        Schema::table('objek_wisata', function (Blueprint $table) {
            $table->text('alamat')->nullable()->after('deskripsi');
            $table->decimal('harga_tiket', 12, 2)->nullable()->after('foto');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif')->after('harga_tiket');
        });

        // ---------- PaketWisata: fasilitas, status (menggantikan aktif) ----------
        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->text('fasilitas')->nullable()->after('deskripsi');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif')->after('foto');
        });
        DB::statement("UPDATE paket_wisata SET status = IF(aktif = 1, 'Aktif', 'Nonaktif')");
        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->dropColumn('aktif');
        });

        // ---------- DetailPaketWisata: durasi kunjungan, keterangan ----------
        Schema::table('detail_paket_wisata', function (Blueprint $table) {
            $table->unsignedSmallInteger('durasi_kunjungan')->nullable()->after('urutan'); // menit
            $table->text('keterangan')->nullable()->after('durasi_kunjungan');
        });

        // ---------- Guide: pengalaman, foto, status, status_verifikasi ----------
        Schema::table('guide', function (Blueprint $table) {
            $table->text('pengalaman')->nullable()->after('bio_lengkap_file');
            $table->string('foto')->nullable()->after('pengalaman');
            $table->enum('status_verifikasi', ['Menunggu', 'Terverifikasi', 'Ditolak'])->default('Menunggu')->after('rating');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif')->after('status_verifikasi');
        });
        DB::statement("UPDATE guide SET status_verifikasi = CASE status_pendaftaran
            WHEN 'disetujui' THEN 'Terverifikasi' WHEN 'ditolak' THEN 'Ditolak' ELSE 'Menunggu' END");
        Schema::table('guide', function (Blueprint $table) {
            $table->dropColumn('status_pendaftaran');
            $table->unique('email');
        });

        // ---------- JadwalGuide: jam, status, keterangan ----------
        Schema::table('jadwal_guide', function (Blueprint $table) {
            $table->time('jam_mulai')->default('08:00:00')->after('tanggal_aktif');
            $table->time('jam_selesai')->default('17:00:00')->after('jam_mulai');
            $table->enum('status', ['Tersedia', 'Tidak Tersedia'])->default('Tersedia')->after('jam_selesai');
            $table->string('keterangan')->nullable()->after('status');
        });

        // ---------- TourGuide: status, catatan ----------
        Schema::table('tour_guide', function (Blueprint $table) {
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif')->after('id_paket');
            $table->string('catatan')->nullable()->after('status');
        });

        // ---------- Customer (profil wisatawan, terhubung ke akun di users) ----------
        Schema::create('customer', function (Blueprint $table) {
            $table->id('id_wisatawan');
            $table->foreignId('id_user')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nama_wisatawan', 150);
            $table->string('email', 150)->unique();
            $table->string('telepon', 20)->nullable();
            $table->dateTime('tanggal_daftar');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();
        });

        // Isi dari akun wisatawan yang sudah ada
        DB::statement("INSERT INTO customer (id_user, nama_wisatawan, email, telepon, tanggal_daftar, status, created_at, updated_at)
            SELECT id, name, email, phone, COALESCE(created_at, NOW()), 'Aktif', NOW(), NOW()
            FROM users WHERE role = 'customer'");

        // Booking: id_wisatawan sekarang mengacu ke customer, bukan users
        Schema::table('booking', function (Blueprint $table) {
            $table->dropForeign(['id_wisatawan']);
        });
        DB::statement("UPDATE booking b JOIN customer c ON c.id_user = b.id_wisatawan SET b.id_wisatawan = c.id_wisatawan");
        Schema::table('booking', function (Blueprint $table) {
            $table->foreign('id_wisatawan')->references('id_wisatawan')->on('customer')->restrictOnDelete();
            $table->time('jam_mulai')->nullable()->default('08:00:00')->after('tanggal_booking');
        });

        // ---------- Bahasa dan DetailBahasaGuide ----------
        Schema::create('bahasa', function (Blueprint $table) {
            $table->id('id_bahasa');
            $table->string('nama_bahasa', 100)->unique();
            $table->timestamps();
        });

        Schema::create('detail_bahasa_guide', function (Blueprint $table) {
            $table->id('id_detail_bahasa');
            $table->foreignId('id_guide')->constrained('guide', 'id_guide')->cascadeOnDelete();
            $table->foreignId('id_bahasa')->constrained('bahasa', 'id_bahasa')->cascadeOnDelete();
            $table->enum('tingkat_kemampuan', ['Dasar', 'Menengah', 'Mahir', 'Fasih'])->default('Menengah');
            $table->unique(['id_guide', 'id_bahasa']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Untuk kembali ke skema sebelumnya, pulihkan dari backup database (file .sql).
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('detail_bahasa_guide');
        Schema::dropIfExists('bahasa');
        Schema::enableForeignKeyConstraints();
    }
};
