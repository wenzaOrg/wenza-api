<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Add curriculum as JSON (default empty array)
            if (! Schema::hasColumn('courses', 'curriculum')) {
                $table->json('curriculum')->after('faqs')->nullable();
            }

            // Add outcomes as JSON (default empty array)
            // Note: 'what_youll_learn' exists, but we'll add 'outcomes' as requested
            if (! Schema::hasColumn('courses', 'outcomes')) {
                $table->json('outcomes')->after('curriculum')->nullable();
            }

            // about_mdx as TEXT (long prose)
            if (! Schema::hasColumn('courses', 'about_mdx')) {
                $table->text('about_mdx')->after('outcomes')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['curriculum', 'outcomes', 'about_mdx']);
        });
    }
};
