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
            if (! Schema::hasColumn('mock_test_registrations', 'invoice_number')) {
                $table->string('invoice_number')->nullable()->after('registration_status');
                $table->unique('invoice_number', 'mock_test_registrations_invoice_number_unique');
            }
        });
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::table('mock_test_registrations', function (Blueprint $table): void {
            if (Schema::hasColumn('mock_test_registrations', 'invoice_number')) {
                try {
                    $table->dropUnique('mock_test_registrations_invoice_number_unique');
                } catch (Throwable) {
                }

                $table->dropColumn('invoice_number');
            }
        });
    }
};
