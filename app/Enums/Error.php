<?php

namespace App\Enums;

enum Error: string
{
    case Unknown = 1;

    public function label(): string
    {
        return match($this) {
            static::Unknown => 'unknown error',
        };
    }
}