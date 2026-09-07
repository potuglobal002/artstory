<?php

namespace App\Console\Commands;

use Database\Seeders\RoleSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync {--panel=admin : Filament panel ID to scan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize Shield permissions, predefined roles, and Admin permissions.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $panel = (string) $this->option('panel');

        $this->call('shield:generate', [
            '--all' => true,
            '--panel' => $panel,
            '--ignore-existing-policies' => true,
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->removeStaleGeneratedPermissions();

        foreach (RoleSeeder::ROLES as $roleName) {
            Role::findOrCreate($roleName, 'web');
        }

        Role::findByName('Admin', 'web')
            ->syncPermissions(Permission::query()->pluck('name')->all());

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->components->info('Permissions synchronized and Admin role refreshed.');

        return self::SUCCESS;
    }

    protected function removeStaleGeneratedPermissions(): void
    {
        $staleActionPrefixes = [
            'DeleteAny',
            'ForceDelete',
            'ForceDeleteAny',
            'Reorder',
            'Replicate',
            'Restore',
            'RestoreAny',
        ];

        $removedSubjects = [
            'Course',
            'CourseEnrollment',
            'JourneyItem',
            'PaymentGatewaySetting',
            'SuccessStory',
        ];

        $stalePermissions = Permission::query()
            ->where(function ($query) use ($staleActionPrefixes): void {
                foreach ($staleActionPrefixes as $prefix) {
                    $query->orWhere('name', 'like', "{$prefix}:%");
                }
            })
            ->orWhereIn('name', [
                'Create:Activity',
                'Update:Activity',
                'Delete:Activity',
            ])
            ->orWhere(function ($query) use ($removedSubjects): void {
                foreach ($removedSubjects as $subject) {
                    $query->orWhere('name', 'like', "%:{$subject}");
                }
            })
            ->pluck('id');

        if ($stalePermissions->isEmpty()) {
            return;
        }

        DB::table(config('permission.table_names.role_has_permissions'))
            ->whereIn(config('permission.column_names.permission_pivot_key') ?: 'permission_id', $stalePermissions)
            ->delete();

        DB::table(config('permission.table_names.model_has_permissions'))
            ->whereIn(config('permission.column_names.permission_pivot_key') ?: 'permission_id', $stalePermissions)
            ->delete();

        Permission::query()
            ->whereIn('id', $stalePermissions)
            ->delete();

        $this->components->info('Stale advanced, removed module, and Activity Log write permissions removed.');
    }
}
