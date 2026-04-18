<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->unsignedInteger('order');
            $table->enum('content_type', ['video', 'text', 'quiz']);
            $table->string('content_url')->nullable();
            $table->longText('content_body')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
