<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('artwork_payment_methods')) {
            Schema::create('artwork_payment_methods', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        foreach (['Cash Payment', 'Bank Transfer', 'Auction Sale'] as $method) {
            DB::table('artwork_payment_methods')->updateOrInsert(
                ['name' => $method],
                ['is_active' => true, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        Schema::table('artwork_sales', function (Blueprint $table) {
            if (! Schema::hasColumn('artwork_sales', 'payment_method_id')) {
                $table->foreignId('payment_method_id')->nullable()->after('buyer_id')->constrained('artwork_payment_methods')->nullOnDelete();
            }

            if (! Schema::hasColumn('artwork_sales', 'paid_by')) {
                $table->string('paid_by')->nullable()->after('sold_price');
            }

            if (! Schema::hasColumn('artwork_sales', 'invoice_number')) {
                $table->string('invoice_number')->nullable()->after('paid_by');
                $table->unique('invoice_number', 'artwork_sales_invoice_number_unique');
            }

            if (! Schema::hasColumn('artwork_sales', 'invoice_sent_at')) {
                $table->timestamp('invoice_sent_at')->nullable()->after('invoice_number');
            }
        });

        DB::table('artwork_sales')
            ->whereNull('invoice_number')
            ->orderBy('id')
            ->get(['id'])
            ->each(fn (object $sale): int => DB::table('artwork_sales')
                ->where('id', $sale->id)
                ->update(['invoice_number' => 'ASI-' . str_pad((string) $sale->id, 6, '0', STR_PAD_LEFT)]));
    }

    public function down(): void
    {
        Schema::table('artwork_sales', function (Blueprint $table) {
            if (Schema::hasColumn('artwork_sales', 'payment_method_id')) {
                $table->dropConstrainedForeignId('payment_method_id');
            }

            if (Schema::hasColumn('artwork_sales', 'paid_by')) {
                $table->dropColumn('paid_by');
            }

            if (Schema::hasColumn('artwork_sales', 'invoice_number')) {
                $table->dropUnique('artwork_sales_invoice_number_unique');
                $table->dropColumn('invoice_number');
            }

            if (Schema::hasColumn('artwork_sales', 'invoice_sent_at')) {
                $table->dropColumn('invoice_sent_at');
            }
        });

        Schema::dropIfExists('artwork_payment_methods');
    }
};
