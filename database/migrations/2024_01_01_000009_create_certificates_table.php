<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Primary key is the human-readable certificate ID e.g. "WZ-2026-00042"
        Schema::create('certificates', function (Blueprint $table) {
            $table->string('id')->primary(); // "WZ-2026-00042"
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->timestamp('issued_at');
            $table->string('pdf_url')->nullable();
            $table->unsignedInteger('verification_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
