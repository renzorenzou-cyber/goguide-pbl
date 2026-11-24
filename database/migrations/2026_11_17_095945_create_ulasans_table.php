<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ulasan', function (Blueprint $table) {
            $table->increments('id_ulasan');
            $table->unsignedInteger('id_booking')->unique();
            $table->unsignedInteger('id_wisatawan');
            $table->unsignedInteger('id_guide');
            $table->unsignedTinyInteger('rating');
            $table->text('komentar')->nullable();
            $table->timestamps();

            $table->foreign('id_booking')->references('id_booking')->on('booking')->onDelete('cascade');
            $table->foreign('id_wisatawan')->references('id_wisatawan')->on('wisatawan');
            $table->foreign('id_guide')->references('id_guide')->on('guide');

            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasan');
    }
};