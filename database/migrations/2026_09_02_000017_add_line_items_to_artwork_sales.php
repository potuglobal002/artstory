<?php

use App\Models\Artwork;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artwork_sales', function ($table) {
            if (! Schema::hasColumn('artwork_sales', 'line_items')) {
                $table->json('line_items')->nullable()->after('sold_price');
            }
        });

        DB::table('artwork_sales')
            ->whereNull('line_items')
            ->whereNotNull('artwork_id')
            ->orderBy('id')
            ->get(['id', 'artwork_id', 'sold_price'])
            ->each(function (object $sale): void {
                $artwork = Artwork::query()
                    ->with(['artist', 'style', 'subjectStyle', 'medium', 'size'])
                    ->find($sale->artwork_id);

                if (! $artwork) {
                    return;
                }

                DB::table('artwork_sales')
                    ->where('id', $sale->id)
                    ->update([
                        'line_items' => json_encode([[
                            'artwork_id' => $artwork->id,
                            'title' => $artwork->title ?: 'Untitled',
                            'artist_name' => $artwork->artist?->name ?: 'Unknown Artist',
                            'year' => $artwork->year,
                            'style' => $artwork->style?->name,
                            'subject_style' => $artwork->subjectStyle?->name,
                            'medium' => $artwork->medium?->name,
                            'size' => $artwork->size?->name,
                            'image_path' => $artwork->image_path,
                            'sold_price' => (float) ($sale->sold_price ?: $artwork->displayPrice() ?: 0),
                        ]]),
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('artwork_sales', function ($table) {
            if (Schema::hasColumn('artwork_sales', 'line_items')) {
                $table->dropColumn('line_items');
            }
        });
    }
};
