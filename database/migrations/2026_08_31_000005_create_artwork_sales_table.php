<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('artwork_sales')) {
            Schema::create('artwork_sales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('artwork_id')->constrained('artworks')->cascadeOnDelete();
                $table->foreignId('artist_id')->nullable()->constrained('artists')->nullOnDelete();
                $table->date('sold_at')->nullable();
                $table->decimal('sold_price', 12, 2)->nullable();
                $table->string('buyer_name')->nullable();
                $table->string('buyer_phone')->nullable();
                $table->string('buyer_email')->nullable();
                $table->text('note')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['artist_id', 'sold_at']);
                $table->index(['artwork_id', 'sold_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('artwork_sales');
    }
};
