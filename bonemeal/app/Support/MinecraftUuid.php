<?php

namespace App\Support;

final class MinecraftUuid
{
    /** Normalise a UUID with or without dashes to lowercase dashed form, or null if blank/invalid. */
    public static function normalize(?string $value): ?string
    {
        $hex = strtolower(str_replace('-', '', trim((string) $value)));

        if (! preg_match('/^[0-9a-f]{32}$/', $hex)) {
            return null;
        }

        return substr($hex, 0, 8).'-'.substr($hex, 8, 4).'-'.substr($hex, 12, 4).'-'.substr($hex, 16, 4).'-'.substr($hex, 20);
    }
}
