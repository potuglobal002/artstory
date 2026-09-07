<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            if (! Schema::hasColumn('artworks', 'price')) {
                $table->decimal('price', 12, 2)->nullable()->after('year');
            }

            if (! Schema::hasColumn('artworks', 'status')) {
                $table->string('status', 30)->default('available')->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            if (Schema::hasColumn('artworks', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('artworks', 'price')) {
                $table->dropColumn('price');
            }
        });
    }
};
