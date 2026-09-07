<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artists', function (Blueprint $table): void {
            if (! Schema::hasColumn('artists', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('artists', 'email')) {
                $table->string('email')->nullable()->after('name')->unique();
            }

            if (! Schema::hasColumn('artists', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            if (! Schema::hasColumn('artists', 'approval_status')) {
                $table->string('approval_status')->default('approved')->after('phone')->index();
            }

            if (! Schema::hasColumn('artists', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approval_status');
            }
        });

        Schema::table('artworks', function (Blueprint $table): void {
            if (! Schema::hasColumn('artworks', 'submitted_by_artist')) {
                $table->boolean('submitted_by_artist')->default(false)->after('note')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table): void {
            if (Schema::hasColumn('artworks', 'submitted_by_artist')) {
                $table->dropColumn('submitted_by_artist');
            }
        });

        Schema::table('artists', function (Blueprint $table): void {
            foreach (['approved_at', 'approval_status', 'phone', 'email', 'user_id'] as $column) {
                if (Schema::hasColumn('artists', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
