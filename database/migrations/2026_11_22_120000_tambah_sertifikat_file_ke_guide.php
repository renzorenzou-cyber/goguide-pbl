<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guide', function (Blueprint $table) {
            $table->string('sertifikat_file', 255)->nullable()->after('bio_lengkap_file');
        });
    }

    public function down(): void
    {
        Schema::table('guide', function (Blueprint $table) {
            $table->dropColumn('sertifikat_file');
        });
    }
};