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
       Schema::create('tours', function (Blueprint $table) {
    $table->id();
    $table->foreignId('region_id')->constrained()->cascadeOnDelete();
    $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->unsignedInteger('price');
    $table->string('duration')->nullable();
    $table->string('image')->nullable();
    $table->decimal('rating', 3, 2)->default(0);
    $table->unsignedInteger('review_count')->default(0);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
