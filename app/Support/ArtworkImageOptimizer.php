<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ArtworkImageOptimizer
{
    private const MAX_DIMENSION = 3000;

    private const WEBP_QUALITY = 95;

    public static function store(TemporaryUploadedFile|UploadedFile $file): string
    {
        $image = self::createImage($file->getRealPath());

        if (! $image) {
            return $file->store('artworks', 'public');
        }

        $width = imagesx($image);
        $height = imagesy($image);
        [$targetWidth, $targetHeight] = self::targetDimensions($width, $height);

        if ($targetWidth !== $width || $targetHeight !== $height) {
            $resized = imagecreatetruecolor($targetWidth, $targetHeight);
            imagealphablending($resized, true);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        $filename = 'artworks/' . Str::uuid() . '.webp';
        $absolutePath = Storage::disk('public')->path($filename);

        if (! is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0775, true);
        }

        imagepalettetotruecolor($image);
        imagewebp($image, $absolutePath, self::WEBP_QUALITY);
        imagedestroy($image);

        return $filename;
    }

    private static function createImage(string $path): mixed
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            return false;
        }

        return @imagecreatefromstring($contents);
    }

    private static function targetDimensions(int $width, int $height): array
    {
        $largestSide = max($width, $height);

        if ($largestSide <= self::MAX_DIMENSION) {
            return [$width, $height];
        }

        $ratio = self::MAX_DIMENSION / $largestSide;

        return [
            max(1, (int) round($width * $ratio)),
            max(1, (int) round($height * $ratio)),
        ];
    }
}
