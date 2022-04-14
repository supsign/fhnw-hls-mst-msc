<?php

namespace App\Enums\Traits;

trait AsArray
{
    public static function asArray(): array
    {
        $result = [];

        foreach (self::cases() AS $case) {
            $result[$case->value] = method_exists(self::class, 'label') ? $case->label() : $case->name;
        }

        return $result;
    }
}