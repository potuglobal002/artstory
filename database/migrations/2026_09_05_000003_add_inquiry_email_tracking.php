<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artwork_email_logs', function (Blueprint $table): void {
            $table->foreignId('artwork_inquiry_id')->nullable()->after('artwork_sale_id')->constrained('artwork_inquiries')->nullOnDelete();
            $table->index(['artwork_inquiry_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('artwork_email_logs', function (Blueprint $table): void {
            $table->dropForeign(['artwork_inquiry_id']);
            $table->dropIndex(['artwork_inquiry_id', 'status']);
            $table->dropColumn('artwork_inquiry_id');
        });
    }
};
