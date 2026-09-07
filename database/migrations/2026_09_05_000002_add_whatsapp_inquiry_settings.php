<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->string('whatsapp_phone')->nullable()->after('whatsapp_url');
            $table->string('whatsapp_inquiry_label')->nullable()->after('whatsapp_phone');
            $table->text('whatsapp_inquiry_template')->nullable()->after('whatsapp_inquiry_label');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn(['whatsapp_phone', 'whatsapp_inquiry_label', 'whatsapp_inquiry_template']);
        });
    }
};
