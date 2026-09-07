<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CourseEnrollment extends BaseModel
{
    protected $fillable = [
        'uuid',
        'course_id',
        'student_name',
        'student_email',
        'student_phone',
        'guardian_phone',
        'address',
        'note',
        'amount',
        'coupon_code_id',
        'original_amount',
        'discount_amount',
        'coupon_code',
        'coupon_snapshot',
        'currency',
        'gateway_code',
        'gateway_name',
        'payment_status',
        'enrollment_status',
        'transaction_id',
        'gateway_payment_id',
        'gateway_payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'original_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'coupon_snapshot' => 'array',
            'gateway_payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $enrollment): void {
            $enrollment->uuid ??= (string) Str::uuid();
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
