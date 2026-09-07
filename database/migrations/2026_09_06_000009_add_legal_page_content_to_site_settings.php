<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->string('privacy_policy_title')->nullable()->after('footer_collector_links');
            $table->text('privacy_policy_content')->nullable()->after('privacy_policy_title');
            $table->string('terms_conditions_title')->nullable()->after('privacy_policy_content');
            $table->text('terms_conditions_content')->nullable()->after('terms_conditions_title');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'privacy_policy_title',
                'privacy_policy_content',
                'terms_conditions_title',
                'terms_conditions_content',
            ]);
        });
    }
};
