<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artwork_email_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('artwork_sale_id')->nullable()->constrained('artwork_sales')->nullOnDelete();
            $table->string('template_key')->nullable();
            $table->string('recipient_email');
            $table->string('subject')->nullable();
            $table->string('status')->default('pending');
            $table->json('payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();

            $table->index(['artwork_sale_id', 'status']);
            $table->index(['recipient_email', 'created_at']);
        });

        if (Schema::hasTable('artwork_sales') && Schema::hasColumn('artwork_sales', 'invoice_sent_at')) {
            DB::table('artwork_sales')
                ->whereNotNull('invoice_sent_at')
                ->orderBy('id')
                ->get()
                ->each(function (object $sale): void {
                    DB::table('artwork_email_logs')->insert([
                        'artwork_sale_id' => $sale->id,
                        'template_key' => 'artwork_sale_invoice',
                        'recipient_email' => $sale->buyer_email ?: 'unknown',
                        'subject' => 'Invoice ' . ($sale->invoice_number ?: ''),
                        'status' => 'sent',
                        'payload' => json_encode([
                            'invoice_number' => $sale->invoice_number,
                            'currency' => $sale->currency ?? 'BDT',
                            'total' => $sale->sold_price,
                        ]),
                        'sent_at' => $sale->invoice_sent_at,
                        'created_at' => $sale->invoice_sent_at,
                        'updated_at' => $sale->invoice_sent_at,
                    ]);
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('artwork_email_logs');
    }
};
