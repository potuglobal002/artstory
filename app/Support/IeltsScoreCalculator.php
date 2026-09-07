<?php

namespace App\Support;

class IeltsScoreCalculator
{
    public static function listeningBand(?int $correctAnswers): ?float
    {
        return self::bandFromCorrectAnswers($correctAnswers, [
            [39, 40, 9.0],
            [37, 38, 8.5],
            [35, 36, 8.0],
            [32, 34, 7.5],
            [30, 31, 7.0],
            [26, 29, 6.5],
            [23, 25, 6.0],
            [18, 22, 5.5],
            [16, 17, 5.0],
            [13, 15, 4.5],
            [10, 12, 4.0],
            [7, 9, 3.5],
            [5, 6, 3.0],
            [3, 4, 2.5],
        ]);
    }

    public static function readingBand(?int $correctAnswers, ?string $testTypeTitle): ?float
    {
        $isGeneralTraining = str($testTypeTitle ?: '')->lower()->contains('general');

        return self::bandFromCorrectAnswers($correctAnswers, $isGeneralTraining ? [
            [40, 40, 9.0],
            [39, 39, 8.5],
            [37, 38, 8.0],
            [36, 36, 7.5],
            [34, 35, 7.0],
            [32, 33, 6.5],
            [30, 31, 6.0],
            [27, 29, 5.5],
            [23, 26, 5.0],
            [19, 22, 4.5],
            [15, 18, 4.0],
            [12, 14, 3.5],
            [9, 11, 3.0],
            [6, 8, 2.5],
        ] : [
            [39, 40, 9.0],
            [37, 38, 8.5],
            [35, 36, 8.0],
            [33, 34, 7.5],
            [30, 32, 7.0],
            [27, 29, 6.5],
            [23, 26, 6.0],
            [19, 22, 5.5],
            [15, 18, 5.0],
            [13, 14, 4.5],
            [10, 12, 4.0],
            [8, 9, 3.5],
            [6, 7, 3.0],
            [4, 5, 2.5],
        ]);
    }

    public static function writingBand(?float $taskOne, ?float $taskTwo): ?float
    {
        if ($taskOne === null || $taskTwo === null) {
            return null;
        }

        return self::nearestHalf(($taskOne + ($taskTwo * 2)) / 3);
    }

    public static function overallBand(?float $listening, ?float $reading, ?float $writing, ?float $speaking): ?float
    {
        $scores = collect([$listening, $reading, $writing, $speaking])
            ->filter(fn (?float $score): bool => $score !== null);

        if ($scores->count() !== 4) {
            return null;
        }

        return self::nearestHalf($scores->avg());
    }

    /**
     * @return array<string, string>
     */
    public static function bandOptions(): array
    {
        $options = [];

        for ($score = 0; $score <= 9; $score += 0.5) {
            $key = number_format($score, 1, '.', '');
            $options[$key] = $key;
        }

        return $options;
    }

    /**
     * @param  array<int, array{0: int, 1: int, 2: float}>  $ranges
     */
    private static function bandFromCorrectAnswers(?int $correctAnswers, array $ranges): ?float
    {
        if ($correctAnswers === null) {
            return null;
        }

        $correctAnswers = max(min($correctAnswers, 40), 0);

        foreach ($ranges as [$min, $max, $band]) {
            if ($correctAnswers >= $min && $correctAnswers <= $max) {
                return $band;
            }
        }

        return 0.0;
    }

    private static function nearestHalf(float $score): float
    {
        $whole = floor($score);
        $fraction = $score - $whole;

        if ($fraction < 0.25) {
            return (float) $whole;
        }

        if ($fraction < 0.75) {
            return $whole + 0.5;
        }

        return $whole + 1.0;
    }
}
