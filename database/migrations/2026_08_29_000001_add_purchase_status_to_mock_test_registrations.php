<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::table('mock_test_registrations', function (Blueprint $table): void {
            if (! Schema::hasColumn('mock_test_registrations', 'mock_test_purchase_status')) {
                $table->string('mock_test_purchase_status')
                    ->nullable()
                    ->after('invoice_number')
                    ->index('idx_mock_reg_purchase_status');
            }
        });
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::table('mock_test_registrations', function (Blueprint $table): void {
            if (Schema::hasColumn('mock_test_registrations', 'mock_test_purchase_status')) {
                $table->dropIndex('idx_mock_reg_purchase_status');
                $table->dropColumn('mock_test_purchase_status');
            }
        });
    }
};
