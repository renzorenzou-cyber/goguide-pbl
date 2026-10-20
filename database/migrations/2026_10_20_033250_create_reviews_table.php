Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    $table->string('reviewer_name');
    $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
    $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
    $table->foreignId('booking_id')->nullable()->unique()->constrained()->nullOnDelete();
    $table->unsignedTinyInteger('rating'); // 1-5
    $table->text('comment');
    $table->timestamps();
});