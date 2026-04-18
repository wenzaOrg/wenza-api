<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->timestamp('due_at')->nullable();
            $table->timestamps();
        });

        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('submitted_at');
            $table->longText('content');
            $table->unsignedTinyInteger('grade')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('assignments');
    }
};
