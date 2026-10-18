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
        Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
    $table->string('order_id')->unique();
    $table->string('snap_token')->nullable();
    $table->string('payment_type')->nullable();
    $table->string('status')->default('pending'); // pending, paid, failed, expired
    $table->timestamp('paid_at')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
