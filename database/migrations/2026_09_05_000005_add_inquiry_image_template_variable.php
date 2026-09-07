<?php

use App\Models\EmailTemplate;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $template = EmailTemplate::query()->where('key', 'artwork_inquiry_response')->first();
        if (! $template) return;

        $variables = $template->available_variables ?: [];
        $variables['artworkImageUrl'] = 'Artwork image URL';
        $template->update(['available_variables' => $variables]);
    }

    public function down(): void
    {
        $template = EmailTemplate::query()->where('key', 'artwork_inquiry_response')->first();
        if (! $template) return;

        $variables = $template->available_variables ?: [];
        unset($variables['artworkImageUrl']);
        $template->update(['available_variables' => $variables]);
    }
};
