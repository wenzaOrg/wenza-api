<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // SQLite workaround: drop index explicitly before dropping the column
            if (config('database.default') === 'sqlite') {
                $table->dropUnique(['reference']);
            }

            // Drop legacy columns that were replaced by the new lead application system
            $table->dropColumn(['reference', 'status', 'referral_source', 'motivation']);
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('reference')->unique();
            $table->string('referral_source')->nullable();
            $table->text('motivation')->nullable();
            $table->string('status')->default('new');
        });
    }
};
