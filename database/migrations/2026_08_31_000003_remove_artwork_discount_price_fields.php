<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('artwork_price_versions') && Schema::hasColumn('artwork_price_versions', 'discount_price')) {
            Schema::table('artwork_price_versions', function (Blueprint $table) {
                $table->dropColumn('discount_price');
            });
        }

        Schema::table('artworks', function (Blueprint $table) {
            foreach (['previous_discount_price', 'discount_price'] as $column) {
                if (Schema::hasColumn('artworks', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            if (! Schema::hasColumn('artworks', 'discount_price')) {
                $table->decimal('discount_price', 12, 2)->nullable()->after('selling_price');
            }

            if (! Schema::hasColumn('artworks', 'previous_discount_price')) {
                $table->decimal('previous_discount_price', 12, 2)->nullable()->after('previous_selling_price');
            }
        });

        if (Schema::hasTable('artwork_price_versions') && ! Schema::hasColumn('artwork_price_versions', 'discount_price')) {
            Schema::table('artwork_price_versions', function (Blueprint $table) {
                $table->decimal('discount_price', 12, 2)->nullable()->after('selling_price');
            });
        }
    }
};
