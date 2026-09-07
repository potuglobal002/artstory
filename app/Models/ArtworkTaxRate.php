<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtworkTaxRate extends BaseModel
{
    protected $fillable = [
        'name',
        'country_code',
        'country_name',
        'payment_method_id',
        'currency',
        'percentage',
        'version',
        'is_tax_included',
        'effective_from',
        'effective_until',
        'note',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'percentage' => 'decimal:3',
            'version' => 'integer',
            'is_tax_included' => 'boolean',
            'effective_from' => 'date',
            'effective_until' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $taxRate): void {
            $taxRate->currency = strtoupper($taxRate->currency ?: 'BDT');
            $taxRate->version ??= ((int) static::query()
                ->where('country_code', $taxRate->country_code)
                ->where('country_name', $taxRate->country_name)
                ->where('payment_method_id', $taxRate->payment_method_id)
                ->where('currency', $taxRate->currency)
                ->max('version')) + 1;
        });
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(ArtworkPaymentMethod::class, 'payment_method_id');
    }

    public function label(): string
    {
        $payment = $this->paymentMethod?->name ?: 'Any payment';
        $country = $this->country_code ? "{$this->country_name} ({$this->country_code})" : $this->country_name;

        return "{$country} - {$payment} - {$this->currency} {$this->percentage}% - v{$this->version}";
    }

    public function snapshot(): array
    {
        return [
            'tax_country_code' => $this->country_code,
            'tax_country_name' => $this->country_name,
            'tax_name' => $this->name,
            'tax_percentage' => (float) $this->percentage,
            'is_tax_included' => (bool) $this->is_tax_included,
        ];
    }
}
