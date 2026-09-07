<?php

namespace App\Support;

use Illuminate\Support\Collection;

class ActivityProperties
{
    public static function get(mixed $properties, string $key, mixed $default = null): mixed
    {
        return data_get(static::toArray($properties), $key, $default);
    }

    public static function toArray(mixed $properties): array
    {
        if ($properties instanceof Collection) {
            return $properties->toArray();
        }

        if (is_array($properties)) {
            return $properties;
        }

        if (is_object($properties) && method_exists($properties, 'toArray')) {
            return $properties->toArray();
        }

        return [];
    }

    public static function toJson(mixed $properties): string
    {
        return json_encode(
            static::toArray($properties),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        ) ?: '-';
    }
}
