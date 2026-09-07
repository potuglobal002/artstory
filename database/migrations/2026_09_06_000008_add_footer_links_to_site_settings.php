<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->json('footer_section_links')->nullable()->after('footer_copyright_text');
            $table->json('footer_collector_links')->nullable()->after('footer_section_links');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn(['footer_section_links', 'footer_collector_links']);
        });
    }
};
