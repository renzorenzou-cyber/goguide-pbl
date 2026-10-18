<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
    $table->id();
    $table->string('booking_code')->unique();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
    $table->date('tour_date');
    $table->unsignedInteger('participants');
    $table->unsignedInteger('total_price');
    $table->string('status')->default('pending'); // pending, paid, cancelled
    $table->text('notes')->nullable();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
