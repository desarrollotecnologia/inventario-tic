<?php

namespace App\Support;

class MacAddress
{
    public static function format(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        if ($trimmed === '') {
            return '';
        }

        $hex = strtoupper((string) preg_replace('/[^0-9A-Fa-f]/', '', $trimmed));

        if (strlen($hex) === 12 || strlen($hex) === 16) {
            return implode(':', str_split($hex, 2));
        }

        return strtoupper(str_replace(['-', '.', ' '], ':', $trimmed));
    }
}
