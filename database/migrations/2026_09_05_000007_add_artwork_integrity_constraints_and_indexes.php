<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table): void {
            foreach ([
                'idx_artworks_active_status' => ['is_active', 'status'],
                'idx_artworks_artist_active' => ['artist_id', 'is_active'],
                'idx_artworks_active_selling_price' => ['is_active', 'selling_price'],
                'idx_artworks_active_usd_selling_price' => ['is_active', 'usd_selling_price'],
            ] as $name => $columns) {
                if (! collect(Schema::getIndexes('artworks'))->contains('name', $name)) {
                    $table->index($columns, $name);
                }
            }
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE artworks ADD CONSTRAINT chk_artworks_canvas_count CHECK (canvas_count >= 1)');
            DB::statement('ALTER TABLE artworks ADD CONSTRAINT chk_artworks_non_negative_prices CHECK ((price IS NULL OR price >= 0) AND (selling_price IS NULL OR selling_price >= 0) AND (usd_price IS NULL OR usd_price >= 0) AND (usd_selling_price IS NULL OR usd_selling_price >= 0))');
            DB::statement("ALTER TABLE artwork_sales ADD CONSTRAINT chk_artwork_sales_currency CHECK (currency IN ('BDT', 'USD'))");
            DB::statement('ALTER TABLE artwork_sales ADD CONSTRAINT chk_artwork_sales_non_negative_amounts CHECK ((sold_price IS NULL OR sold_price >= 0) AND (subtotal_amount IS NULL OR subtotal_amount >= 0) AND tax_amount >= 0 AND (total_amount IS NULL OR total_amount >= 0) AND tax_percentage >= 0)');
            DB::statement("ALTER TABLE artwork_tax_rates ADD CONSTRAINT chk_artwork_tax_rates_currency CHECK (currency IN ('BDT', 'USD'))");
            DB::statement('ALTER TABLE artwork_tax_rates ADD CONSTRAINT chk_artwork_tax_rates_percentage CHECK (percentage >= 0)');
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE artwork_tax_rates DROP CHECK chk_artwork_tax_rates_currency');
            DB::statement('ALTER TABLE artwork_tax_rates DROP CHECK chk_artwork_tax_rates_percentage');
            DB::statement('ALTER TABLE artwork_sales DROP CHECK chk_artwork_sales_currency');
            DB::statement('ALTER TABLE artwork_sales DROP CHECK chk_artwork_sales_non_negative_amounts');
            DB::statement('ALTER TABLE artworks DROP CHECK chk_artworks_canvas_count');
            DB::statement('ALTER TABLE artworks DROP CHECK chk_artworks_non_negative_prices');
        }

        Schema::table('artworks', function (Blueprint $table): void {
            $table->dropIndex('idx_artworks_active_usd_selling_price');
            $table->dropIndex('idx_artworks_active_selling_price');
            $table->dropIndex('idx_artworks_artist_active');
            $table->dropIndex('idx_artworks_active_status');
        });
    }
};
