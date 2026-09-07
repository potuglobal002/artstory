<?php

namespace App\Models;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ArtworkSale extends BaseModel
{
    private const SUPPORTED_CURRENCIES = ['BDT', 'USD'];

    private const IMMUTABLE_AFTER_INVOICE_FIELDS = [
        'artwork_id', 'artist_id', 'buyer_id', 'payment_method_id', 'tax_rate_id',
        'sold_at', 'currency', 'sold_price', 'subtotal_amount', 'tax_country_code',
        'tax_country_name', 'tax_name', 'tax_percentage', 'tax_amount',
        'is_tax_included', 'total_amount', 'line_items', 'paid_by', 'buyer_name',
        'buyer_designation', 'buyer_phone', 'buyer_email', 'note',
    ];

    protected $fillable = [
        'artwork_id',
        'artist_id',
        'buyer_id',
        'payment_method_id',
        'tax_rate_id',
        'sold_at',
        'currency',
        'sold_price',
        'subtotal_amount',
        'tax_country_code',
        'tax_country_name',
        'tax_name',
        'tax_percentage',
        'tax_amount',
        'is_tax_included',
        'total_amount',
        'line_items',
        'paid_by',
        'invoice_number',
        'invoice_sent_at',
        'invoice_email_send_count',
        'last_invoice_email_status',
        'last_invoice_email_error',
        'buyer_name',
        'buyer_designation',
        'buyer_phone',
        'buyer_email',
        'note',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sold_at' => 'date',
            'sold_price' => 'decimal:2',
            'subtotal_amount' => 'decimal:2',
            'tax_percentage' => 'decimal:3',
            'tax_amount' => 'decimal:2',
            'is_tax_included' => 'boolean',
            'total_amount' => 'decimal:2',
            'line_items' => 'array',
            'invoice_sent_at' => 'datetime',
            'invoice_email_send_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $sale): void {
            $sale->currency = strtoupper($sale->currency ?: 'BDT');
            $sale->validateData();

            if ($sale->exists && $sale->getOriginal('invoice_sent_at') && $sale->isDirty(self::IMMUTABLE_AFTER_INVOICE_FIELDS)) {
                throw new \LogicException('An invoiced artwork sale cannot change its financial or artwork snapshot.');
            }

            $sale->applyTaxRateSnapshot();

            if (! empty($sale->line_items)) {
                $lineItems = static::normalizeLineItems($sale->line_items, $sale->currency);
                $firstItem = $lineItems[0] ?? null;

                $sale->line_items = $lineItems;
                $sale->artwork_id = $firstItem['artwork_id'] ?? $sale->artwork_id;
                $sale->artist_id = $firstItem['artist_id'] ?? $sale->artist_id;
                $sale->subtotal_amount = collect($lineItems)->sum(fn (array $item): float => (float) ($item['sold_price'] ?? 0));
                $sale->syncTotals();
                $sale->sold_at ??= now()->toDateString();
                $sale->invoice_number ??= static::makeInvoiceNumber();

                if ($sale->buyer) {
                    $sale->buyer_name = $sale->buyer->name;
                    $sale->buyer_designation = $sale->buyer->designation;
                    $sale->buyer_phone = $sale->buyer->phone;
                    $sale->buyer_email = $sale->buyer->email;
                }

                if ($sale->paymentMethod) {
                    $sale->paid_by = $sale->paymentMethod->name;
                }

                return;
            }

            $artwork = $sale->artwork;

            if (! $artwork) {
                return;
            }

            $sale->artist_id = $artwork->artist_id;
            $sale->sold_at ??= now()->toDateString();
            $sale->subtotal_amount ??= $artwork->displayPrice($sale->currency);
            $sale->syncTotals();
            $sale->invoice_number ??= static::makeInvoiceNumber();

            if ($sale->buyer) {
                $sale->buyer_name = $sale->buyer->name;
                $sale->buyer_designation = $sale->buyer->designation;
                $sale->buyer_phone = $sale->buyer->phone;
                $sale->buyer_email = $sale->buyer->email;
            }

            if ($sale->paymentMethod) {
                $sale->paid_by = $sale->paymentMethod->name;
            }
        });

        static::saved(function (self $sale): void {
            $sale->displayLineItems()->each(function (array $item) use ($sale): void {
                $priceField = strtoupper($sale->currency ?: 'BDT') === 'USD'
                    ? 'usd_selling_price'
                    : 'selling_price';

                Artwork::query()
                    ->whereKey($item['artwork_id'] ?? null)
                    ->update([
                        'status' => 'sold',
                        $priceField => (float) ($item['sold_price'] ?? 0),
                    ]);
            });
        });
    }

    public function save(array $options = []): bool
    {
        return app(DatabaseManager::class)->transaction(fn (): bool => parent::save($options));
    }

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(ArtworkPaymentMethod::class, 'payment_method_id');
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(ArtworkEmailLog::class);
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(ArtworkTaxRate::class, 'tax_rate_id');
    }

    public static function makeInvoiceNumber(): string
    {
        do {
            $number = 'ASI-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (static::query()->where('invoice_number', $number)->exists());

        return $number;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    public static function normalizeLineItems(array $items, string $currency = 'BDT'): array
    {
        $currency = strtoupper($currency ?: 'BDT');

        return collect($items)
            ->filter(fn (array $item): bool => filled($item['artwork_id'] ?? null))
            ->unique(fn (array $item): mixed => $item['artwork_id'])
            ->map(function (array $item) use ($currency): array {
                $artwork = Artwork::query()
                    ->with(['artist', 'style', 'subjectStyle', 'medium', 'size'])
                    ->find($item['artwork_id']);

                if (! $artwork) {
                    return $item;
                }

                $price = filled($item['sold_price'] ?? null)
                    ? (float) $item['sold_price']
                    : (float) ($artwork->displayPrice($currency) ?: 0);

                return [
                    'artwork_id' => $artwork->id,
                    'artist_id' => $artwork->artist_id,
                    'title' => $artwork->title ?: 'Untitled',
                    'artist_name' => $artwork->artist?->name ?: 'Unknown Artist',
                    'year' => $artwork->year,
                    'style' => $artwork->style?->name,
                    'subject_style' => $artwork->subjectStyle?->name,
                    'medium' => $artwork->medium?->name,
                    'size' => $artwork->size?->name,
                    'image_path' => $artwork->image_path,
                    'currency' => $currency,
                    'sold_price' => $price,
                ];
            })
            ->values()
            ->all();
    }

    public function displayLineItems(): Collection
    {
        if (! empty($this->line_items)) {
            return collect($this->line_items);
        }

        if (! $this->artwork) {
            return collect();
        }

        return collect([[
            'artwork_id' => $this->artwork->id,
            'artist_id' => $this->artist_id,
            'title' => $this->artwork->title ?: 'Untitled',
            'artist_name' => $this->artist?->name ?: 'Unknown Artist',
            'year' => $this->artwork->year,
            'style' => $this->artwork->style?->name,
            'subject_style' => $this->artwork->subjectStyle?->name,
            'medium' => $this->artwork->medium?->name,
            'size' => $this->artwork->size?->name,
            'image_path' => $this->artwork->image_path,
            'currency' => $this->currency ?: 'BDT',
            'sold_price' => (float) $this->sold_price,
        ]]);
    }

    public function lineItemsTotal(): float
    {
        return $this->displayLineItems()->sum(fn (array $item): float => (float) ($item['sold_price'] ?? 0));
    }

    public function subtotal(): float
    {
        return (float) ($this->subtotal_amount ?? $this->lineItemsTotal());
    }

    public function totalPaid(): float
    {
        return (float) ($this->total_amount ?? $this->sold_price ?? $this->subtotal());
    }

    public function calculateTaxAmount(float $subtotal): float
    {
        $percentage = (float) ($this->tax_percentage ?? 0);

        if ($percentage <= 0) {
            return 0.0;
        }

        return $this->is_tax_included
            ? round($subtotal - ($subtotal / (1 + ($percentage / 100))), 2)
            : round($subtotal * ($percentage / 100), 2);
    }

    public function calculateTotal(float $subtotal): float
    {
        return $this->is_tax_included
            ? round($subtotal, 2)
            : round($subtotal + $this->calculateTaxAmount($subtotal), 2);
    }

    public function formattedMoney(mixed $value): string
    {
        $currency = strtoupper($this->currency ?: 'BDT');
        $amount = (float) $value;

        return $currency.' '.number_format($amount, $currency === 'BDT' ? 0 : 2);
    }

    public function lineItemsLabel(): string
    {
        $count = $this->displayLineItems()->count();

        return $count === 1 ? '1 artwork' : "{$count} artworks";
    }

    private function applyTaxRateSnapshot(): void
    {
        $taxRate = $this->tax_rate_id ? ArtworkTaxRate::query()->find($this->tax_rate_id) : null;

        if (! $taxRate) {
            return;
        }

        foreach ($taxRate->snapshot() as $field => $value) {
            if ($this->{$field} === null || (! $this->isDirty($field) && in_array($field, ['tax_percentage', 'is_tax_included'], true))) {
                $this->{$field} = $value;
            }
        }
    }

    private function syncTotals(): void
    {
        $subtotal = (float) ($this->subtotal_amount ?? 0);
        $taxAmount = $this->calculateTaxAmount($subtotal);

        $this->tax_amount = $taxAmount;
        $this->total_amount = $this->calculateTotal($subtotal);
        $this->sold_price = $this->total_amount;
    }

    private function validateData(): void
    {
        if (! in_array($this->currency, self::SUPPORTED_CURRENCIES, true)) {
            throw new \InvalidArgumentException('Artwork sales support only BDT and USD currencies.');
        }

        foreach (['sold_price', 'subtotal_amount', 'tax_amount', 'total_amount'] as $field) {
            if ($this->{$field} !== null && (float) $this->{$field} < 0) {
                throw new \InvalidArgumentException("Artwork sale {$field} cannot be negative.");
            }
        }

        if ($this->tax_percentage !== null && (float) $this->tax_percentage < 0) {
            throw new \InvalidArgumentException('Artwork sale tax percentage cannot be negative.');
        }
    }
}
