<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::table('mock_test_registrations', function (Blueprint $table): void {
            if (! Schema::hasColumn('mock_test_registrations', 'confirmed_at')) {
                $table->timestamp('confirmed_at')
                    ->nullable()
                    ->after('registration_status')
                    ->index('idx_mock_reg_confirmed_at');
            }
        });

        DB::table('mock_test_registrations')
            ->where('registration_status', 'confirmed')
            ->whereNull('confirmed_at')
            ->update([
                'confirmed_at' => DB::raw('COALESCE(paid_at, updated_at, created_at)'),
            ]);
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::table('mock_test_registrations', function (Blueprint $table): void {
            if (Schema::hasColumn('mock_test_registrations', 'confirmed_at')) {
                $table->dropIndex('idx_mock_reg_confirmed_at');
                $table->dropColumn('confirmed_at');
            }
        });
    }
};
