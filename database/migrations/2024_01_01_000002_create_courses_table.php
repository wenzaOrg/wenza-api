<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('category');
            $table->text('description');
            $table->unsignedInteger('duration_weeks');
            $table->enum('format', ['cohort', 'self_paced'])->default('cohort');
            $table->unsignedBigInteger('price_ngn');
            $table->unsignedBigInteger('price_usd')->nullable();
            $table->unsignedBigInteger('scholarship_price_ngn')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
