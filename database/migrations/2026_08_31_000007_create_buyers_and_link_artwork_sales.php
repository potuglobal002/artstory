<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('buyers')) {
            Schema::create('buyers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('designation')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->text('note')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['name', 'phone']);
                $table->index('email');
            });
        }

        Schema::table('artwork_sales', function (Blueprint $table) {
            if (! Schema::hasColumn('artwork_sales', 'buyer_id')) {
                $table->foreignId('buyer_id')->nullable()->after('artist_id')->constrained('buyers')->nullOnDelete();
            }

            if (! Schema::hasColumn('artwork_sales', 'buyer_designation')) {
                $table->string('buyer_designation')->nullable()->after('buyer_name');
            }
        });

        DB::table('artwork_sales')
            ->whereNotNull('buyer_name')
            ->orderBy('id')
            ->get()
            ->each(function (object $sale): void {
                $buyer = DB::table('buyers')
                    ->where('name', $sale->buyer_name)
                    ->when($sale->buyer_phone, fn ($query) => $query->where('phone', $sale->buyer_phone))
                    ->first();

                $buyerId = $buyer?->id;

                if (! $buyerId) {
                    $buyerId = DB::table('buyers')->insertGetId([
                        'name' => $sale->buyer_name,
                        'phone' => $sale->buyer_phone,
                        'email' => $sale->buyer_email,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('artwork_sales')
                    ->where('id', $sale->id)
                    ->update(['buyer_id' => $buyerId]);
            });
    }

    public function down(): void
    {
        Schema::table('artwork_sales', function (Blueprint $table) {
            if (Schema::hasColumn('artwork_sales', 'buyer_id')) {
                $table->dropConstrainedForeignId('buyer_id');
            }

            if (Schema::hasColumn('artwork_sales', 'buyer_designation')) {
                $table->dropColumn('buyer_designation');
            }
        });

        Schema::dropIfExists('buyers');
    }
};
