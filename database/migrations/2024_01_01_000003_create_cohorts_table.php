<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohorts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. "Phoenix Cohort"
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('capacity');
            $table->enum('status', ['upcoming', 'active', 'completed'])->default('upcoming');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cohorts');
    }
};
