<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $medium = DB::table('artwork_mediums')
            ->where('name', 'Acrylic on Canvas')
            ->first();

        if (! $medium) {
            $mediumId = DB::table('artwork_mediums')->insertGetId([
                'name' => 'Acrylic on Canvas',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $mediumId = $medium->id;

            DB::table('artwork_mediums')
                ->where('id', $mediumId)
                ->update(['is_active' => true, 'updated_at' => $now]);
        }

        DB::table('artworks')->update([
            'artwork_medium_id' => $mediumId,
            'artist_medium_name' => null,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        // Do not discard administrator-selected artwork medium data on rollback.
    }
};
