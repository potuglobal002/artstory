<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('exhibition_enabled')->default(true);
            $table->boolean('events_pr_enabled')->default(true);
            $table->string('about_eyebrow')->nullable();
            $table->string('about_title')->nullable();
            $table->text('about_description')->nullable();
            $table->string('about_image_path')->nullable();
            $table->string('about_goal_title')->nullable();
            $table->text('about_goal_text')->nullable();
            $table->string('about_problem_title')->nullable();
            $table->text('about_problem_text')->nullable();
            $table->string('about_offer_title')->nullable();
            $table->text('about_offer_text')->nullable();
            $table->string('about_csr_title')->nullable();
            $table->text('about_csr_text')->nullable();
            $table->string('contact_eyebrow')->nullable();
            $table->string('contact_title')->nullable();
            $table->text('contact_description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'exhibition_enabled', 'events_pr_enabled', 'about_eyebrow', 'about_title',
                'about_description', 'about_image_path', 'about_goal_title', 'about_goal_text',
                'about_problem_title', 'about_problem_text', 'about_offer_title', 'about_offer_text',
                'about_csr_title', 'about_csr_text', 'contact_eyebrow', 'contact_title', 'contact_description',
            ]);
        });
    }
};
