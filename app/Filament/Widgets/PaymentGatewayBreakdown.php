<?php

namespace App\Filament\Widgets;

use App\Models\CourseEnrollment;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PaymentGatewayBreakdown extends Widget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.payment-gateway-breakdown';

    public static function canView(): bool
    {
        return auth()->user()?->can('View:PaymentGatewayBreakdown') ?? false;
    }

    /**
     * @return Collection<int, object>
     */
    public function getRows(): Collection
    {
        return Cache::remember('admin.dashboard.payment_gateway_breakdown', now()->addMinutes(2), fn (): Collection => CourseEnrollment::query()
            ->selectRaw('gateway_code')
            ->selectRaw('COALESCE(gateway_name, gateway_code, ?) as gateway_name', ['Unknown'])
            ->selectRaw('COUNT(*) as total_enrollments')
            ->selectRaw("SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_enrollments")
            ->selectRaw("SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_enrollments")
            ->selectRaw("SUM(CASE WHEN payment_status = 'failed' THEN 1 ELSE 0 END) as failed_enrollments")
            ->selectRaw("SUM(CASE WHEN payment_status = 'paid' THEN amount ELSE 0 END) as paid_amount")
            ->selectRaw('MAX(created_at) as latest_enrollment_at')
            ->groupBy('gateway_code', 'gateway_name')
            ->orderByDesc('total_enrollments')
            ->get());
    }

    public function money(mixed $amount): string
    {
        return 'BDT ' . number_format((float) $amount, 0);
    }
}
