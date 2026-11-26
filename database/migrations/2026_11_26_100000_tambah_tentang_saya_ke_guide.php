<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('guide', 'tentang_saya')) {
            return;   // sudah ada (misalnya ditambah manual lewat phpMyAdmin)
        }

        Schema::table('guide', function (Blueprint $table) {
            $table->text('tentang_saya')->nullable()->after('pengalaman');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('guide', 'tentang_saya')) {
            Schema::table('guide', function (Blueprint $table) {
                $table->dropColumn('tentang_saya');
            });
        }
    }
};