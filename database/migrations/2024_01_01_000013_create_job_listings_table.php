<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Named job_listings to avoid collision with Laravel's queue jobs table
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('title');
            $table->text('description');
            $table->string('location');
            $table->enum('type', ['full_time', 'part_time', 'contract', 'internship']);
            $table->string('apply_url');
            $table->timestamp('posted_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
