<?php

namespace App\Beatsaber;

class Utils
{
    public static function calculateGridCoordinates($number) {
        if ($number < 1 || $number > 12) {
            return null;
        }

        $x = ($number - 1) % 4;
        $y = floor(($number - 1) / 4);

        return [ $x, $y ];
    }

    /**
     * This rounds any number to 0, 0.25, 0.5 or 0.75 and maintains formatting.
     */
    public static function roundToNearestQuarter($number): float
    {
        $rounded = round($number * 4) / 4;
        return (float)number_format($rounded, 2);
    }
}