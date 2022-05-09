<?php

namespace App\Enums\Traits;

trait GetByValue
{
    public static function getByValue(int|string $value): ?self
    {
        return array_values(array_filter(self::cases(), fn($case) => $case->value == $value))[0] ?? null;
    }
}