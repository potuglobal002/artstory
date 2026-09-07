<?php

namespace App\Models\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

trait FormatsActivityLog
{
    public function tapActivity(Activity $activity, string $eventName): void
    {
        $properties = $activity->properties instanceof Collection
            ? $activity->properties
            : collect($activity->properties ?? []);

        $attributes = collect($properties->get('attributes', []));
        $old = collect($properties->get('old', []));
        $changedFields = $attributes->keys()
            ->merge($old->keys())
            ->unique()
            ->reject(fn (string $field): bool => in_array($field, ['password', 'remember_token', 'updated_at'], true))
            ->values();

        $details = $changedFields
            ->mapWithKeys(fn (string $field): array => [
                $field => [
                    'from' => $old->has($field) ? $this->formatActivityValue($old->get($field)) : null,
                    'to' => $attributes->has($field) ? $this->formatActivityValue($attributes->get($field)) : null,
                ],
            ])
            ->all();

        $detailsText = $changedFields
            ->map(function (string $field) use ($eventName, $old, $attributes): string {
                $label = Str::headline($field);
                $from = $old->has($field) ? $this->formatActivityValue($old->get($field)) : null;
                $to = $attributes->has($field) ? $this->formatActivityValue($attributes->get($field)) : null;

                return match ($eventName) {
                    'created' => "{$label} set to {$to}",
                    'deleted' => "{$label} was {$from}",
                    default => "{$label} changed from {$from} to {$to}",
                };
            })
            ->implode('; ');

        $activity->properties = $properties
            ->put('module', $this->getActivityModuleName())
            ->put('action', Str::headline($eventName))
            ->put('record_label', $this->getActivityRecordLabel())
            ->put('record_id', $this->getKey())
            ->put('changed_fields', $changedFields->all())
            ->put('details', $details)
            ->put('details_text', $detailsText ?: 'No field-level changes captured.');

        $activity->description = sprintf(
            '%s %s: %s',
            $this->getActivityModuleName(),
            Str::headline($eventName),
            $this->getActivityRecordLabel(),
        );
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return sprintf(
            '%s %s: %s',
            $this->getActivityModuleName(),
            Str::headline($eventName),
            $this->getActivityRecordLabel(),
        );
    }

    protected function getActivityModuleName(): string
    {
        return Str::headline(class_basename(static::class));
    }

    protected function getActivityRecordLabel(): string
    {
        foreach (['name', 'title', 'email'] as $attribute) {
            if (filled($value = $this->getAttribute($attribute))) {
                return (string) $value;
            }
        }

        return '#' . ($this->getKey() ?? 'new');
    }

    protected function formatActivityValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if ($value === null || $value === '') {
            return '-';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '-';
        }

        return '"' . (string) $value . '"';
    }
}
