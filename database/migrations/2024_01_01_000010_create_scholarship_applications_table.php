<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarship_applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->foreignId('course_id')->constrained();
            $table->text('motivation');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('discount_code')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarship_applications');
    }
};
