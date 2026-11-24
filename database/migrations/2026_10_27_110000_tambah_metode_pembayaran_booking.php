<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('booking', 'metode_pembayaran')) {
            Schema::table('booking', function (Blueprint $table) {
                $table->string('metode_pembayaran', 50)->nullable()->after('kode_va');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('booking', 'metode_pembayaran')) {
            Schema::table('booking', function (Blueprint $table) {
                $table->dropColumn('metode_pembayaran');
            });
        }
    }
};
