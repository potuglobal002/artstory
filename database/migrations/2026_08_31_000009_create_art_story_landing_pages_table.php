<?php

use App\Models\LandingPage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('landing_pages')) {
            Schema::create('landing_pages', function (Blueprint $table): void {
                $table->id();
                $table->boolean('is_active')->default(true);
                $table->string('route_path')->default('/');
                $table->string('top_bar_text')->nullable();
                $table->string('top_bar_cta_label')->nullable();
                $table->string('top_bar_cta_url')->nullable();
                $table->json('nav_links')->nullable();
                $table->string('hero_eyebrow')->nullable();
                $table->string('hero_title')->nullable();
                $table->text('hero_subtitle')->nullable();
                $table->string('hero_cta_label')->nullable();
                $table->string('hero_cta_url')->nullable();
                $table->string('hero_secondary_label')->nullable();
                $table->string('hero_secondary_url')->nullable();
                $table->string('hero_video_url')->nullable();
                $table->string('hero_background_path')->nullable();
                $table->json('journey_items')->nullable();
                $table->json('services')->nullable();
                $table->json('programs')->nullable();
                $table->json('trust_stats')->nullable();
                $table->json('approach_steps')->nullable();
                $table->json('success_stories')->nullable();
                $table->json('blog_posts')->nullable();
                $table->string('footer_heading')->nullable();
                $table->text('footer_note')->nullable();
                $table->timestamps();
            });
        }

        // Some deployments already have the original landing_pages table. Add
        // the fields used by the seed below before creating its default record.
        Schema::table('landing_pages', function (Blueprint $table): void {
            if (! Schema::hasColumn('landing_pages', 'featured_artwork_ids')) {
                $table->json('featured_artwork_ids')->nullable()->after('programs');
            }

            if (! Schema::hasColumn('landing_pages', 'featured_artist_ids')) {
                $table->json('featured_artist_ids')->nullable()->after('featured_artwork_ids');
            }

            if (! Schema::hasColumn('landing_pages', 'exhibition_section')) {
                $table->json('exhibition_section')->nullable()->after('featured_artist_ids');
            }

            if (! Schema::hasColumn('landing_pages', 'exhibition_image_path')) {
                $table->string('exhibition_image_path')->nullable()->after('exhibition_section');
            }

            if (! Schema::hasColumn('landing_pages', 'event_section')) {
                $table->json('event_section')->nullable()->after('exhibition_image_path');
            }
        });

        LandingPage::query()->firstOrCreate([], LandingPage::defaults());
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
