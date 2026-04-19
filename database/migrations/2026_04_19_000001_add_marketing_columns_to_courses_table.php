<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->json('tools_and_technologies')->nullable()->after('thumbnail_url');
            $table->json('career_outcomes')->nullable()->after('tools_and_technologies');
            $table->json('what_youll_learn')->nullable()->after('career_outcomes');
            $table->json('faqs')->nullable()->after('what_youll_learn');
            $table->text('prerequisites')->nullable()->after('faqs');
            $table->boolean('is_featured')->default(false)->after('is_published');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'tools_and_technologies',
                'career_outcomes',
                'what_youll_learn',
                'faqs',
                'prerequisites',
                'is_featured',
            ]);
        });
    }
};
