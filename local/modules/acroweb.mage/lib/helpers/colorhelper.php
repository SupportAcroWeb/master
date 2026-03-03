<?php

namespace Acroweb\Mage\Helpers;

class ColorHelper
{
    /**
     * Lighten a hex color
     *
     * @param string $hex The hex color code
     * @param float $percent The percentage to lighten (0-100)
     * @return string The lightened hex color
     */
    public static function lighten(string $hex, float $percent = 15): string
    {
        // Remove # if present
        $hex = ltrim($hex, '#');

        // Convert hex to rgb
        $rgb = array_map('hexdec', str_split($hex, 2));

        // Calculate new rgb values
        foreach ($rgb as &$color) {
            $color = min(255, $color + $color * ($percent / 100));
        }

        // Convert back to hex
        return '#' . implode('', array_map(function($c) {
            return str_pad(dechex(round($c)), 2, '0', STR_PAD_LEFT);
        }, $rgb));
    }

    /**
     * Darken a hex color
     *
     * @param string $hex The hex color code
     * @param float $percent The percentage to darken (0-100)
     * @return string The darkened hex color
     */
    public static function darken(string $hex, float $percent = 15): string
    {
        return self::lighten($hex, -$percent);
    }
}