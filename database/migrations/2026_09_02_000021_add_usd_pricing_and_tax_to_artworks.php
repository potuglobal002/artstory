<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table): void {
            if (! Schema::hasColumn('artworks', 'usd_price')) {
                $table->decimal('usd_price', 12, 2)->nullable()->after('selling_price');
            }

            if (! Schema::hasColumn('artworks', 'usd_selling_price')) {
                $table->decimal('usd_selling_price', 12, 2)->nullable()->after('usd_price');
            }

            if (! Schema::hasColumn('artworks', 'previous_usd_price')) {
                $table->decimal('previous_usd_price', 12, 2)->nullable()->after('previous_selling_price');
            }

            if (! Schema::hasColumn('artworks', 'previous_usd_selling_price')) {
                $table->decimal('previous_usd_selling_price', 12, 2)->nullable()->after('previous_usd_price');
            }
        });

        Schema::table('artwork_price_versions', function (Blueprint $table): void {
            if (! Schema::hasColumn('artwork_price_versions', 'usd_price')) {
                $table->decimal('usd_price', 12, 2)->nullable()->after('selling_price');
            }

            if (! Schema::hasColumn('artwork_price_versions', 'usd_selling_price')) {
                $table->decimal('usd_selling_price', 12, 2)->nullable()->after('usd_price');
            }
        });

        Schema::create('artwork_tax_rates', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->default('Sales Tax');
            $table->string('country_code', 8)->nullable();
            $table->string('country_name')->default('International');
            $table->foreignId('payment_method_id')->nullable()->constrained('artwork_payment_methods')->nullOnDelete();
            $table->string('currency', 3)->default('BDT');
            $table->decimal('percentage', 8, 3)->default(0);
            $table->unsignedInteger('version')->default(1);
            $table->boolean('is_tax_included')->default(false);
            $table->date('effective_from')->nullable();
            $table->date('effective_until')->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['country_code', 'currency', 'payment_method_id', 'is_active'], 'idx_artwork_tax_rates_lookup');
        });

        $cashPaymentId = DB::table('artwork_payment_methods')
            ->whereRaw('LOWER(name) = ?', ['cash payment'])
            ->value('id');

        DB::table('artwork_tax_rates')->insert([
            [
                'name' => 'Cash sale tax',
                'country_code' => 'BD',
                'country_name' => 'Bangladesh',
                'payment_method_id' => $cashPaymentId,
                'currency' => 'BDT',
                'percentage' => 0,
                'version' => 1,
                'is_tax_included' => false,
                'effective_from' => now()->toDateString(),
                'note' => 'Default: local cash sale has no tax added.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bangladesh sales tax',
                'country_code' => 'BD',
                'country_name' => 'Bangladesh',
                'payment_method_id' => null,
                'currency' => 'BDT',
                'percentage' => 15,
                'version' => 1,
                'is_tax_included' => false,
                'effective_from' => now()->toDateString(),
                'note' => 'Default: non-cash Bangladesh payment.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'International sales tax',
                'country_code' => null,
                'country_name' => 'International',
                'payment_method_id' => null,
                'currency' => 'USD',
                'percentage' => 8.875,
                'version' => 1,
                'is_tax_included' => false,
                'effective_from' => now()->toDateString(),
                'note' => 'Default: other country sale.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Schema::table('artwork_sales', function (Blueprint $table): void {
            if (! Schema::hasColumn('artwork_sales', 'currency')) {
                $table->string('currency', 3)->default('BDT')->after('payment_method_id');
            }

            if (! Schema::hasColumn('artwork_sales', 'subtotal_amount')) {
                $table->decimal('subtotal_amount', 12, 2)->nullable()->after('sold_price');
            }

            if (! Schema::hasColumn('artwork_sales', 'tax_rate_id')) {
                $table->foreignId('tax_rate_id')->nullable()->after('subtotal_amount')->constrained('artwork_tax_rates')->nullOnDelete();
            }

            if (! Schema::hasColumn('artwork_sales', 'tax_country_code')) {
                $table->string('tax_country_code', 8)->nullable()->after('tax_rate_id');
            }

            if (! Schema::hasColumn('artwork_sales', 'tax_country_name')) {
                $table->string('tax_country_name')->nullable()->after('tax_country_code');
            }

            if (! Schema::hasColumn('artwork_sales', 'tax_name')) {
                $table->string('tax_name')->nullable()->after('tax_country_name');
            }

            if (! Schema::hasColumn('artwork_sales', 'tax_percentage')) {
                $table->decimal('tax_percentage', 8, 3)->default(0)->after('tax_name');
            }

            if (! Schema::hasColumn('artwork_sales', 'tax_amount')) {
                $table->decimal('tax_amount', 12, 2)->default(0)->after('tax_percentage');
            }

            if (! Schema::hasColumn('artwork_sales', 'is_tax_included')) {
                $table->boolean('is_tax_included')->default(false)->after('tax_amount');
            }

            if (! Schema::hasColumn('artwork_sales', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->nullable()->after('is_tax_included');
            }

            if (! Schema::hasColumn('artwork_sales', 'invoice_email_send_count')) {
                $table->unsignedInteger('invoice_email_send_count')->default(0)->after('invoice_sent_at');
            }

            if (! Schema::hasColumn('artwork_sales', 'last_invoice_email_status')) {
                $table->string('last_invoice_email_status')->nullable()->after('invoice_email_send_count');
            }

            if (! Schema::hasColumn('artwork_sales', 'last_invoice_email_error')) {
                $table->text('last_invoice_email_error')->nullable()->after('last_invoice_email_status');
            }
        });

        DB::table('artwork_sales')
            ->whereNull('subtotal_amount')
            ->update([
                'currency' => 'BDT',
                'subtotal_amount' => DB::raw('sold_price'),
                'tax_percentage' => 0,
                'tax_amount' => 0,
                'is_tax_included' => false,
                'total_amount' => DB::raw('sold_price'),
            ]);
    }

    public function down(): void
    {
        Schema::table('artwork_sales', function (Blueprint $table): void {
            foreach ([
                'last_invoice_email_error',
                'last_invoice_email_status',
                'invoice_email_send_count',
                'total_amount',
                'is_tax_included',
                'tax_amount',
                'tax_percentage',
                'tax_name',
                'tax_country_name',
                'tax_country_code',
                'tax_rate_id',
                'subtotal_amount',
                'currency',
            ] as $column) {
                if (Schema::hasColumn('artwork_sales', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::dropIfExists('artwork_tax_rates');

        Schema::table('artwork_price_versions', function (Blueprint $table): void {
            foreach (['usd_selling_price', 'usd_price'] as $column) {
                if (Schema::hasColumn('artwork_price_versions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('artworks', function (Blueprint $table): void {
            foreach (['previous_usd_selling_price', 'previous_usd_price', 'usd_selling_price', 'usd_price'] as $column) {
                if (Schema::hasColumn('artworks', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
