<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            if (! Schema::hasColumn('artworks', 'selling_price')) {
                $table->decimal('selling_price', 12, 2)->nullable()->after('price');
            }

            if (! Schema::hasColumn('artworks', 'previous_price')) {
                $table->decimal('previous_price', 12, 2)->nullable()->after('selling_price');
            }

            if (! Schema::hasColumn('artworks', 'previous_selling_price')) {
                $table->decimal('previous_selling_price', 12, 2)->nullable()->after('previous_price');
            }

            if (! Schema::hasColumn('artworks', 'price_version')) {
                $table->unsignedInteger('price_version')->default(1)->after('previous_selling_price');
            }
        });

        if (! Schema::hasTable('artwork_price_versions')) {
            Schema::create('artwork_price_versions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('artwork_id')->constrained('artworks')->cascadeOnDelete();
                $table->decimal('price', 12, 2)->nullable();
                $table->decimal('selling_price', 12, 2)->nullable();
                $table->string('status', 30)->default('available');
                $table->unsignedInteger('version');
                $table->timestamp('effective_until')->nullable();
                $table->foreignId('changed_by_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('note')->nullable();
                $table->timestamps();

                $table->index(['artwork_id', 'version'], 'idx_artwork_price_versions_record_version');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('artwork_price_versions');

        Schema::table('artworks', function (Blueprint $table) {
            foreach ([
                'price_version',
                'previous_selling_price',
                'previous_price',
                'selling_price',
            ] as $column) {
                if (Schema::hasColumn('artworks', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
