<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artwork_inquiries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('artwork_id')->nullable()->constrained()->nullOnDelete();
            $table->string('artwork_code')->nullable();
            $table->string('artwork_title')->nullable();
            $table->string('artist_name')->nullable();
            $table->string('name');
            $table->string('whatsapp');
            $table->string('email')->nullable();
            $table->text('message');
            $table->string('status')->default('new');
            $table->text('admin_notes')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('source')->default('artwork_page');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artwork_inquiries');
    }
};
