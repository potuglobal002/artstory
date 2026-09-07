<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_settings')->orderBy('id')->each(function (object $settings): void {
            $links = json_decode($settings->footer_collector_links ?? '[]', true);

            if (! is_array($links)) {
                return;
            }

            $updated = false;

            foreach ($links as &$link) {
                if (($link['label'] ?? null) === 'Catalogue') {
                    $link['label'] = 'Artwork';
                    $updated = true;
                }
            }
            unset($link);

            if ($updated) {
                DB::table('site_settings')
                    ->where('id', $settings->id)
                    ->update([
                        'footer_collector_links' => json_encode($links),
                        'updated_at' => now(),
                    ]);
            }
        });
    }

    public function down(): void
    {
        // Preserve administrator-managed footer labels when rolling back.
    }
};
