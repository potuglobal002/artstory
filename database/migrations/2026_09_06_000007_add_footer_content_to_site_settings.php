<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->text('footer_brand_text')->nullable()->after('contact_form_success_message');
            $table->string('footer_sections_heading')->nullable()->after('footer_brand_text');
            $table->string('footer_collectors_heading')->nullable()->after('footer_sections_heading');
            $table->string('footer_contact_heading')->nullable()->after('footer_collectors_heading');
            $table->string('footer_copyright_text')->nullable()->after('footer_contact_heading');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'footer_brand_text',
                'footer_sections_heading',
                'footer_collectors_heading',
                'footer_contact_heading',
                'footer_copyright_text',
            ]);
        });
    }
};
