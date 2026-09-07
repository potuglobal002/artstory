<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class EnrollmentStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return auth()->user()?->can('View:EnrollmentStatsOverview') ?? false;
    }

    protected function getStats(): array
    {
        $stats = Cache::remember('admin.dashboard.enrollment_stats', now()->addMinutes(2), fn (): array => [
            'totalCourses' => Course::query()->count(),
            'activeCourses' => Course::query()->where('is_active', true)->count(),
            'totalEnrollments' => CourseEnrollment::query()->count(),
            'paidEnrollments' => CourseEnrollment::query()->where('payment_status', 'paid')->count(),
            'pendingEnrollments' => CourseEnrollment::query()->where('payment_status', 'pending')->count(),
            'paidAmount' => CourseEnrollment::query()
                ->where('payment_status', 'paid')
                ->sum('amount'),
        ]);

        return [
            Stat::make('Total courses', number_format($stats['totalCourses']))
                ->description(number_format($stats['activeCourses']) . ' active courses')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),
            Stat::make('Total enrollments', number_format($stats['totalEnrollments']))
                ->description(number_format($stats['paidEnrollments']) . ' paid, ' . number_format($stats['pendingEnrollments']) . ' pending')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),
            Stat::make('Paid revenue', 'BDT ' . number_format((float) $stats['paidAmount'], 0))
                ->description('Confirmed enrollment payments')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
