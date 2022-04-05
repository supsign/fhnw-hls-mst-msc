<?php

namespace App\Enums;

enum Error: int
{
    case Unknown = 1;

    public function label(): string
    {
        return match($this) {
            static::Unknown => 'unknown error',
        };
    }
}