<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        $settings = DB::table('site_settings')->orderBy('id')->first();

        if (! $settings) {
            return;
        }

        $privacyPolicy = <<<'HTML'
<p><strong>Last updated: September 6, 2026</strong></p>
<p>ART Story respects your privacy. This Privacy Policy explains how we collect, use, store, and protect information when you visit our website, explore artworks, submit an inquiry, register as an artist, or otherwise contact us.</p>
<h2>Information we collect</h2>
<p>We may collect your name, email address, phone or WhatsApp number, message content, and the artwork or service you ask us about. When an artist uses the portal, we may also collect profile, artwork, pricing, and supporting information needed to review and present their work.</p>
<h2>How we use your information</h2>
<ul><li>To respond to inquiries and provide requested information.</li><li>To manage artwork, artist, exhibition, event, and collector relationships.</li><li>To send service-related messages and, where permitted, relevant ART Story updates.</li><li>To improve the security, performance, and experience of our website.</li></ul>
<h2>Sharing information</h2>
<p>We do not sell personal information. We may share limited information with trusted service providers that help us operate the website, communicate with visitors, process payments, or deliver services. We may also disclose information where required by law or to protect the rights, safety, and property of ART Story, our artists, collectors, and visitors.</p>
<h2>Cookies and analytics</h2>
<p>Our website may use cookies or similar technologies to remember preferences, understand website use, and improve performance. You can manage cookies through your browser settings; some website features may not work as expected when cookies are disabled.</p>
<h2>Data security and retention</h2>
<p>We use reasonable administrative and technical measures to protect personal information. No online service can guarantee absolute security. We keep information only for as long as it is needed for the purpose it was collected, to meet legal obligations, or to resolve disputes.</p>
<h2>Your choices</h2>
<p>You may ask us to update, correct, or delete personal information we hold about you, subject to applicable legal and operational requirements. You may also opt out of non-essential marketing messages at any time.</p>
<h2>Contact us</h2>
<p>For privacy questions or requests, please contact ART Story through our <a href="/contact">Contact page</a>.</p>
<h2>Changes to this policy</h2>
<p>We may update this Privacy Policy when our services or legal obligations change. The latest version will always be available on this page.</p>
HTML;

        $termsConditions = <<<'HTML'
<p><strong>Last updated: September 6, 2026</strong></p>
<p>These Terms &amp; Conditions govern your use of the ART Story website, artist portal, artwork catalogue, exhibitions, events, and related services. By using our services, you agree to these terms.</p>
<h2>Using the website</h2>
<p>You may use the ART Story website for personal, lawful, and non-commercial purposes. You must not interfere with the website, attempt unauthorised access, upload harmful material, or use our content in a way that infringes another person's rights.</p>
<h2>Artwork information and availability</h2>
<p>Artwork images, dimensions, descriptions, availability, and prices are provided for general information and may change without notice. Colours may vary between screens and the original artwork. Availability is confirmed only by ART Story at the time of sale or written confirmation.</p>
<h2>Inquiries and purchases</h2>
<p>Submitting an inquiry, using WhatsApp, or adding an item to a request does not create a purchase agreement or reserve an artwork. A sale is confirmed only after ART Story accepts the order and confirms the applicable price, currency, taxes, payment method, delivery arrangement, and any other required terms.</p>
<h2>Payments, delivery, and returns</h2>
<p>Payment, delivery, installation, insurance, and return terms may vary by artwork, location, and buyer arrangement. Any agreed terms will be communicated during the purchase process. Buyers are responsible for providing accurate delivery and contact information.</p>
<h2>Artist portal</h2>
<p>Artists are responsible for ensuring that submitted artwork, images, descriptions, and pricing information are accurate and that they have the necessary rights to submit them. ART Story may review, edit for presentation, accept, decline, unpublish, or remove submissions at its discretion.</p>
<h2>Intellectual property</h2>
<p>Artwork, photography, text, branding, logos, and website design remain the property of their respective owners and are protected by applicable intellectual-property laws. You may not reproduce, distribute, alter, or commercially use any content without written permission from the relevant rights holder.</p>
<h2>Third-party links</h2>
<p>Our website may contain links to third-party websites or services. ART Story is not responsible for their content, availability, privacy practices, or terms.</p>
<h2>Limitation of liability</h2>
<p>To the extent permitted by law, ART Story is not liable for indirect, incidental, special, or consequential loss arising from use of the website or reliance on catalogue information. Nothing in these terms limits liability where it cannot legally be limited.</p>
<h2>Changes and contact</h2>
<p>We may update these Terms &amp; Conditions from time to time. Continued use of the website after an update means you accept the revised terms. For questions, please contact ART Story through our <a href="/contact">Contact page</a>.</p>
HTML;

        $updates = ['updated_at' => now()];

        if (blank($settings->privacy_policy_title)) {
            $updates['privacy_policy_title'] = 'Privacy Policy';
        }

        if (blank($settings->privacy_policy_content)) {
            $updates['privacy_policy_content'] = $privacyPolicy;
        }

        if (blank($settings->terms_conditions_title)) {
            $updates['terms_conditions_title'] = 'Terms & Conditions';
        }

        if (blank($settings->terms_conditions_content)) {
            $updates['terms_conditions_content'] = $termsConditions;
        }

        DB::table('site_settings')->where('id', $settings->id)->update($updates);
    }

    public function down(): void
    {
        // Default legal content is intentionally retained on rollback.
    }
};
