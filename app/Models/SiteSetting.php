<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

class SiteSetting extends BaseModel
{
    protected $fillable = [
        'site_title',
        'tagline',
        'login_logo_path',
        'registration_logo_path',
        'admin_logo_path',
        'favicon_path',
        'default_og_image_path',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_robots',
        'canonical_url',
        'google_analytics_id',
        'google_tag_manager_id',
        'google_site_verification',
        'bing_site_verification',
        'facebook_pixel_id',
        'facebook_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
        'x_url',
        'whatsapp_url',
        'whatsapp_phone',
        'whatsapp_inquiry_label',
        'whatsapp_inquiry_template',
        'contact_email',
        'contact_phone',
        'address',
        'head_office_address',
        'new_work_office_address',
        'virtual_gallery_enabled',
        'artist_login_enabled',
        'exhibition_enabled',
        'events_pr_enabled',
        'about_eyebrow', 'about_title', 'about_description', 'about_image_path',
        'about_goal_title', 'about_goal_text', 'about_problem_title', 'about_problem_text',
        'about_offer_title', 'about_offer_text', 'about_csr_title', 'about_csr_text',
        'contact_eyebrow', 'contact_title', 'contact_description',
        'contact_form_title', 'contact_form_submit_label', 'contact_form_success_message',
        'footer_brand_text', 'footer_sections_heading', 'footer_collectors_heading', 'footer_contact_heading', 'footer_copyright_text',
        'footer_section_links', 'footer_collector_links',
        'privacy_policy_title', 'privacy_policy_content', 'terms_conditions_title', 'terms_conditions_content',
    ];

    protected function casts(): array
    {
        return [
            'virtual_gallery_enabled' => 'boolean',
            'artist_login_enabled' => 'boolean',
            'exhibition_enabled' => 'boolean',
            'events_pr_enabled' => 'boolean',
            'footer_section_links' => 'array',
            'footer_collector_links' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'site_title' => config('app.name', 'ART Story'),
            'meta_robots' => 'index, follow',
        ]);
    }

    public function imageUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    public function adminLogoUrl(): ?string
    {
        return $this->imageUrl($this->admin_logo_path ?: $this->login_logo_path);
    }

    public function loginLogoUrl(): ?string
    {
        return $this->imageUrl($this->login_logo_path ?: $this->admin_logo_path);
    }

    public function registrationLogoUrl(): ?string
    {
        return $this->imageUrl($this->registration_logo_path ?: $this->login_logo_path ?: $this->admin_logo_path);
    }

    public function faviconUrl(): ?string
    {
        return $this->imageUrl($this->favicon_path);
    }

    public function defaultOgImageUrl(): ?string
    {
        return $this->imageUrl($this->default_og_image_path);
    }
}
