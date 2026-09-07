<?php

namespace App\Providers;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use App\Support\DynamicMailSettings;
use App\Models\User;
use Spatie\Activitylog\Facades\Activity;
use Spatie\Permission\Events\RoleAttached;
use Spatie\Permission\Events\RoleDetached;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentShield::enforcePolicies();
        DynamicMailSettings::apply();

        Event::listen(RoleAttached::class, function (RoleAttached $event): void {
            if (! $event->model instanceof User) {
                return;
            }

            $roles = $this->resolveRoleNames($event->rolesOrIds);
            $details = 'Role assigned: ' . ($roles === [] ? 'Unknown role' : implode(', ', $roles));
            $logger = Activity::event('role_attached')
                ->useLog('User')
                ->performedOn($event->model)
                ->withProperties([
                    'module' => 'User',
                    'action' => 'Role Assigned',
                    'record_label' => $event->model->name,
                    'record_id' => $event->model->getKey(),
                    'roles' => $roles,
                    'details_text' => $details,
                ]);

            auth()->check()
                ? $logger->causedBy(auth()->user())
                : $logger->causedByAnonymous();

            $logger->log($details);
        });

        Event::listen(RoleDetached::class, function (RoleDetached $event): void {
            if (! $event->model instanceof User) {
                return;
            }

            $roles = $this->resolveRoleNames($event->rolesOrIds);
            $details = 'Role removed: ' . ($roles === [] ? 'Unknown role' : implode(', ', $roles));
            $logger = Activity::event('role_detached')
                ->useLog('User')
                ->performedOn($event->model)
                ->withProperties([
                    'module' => 'User',
                    'action' => 'Role Removed',
                    'record_label' => $event->model->name,
                    'record_id' => $event->model->getKey(),
                    'roles' => $roles,
                    'details_text' => $details,
                ]);

            auth()->check()
                ? $logger->causedBy(auth()->user())
                : $logger->causedByAnonymous();

            $logger->log($details);
        });

        Event::listen(Login::class, function (Login $event): void {
            $loginAt = now();

            if (request()->hasSession()) {
                session()->put('activity_login_at', $loginAt->toIso8601String());
            }

            Activity::event('login')
                ->useLog('Authentication')
                ->causedBy($event->user)
                ->performedOn($event->user)
                ->withProperties([
                    'module' => 'Authentication',
                    'action' => 'Login',
                    'record_label' => $event->user->name,
                    'record_id' => $event->user->getKey(),
                    'details_text' => 'User logged in to the admin system.',
                    'login_at' => $loginAt->toDateTimeString(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ])
                ->log('User logged in');
        });

        Event::listen(Logout::class, function (Logout $event): void {
            if (! $event->user) {
                return;
            }

            $logoutAt = now();
            $loginAt = request()->hasSession() ? session()->pull('activity_login_at') : null;
            $durationSeconds = $loginAt ? (int) Carbon::parse($loginAt)->diffInSeconds($logoutAt) : null;

            Activity::event('logout')
                ->useLog('Authentication')
                ->causedBy($event->user)
                ->performedOn($event->user)
                ->withProperties([
                    'module' => 'Authentication',
                    'action' => 'Logout',
                    'record_label' => $event->user->name,
                    'record_id' => $event->user->getKey(),
                    'details_text' => $durationSeconds !== null
                        ? 'User logged out after ' . gmdate('H:i:s', $durationSeconds) . '.'
                        : 'User logged out. Login time was not available.',
                    'login_at' => $loginAt ? Carbon::parse($loginAt)->toDateTimeString() : null,
                    'logout_at' => $logoutAt->toDateTimeString(),
                    'session_duration_seconds' => $durationSeconds,
                    'session_duration' => $durationSeconds !== null ? gmdate('H:i:s', $durationSeconds) : null,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ])
                ->log('User logged out');
        });
    }

    /**
     * @return array<int, string>
     */
    protected function resolveRoleNames(mixed $rolesOrIds): array
    {
        $roles = collect(is_iterable($rolesOrIds) ? $rolesOrIds : [$rolesOrIds]);

        $names = $roles
            ->filter(fn (mixed $role): bool => is_object($role) && isset($role->name))
            ->map(fn (object $role): string => (string) $role->name);

        $ids = $roles
            ->reject(fn (mixed $role): bool => is_object($role) && isset($role->name))
            ->filter(fn (mixed $role): bool => is_numeric($role))
            ->values();

        if ($ids->isNotEmpty()) {
            $names = $names->merge(Role::query()
                ->whereIn('id', $ids->all())
                ->pluck('name'));
        }

        $stringNames = $roles
            ->reject(fn (mixed $role): bool => is_object($role) || is_numeric($role))
            ->map(fn (mixed $role): string => (string) $role);

        return $names
            ->merge($stringNames)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
