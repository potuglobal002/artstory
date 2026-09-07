<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class DatabaseIndexManager
{
    /**
     * @return array<int, array{name: string, table: string, columns: array<int, string>, reason: string}>
     */
    public function recommendations(): array
    {
        $activityTable = (string) config('activitylog.table_name', 'activity_log');

        return [
            ['name' => 'idx_courses_active_sort', 'table' => 'courses', 'columns' => ['is_active', 'sort_order'], 'reason' => 'Frontend course listing and homepage ordering.'],
            ['name' => 'idx_courses_featured_active_sort', 'table' => 'courses', 'columns' => ['is_featured', 'is_active', 'sort_order'], 'reason' => 'Popular and featured program sections.'],
            ['name' => 'idx_courses_category_active', 'table' => 'courses', 'columns' => ['category', 'is_active'], 'reason' => 'Course category filtering.'],
            ['name' => 'idx_blogs_active_publish_sort', 'table' => 'blog_posts', 'columns' => ['is_active', 'published_at', 'sort_order'], 'reason' => 'Blog list and homepage latest posts.'],
            ['name' => 'idx_blogs_category_active', 'table' => 'blog_posts', 'columns' => ['category', 'is_active'], 'reason' => 'Blog category filtering.'],
            ['name' => 'idx_stories_active_featured_sort', 'table' => 'success_stories', 'columns' => ['is_active', 'is_featured', 'sort_order'], 'reason' => 'Success story grid and featured stories.'],
            ['name' => 'idx_journey_active_sort', 'table' => 'journey_items', 'columns' => ['is_active', 'sort_order'], 'reason' => 'Journey timeline ordering.'],
            ['name' => 'idx_gateways_active_sort', 'table' => 'payment_gateway_settings', 'columns' => ['is_active', 'sort_order'], 'reason' => 'Checkout payment method loading.'],
            ['name' => 'idx_enrollments_course_status', 'table' => 'course_enrollments', 'columns' => ['course_id', 'payment_status'], 'reason' => 'Course enrollment reports.'],
            ['name' => 'idx_enrollments_gateway_status_created', 'table' => 'course_enrollments', 'columns' => ['gateway_code', 'payment_status', 'created_at'], 'reason' => 'Dashboard payment gateway breakdown.'],
            ['name' => 'idx_enrollments_payment_status_created', 'table' => 'course_enrollments', 'columns' => ['payment_status', 'created_at'], 'reason' => 'Paid, pending and failed payment filters.'],
            ['name' => 'idx_enrollments_student_phone', 'table' => 'course_enrollments', 'columns' => ['student_phone'], 'reason' => 'Find student enrollment by phone.'],
            ['name' => 'idx_enrollments_transaction', 'table' => 'course_enrollments', 'columns' => ['transaction_id'], 'reason' => 'Payment callback lookup.'],
            ['name' => 'idx_users_active_created', 'table' => 'users', 'columns' => ['is_active', 'created_at'], 'reason' => 'User active/inactive filtering.'],
            ['name' => 'idx_activity_causer_created', 'table' => $activityTable, 'columns' => ['causer_type', 'causer_id', 'created_at'], 'reason' => 'User activity history and session audit.'],
            ['name' => 'idx_activity_subject_created', 'table' => $activityTable, 'columns' => ['subject_type', 'subject_id', 'created_at'], 'reason' => 'Record-level activity history.'],
            ['name' => 'idx_activity_event_created', 'table' => $activityTable, 'columns' => ['event', 'created_at'], 'reason' => 'Activity action filters.'],
            ['name' => 'idx_activity_created', 'table' => $activityTable, 'columns' => ['created_at'], 'reason' => 'Activity log newest-first listing.'],
        ];
    }

    public function rows(): Collection
    {
        return Cache::remember('admin.dashboard.database_indexes', now()->addMinutes(10), fn (): Collection => collect($this->recommendations())
            ->map(function (array $index): array {
                $index['available'] = $this->hasRequiredTableAndColumns($index['table'], $index['columns']);
                $index['installed'] = $index['available'] && $this->indexExists($index['table'], $index['name']);

                return $index;
            }));
    }

    public function missingCount(): int
    {
        return $this->rows()
            ->where('available', true)
            ->where('installed', false)
            ->count();
    }

    public function applyMissing(): int
    {
        $applied = 0;

        foreach ($this->recommendations() as $index) {
            if (! $this->hasRequiredTableAndColumns($index['table'], $index['columns'])) {
                continue;
            }

            if ($this->indexExists($index['table'], $index['name'])) {
                continue;
            }

            $this->createIndex($index['table'], $index['name'], $index['columns']);
            $applied++;
        }

        Cache::forget('admin.dashboard.database_indexes');

        return $applied;
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function hasRequiredTableAndColumns(string $table, array $columns): bool
    {
        if (! Schema::hasTable($table)) {
            return false;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }

    private function indexExists(string $table, string $indexName): bool
    {
        try {
            return collect(DB::select('SHOW INDEX FROM ' . $this->quoteIdentifier($table)))
                ->contains(fn (object $index): bool => (string) $index->Key_name === $indexName);
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function createIndex(string $table, string $indexName, array $columns): void
    {
        $columnList = collect($columns)
            ->map(fn (string $column): string => $this->quoteIdentifier($column))
            ->implode(', ');

        DB::statement(sprintf(
            'ALTER TABLE %s ADD INDEX %s (%s)',
            $this->quoteIdentifier($table),
            $this->quoteIdentifier($indexName),
            $columnList,
        ));
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
