<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->dropForeign(['id_wisatawan']);
        });

        Schema::rename('customer', 'wisatawan');

        Schema::table('booking', function (Blueprint $table) {
            $table->foreign('id_wisatawan')
                ->references('id_wisatawan')
                ->on('wisatawan')
                ->restrictOnDelete();
        });

        DB::table('users')->where('role', 'customer')->update(['role' => 'wisatawan']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('wisatawan')->change();
        });
    }

    public function down(): void
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->dropForeign(['id_wisatawan']);
        });

        Schema::rename('wisatawan', 'customer');

        Schema::table('booking', function (Blueprint $table) {
            $table->foreign('id_wisatawan')
                ->references('id_wisatawan')
                ->on('customer')
                ->restrictOnDelete();
        });

        DB::table('users')->where('role', 'wisatawan')->update(['role' => 'customer']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->change();
        });
    }
};
