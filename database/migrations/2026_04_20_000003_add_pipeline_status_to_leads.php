<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('reference_code')->unique()->after('id');
            $table->enum('pipeline_status', [
                'new',
                'contacted',
                'interviewing',
                'accepted',
                'rejected',
                'withdrawn',
            ])->default('new')->after('reference_code');
            $table->integer('age')->nullable()->after('phone');
            $table->string('employment_status')->nullable()->after('age');
            $table->string('education_level')->nullable()->after('employment_status');
            $table->text('goals')->nullable()->after('education_level');
            $table->boolean('wants_scholarship')->default(false)->after('goals');
            $table->boolean('guardian_consent')->nullable()->after('wants_scholarship');
            $table->text('admin_notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'reference_code',
                'pipeline_status',
                'age',
                'employment_status',
                'education_level',
                'goals',
                'wants_scholarship',
                'guardian_consent',
                'admin_notes',
            ]);
        });
    }
};
