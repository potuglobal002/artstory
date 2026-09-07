<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('artworks')
            ->whereNull('title')
            ->orWhere('title', '')
            ->update(['title' => 'Untitled']);

        Schema::table('artworks', function (Blueprint $table) {
            $table->string('title')->default('Untitled')->change();
        });
    }

    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->string('title')->nullable()->default(null)->change();
        });
    }
};
