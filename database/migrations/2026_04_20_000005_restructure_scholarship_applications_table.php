<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite workaround: drop unique index before dropping column
        if (config('database.default') === 'sqlite') {
            Schema::table('scholarship_applications', function (Blueprint $table) {
                $table->dropUnique(['reference']);
            });
        }

        Schema::table('scholarship_applications', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn([
                'reference',
                'full_name',
                'motivation',
                'status',
                'discount_code',
                'submitted_at',
            ]);

            // Add new columns
            $table->string('reference_code', 10)->unique()->after('id');
            $table->string('first_name', 80)->after('reference_code');
            $table->string('last_name', 80)->after('first_name');
            $table->enum('gender', ['male', 'female', 'prefer_not_to_say'])->after('phone');
            $table->string('country', 80)->after('gender');
            $table->string('state_or_city', 120)->after('country');
            $table->enum('current_status', ['student', 'graduate', 'nysc', 'employed', 'self_employed', 'unemployed', 'other'])->after('state_or_city');
            $table->enum('education_level', ['high_school', 'degree', 'masters', 'hnd', 'diploma', 'ond', 'mphil_phd', 'nce', 'other'])->after('current_status');
            $table->foreignId('cohort_id')->constrained('cohorts')->after('course_id');
            $table->enum('learning_mode', ['online', 'physical', 'hybrid'])->after('cohort_id');
            $table->boolean('wants_scholarship')->default(true)->after('learning_mode');
            $table->enum('prior_tech_experience', ['none', 'some', 'experienced'])->after('wants_scholarship');
            $table->boolean('wants_job_placement')->after('prior_tech_experience');
            $table->enum('pipeline_status', ['new', 'reviewing', 'shortlisted', 'accepted', 'waitlisted', 'rejected', 'withdrawn'])->default('new')->after('wants_job_placement');
            $table->text('admin_notes')->nullable()->after('pipeline_status');
        });
    }

    public function down(): void
    {
        Schema::table('scholarship_applications', function (Blueprint $table) {
            $table->dropForeign(['cohort_id']);
            $table->dropColumn([
                'reference_code',
                'first_name',
                'last_name',
                'gender',
                'country',
                'state_or_city',
                'current_status',
                'education_level',
                'cohort_id',
                'learning_mode',
                'wants_scholarship',
                'prior_tech_experience',
                'wants_job_placement',
                'pipeline_status',
                'admin_notes',
            ]);

            $table->string('reference')->unique()->nullable();
            $table->string('full_name')->nullable();
            $table->text('motivation')->nullable();
            $table->string('status')->nullable();
            $table->string('discount_code')->nullable();
            $table->timestamp('submitted_at')->nullable();
        });
    }
};
