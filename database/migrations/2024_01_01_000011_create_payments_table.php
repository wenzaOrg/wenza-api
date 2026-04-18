<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('enrollment_id')->constrained();
            $table->unsignedBigInteger('amount_ngn');
            $table->enum('gateway', ['paystack', 'flutterwave']);
            $table->string('reference')->unique();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
