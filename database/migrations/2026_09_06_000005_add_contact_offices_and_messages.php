<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->text('head_office_address')->nullable()->after('address');
            $table->text('new_work_office_address')->nullable()->after('head_office_address');
            $table->string('contact_form_title')->nullable()->after('contact_description');
            $table->string('contact_form_submit_label')->nullable()->after('contact_form_title');
            $table->string('contact_form_success_message')->nullable()->after('contact_form_submit_label');
        });

        Schema::create('contact_messages', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('new');
            $table->text('admin_notes')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('source')->default('contact_page');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');

        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'head_office_address',
                'new_work_office_address',
                'contact_form_title',
                'contact_form_submit_label',
                'contact_form_success_message',
            ]);
        });
    }
};
