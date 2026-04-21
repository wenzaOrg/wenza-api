<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('reference_code', 10)->unique();
            $table->string('full_name', 120);
            $table->string('email', 200);
            $table->enum('subject', ['application_question', 'scholarship_question', 'press_partnerships', 'other']);
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('replied_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
