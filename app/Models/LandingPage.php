<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

class LandingPage extends BaseModel
{
    protected $fillable = [
        'is_active',
        'route_path',
        'top_bar_text',
        'top_bar_cta_label',
        'top_bar_cta_url',
        'nav_links',
        'hero_eyebrow',
        'hero_title',
        'hero_subtitle',
        'hero_cta_label',
        'hero_cta_url',
        'hero_secondary_label',
        'hero_secondary_url',
        'hero_video_url',
        'hero_background_path',
        'about_image_path',
        'journey_items',
        'services',
        'programs',
        'featured_artwork_ids',
        'featured_artist_ids',
        'exhibition_section',
        'exhibition_image_path',
        'event_section',
        'trust_stats',
        'approach_steps',
        'success_stories',
        'blog_posts',
        'footer_heading',
        'footer_note',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'nav_links' => 'array',
            'journey_items' => 'array',
            'services' => 'array',
            'programs' => 'array',
            'featured_artwork_ids' => 'array',
            'featured_artist_ids' => 'array',
            'exhibition_section' => 'array',
            'event_section' => 'array',
            'trust_stats' => 'array',
            'approach_steps' => 'array',
            'success_stories' => 'array',
            'blog_posts' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], static::defaults());
    }

    public static function defaults(): array
    {
        return [
            'is_active' => true,
            'route_path' => '/',
            'top_bar_text' => 'Every Art has a story. Let us be part of yours.',
            'top_bar_cta_label' => 'Explore artworks',
            'top_bar_cta_url' => '/artworks',
            'nav_links' => [
                ['label' => 'About', 'url' => '#about-art-story'],
                ['label' => 'Featured', 'url' => '#featured-artworks'],
                ['label' => 'Artists', 'url' => '#featured-artists'],
                ['label' => 'Contact', 'url' => '/contact'],
            ],
            'hero_eyebrow' => 'ART Story',
            'hero_title' => 'Every Art has a story. Let us be part of yours.',
            'hero_subtitle' => '',
            'hero_cta_label' => 'Start Exploring',
            'hero_cta_url' => '/artworks',
            'hero_secondary_label' => 'Meet Artists',
            'hero_secondary_url' => '/artists',
            'journey_items' => [
                ['year' => 'About', 'title' => 'About Us', 'description' => 'Art Story is a global platform focused primarily on helping emerging artists achieve their passion.'],
                ['year' => 'Goal', 'title' => 'Our Goal', 'description' => 'To empower local talents and create global career opportunities for them in the world of art.'],
                ['year' => 'CSR', 'title' => 'Our CSR', 'description' => 'ART Story pledges to contribute part of its proceeds to a non-profit organization.'],
            ],
            'services' => [
                ['title' => 'About Us', 'description' => 'Art Story helps creative souls move forward with their careers by connecting them with art lovers around the globe.', 'accent' => 'about'],
                ['title' => 'Our Goal', 'description' => 'To empower local talents and create global career opportunities for them in the world of art.', 'accent' => 'goal'],
                ['title' => 'Problem ART Story Solves', 'description' => 'Many artists work for years without access to exhibitions, galleries, receptions, or buyers. ART Story helps bring those dreams closer to reality.', 'accent' => 'problem'],
                ['title' => 'Our CSR', 'description' => 'ART Story believes in giving back and pledges part of its proceeds to a non-profit organization.', 'accent' => 'csr'],
                ['title' => 'What We Offer', 'description' => 'We help talented artists showcase their art in international platforms and move closer to a sustainable career.', 'accent' => 'offer'],
            ],
            'programs' => [
                ['title' => 'Featured Artworks', 'category' => 'Selected Works', 'description' => 'A curated selection from the ART Story catalogue.', 'duration' => '', 'url' => '/artworks'],
                ['title' => 'Featured Artists', 'category' => 'Artists', 'description' => 'Creative souls we support and showcase.', 'duration' => '', 'url' => '/artists'],
            ],
            'featured_artwork_ids' => [],
            'featured_artist_ids' => [],
            'exhibition_section' => [
                'eyebrow' => 'Exhibitions / Upcoming Events',
                'title' => 'Discover ART Story beyond the catalogue.',
                'description' => 'Explore curated selections, upcoming previews, and collector-focused moments around emerging artists.',
                'exhibition_cta_label' => 'Exhibitions',
                'event_cta_label' => 'Upcoming Events',
            ],
            'event_section' => [
                ['date' => '15', 'month' => 'Sep', 'title' => 'Collector Preview', 'location' => 'Online Viewing Room', 'time' => '7:00 PM', 'description' => 'A curated ART Story event featuring selected works, artist context, and collection guidance.'],
                ['date' => '28', 'month' => 'Sep', 'title' => 'Emerging Artist Showcase', 'location' => 'ART Story Catalogue', 'time' => '6:30 PM', 'description' => 'A focused preview for discovering artists and original works from the ART Story catalogue.'],
                ['date' => '12', 'month' => 'Oct', 'title' => 'Studio Stories Session', 'location' => 'Virtual Event', 'time' => '8:00 PM', 'description' => 'Meet the ideas, process, and stories behind selected artworks.'],
            ],
            'trust_stats' => [
                ['value' => '20K+', 'label' => 'Learners guided', 'description' => 'Students supported across programs and counselling.'],
                ['value' => '95%', 'label' => 'Satisfaction', 'description' => 'Learners report clearer next steps after joining.'],
                ['value' => '50+', 'label' => 'Mentors', 'description' => 'Specialists across language, admission and career support.'],
                ['value' => '9', 'label' => 'Teams', 'description' => 'Focused teams for operations, content, IT, PR and more.'],
            ],
            'approach_steps' => [
                ['title' => 'Assess', 'description' => 'Understand the learner level, target and timeline.'],
                ['title' => 'Plan', 'description' => 'Create a practical route with classes, tasks and mentoring.'],
                ['title' => 'Practice', 'description' => 'Use repeated exercises, feedback and real-world assignments.'],
                ['title' => 'Succeed', 'description' => 'Track progress and move confidently toward the goal.'],
            ],
            'success_stories' => [
                ['name' => 'Nusrat Jahan', 'meta' => 'IELTS learner', 'quote' => 'The practice plan helped me stay consistent and confident.', 'result' => 'Band score improved'],
                ['name' => 'Arif Hossain', 'meta' => 'Admission counselling', 'quote' => 'I finally understood which documents mattered and when to prepare them.', 'result' => 'Application submitted'],
                ['name' => 'Maliha Rahman', 'meta' => 'Spoken English', 'quote' => 'The speaking sessions made daily communication much easier.', 'result' => 'Fluency improved'],
            ],
            'blog_posts' => [
                ['title' => 'How to choose the right study pathway', 'excerpt' => 'Simple questions that help students make stronger academic decisions.', 'date' => 'Aug 2026', 'url' => '#'],
                ['title' => 'A practical IELTS preparation routine', 'excerpt' => 'A weekly routine for balanced listening, reading, writing and speaking.', 'date' => 'Aug 2026', 'url' => '#'],
                ['title' => 'What makes language learning sustainable', 'excerpt' => 'Why feedback, habit and real conversations matter more than memorizing.', 'date' => 'Aug 2026', 'url' => '#'],
            ],
            'footer_heading' => 'Let ART Story be part of yours',
            'footer_note' => 'Manage this landing page from the admin dashboard whenever your story, featured content or campaign changes.',
        ];
    }

    public function heroBackgroundUrl(): ?string
    {
        return $this->hero_background_path ? Storage::disk('public')->url($this->hero_background_path) : null;
    }

    public function aboutImageUrl(): ?string
    {
        return $this->about_image_path ? Storage::disk('public')->url($this->about_image_path) : null;
    }

    public function exhibitionImageUrl(): ?string
    {
        return $this->exhibition_image_path ? Storage::disk('public')->url($this->exhibition_image_path) : null;
    }
}
