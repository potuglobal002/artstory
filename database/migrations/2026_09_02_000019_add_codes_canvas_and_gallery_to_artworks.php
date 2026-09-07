<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artworks', function ($table) {
            if (! Schema::hasColumn('artworks', 'artwork_code')) {
                $table->string('artwork_code')->nullable()->after('artist_id');
                $table->unique('artwork_code', 'artworks_artwork_code_unique');
            }

            if (! Schema::hasColumn('artworks', 'canvas_count')) {
                $table->unsignedSmallInteger('canvas_count')->default(1)->after('image_path');
            }

            if (! Schema::hasColumn('artworks', 'gallery_images')) {
                $table->json('gallery_images')->nullable()->after('canvas_count');
            }
        });

        $counters = [];

        DB::table('artworks')
            ->leftJoin('artists', 'artworks.artist_id', '=', 'artists.id')
            ->whereNull('artworks.artwork_code')
            ->orderBy('artworks.artist_id')
            ->orderBy('artworks.id')
            ->get(['artworks.id', 'artworks.artist_id', 'artists.name as artist_name'])
            ->each(function (object $artwork) use (&$counters): void {
                $prefix = $this->artistInitials($artwork->artist_name ?: 'Artwork');
                $counterKey = (string) ($artwork->artist_id ?: $prefix);
                $counters[$counterKey] = ($counters[$counterKey] ?? 0) + 1;
                $code = $this->nextAvailableCode($prefix, $counters[$counterKey]);

                DB::table('artworks')
                    ->where('id', $artwork->id)
                    ->update([
                        'artwork_code' => $code,
                        'canvas_count' => 1,
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('artworks', function ($table) {
            if (Schema::hasColumn('artworks', 'gallery_images')) {
                $table->dropColumn('gallery_images');
            }

            if (Schema::hasColumn('artworks', 'canvas_count')) {
                $table->dropColumn('canvas_count');
            }

            if (Schema::hasColumn('artworks', 'artwork_code')) {
                $table->dropUnique('artworks_artwork_code_unique');
                $table->dropColumn('artwork_code');
            }
        });
    }

    private function artistInitials(string $name): string
    {
        $words = Str::of($name)
            ->replaceMatches('/[^A-Za-z0-9\s]/', ' ')
            ->squish()
            ->explode(' ')
            ->filter();

        $initials = $words
            ->map(fn (string $word): string => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');

        return $initials !== '' ? Str::substr($initials, 0, 4) : 'ART';
    }

    private function nextAvailableCode(string $prefix, int $sequence): string
    {
        do {
            $code = $prefix . '-P-' . str_pad((string) $sequence, 2, '0', STR_PAD_LEFT);
            $sequence++;
        } while (DB::table('artworks')->where('artwork_code', $code)->exists());

        return $code;
    }
};
