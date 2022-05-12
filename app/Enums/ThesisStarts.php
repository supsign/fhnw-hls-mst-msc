<?php

namespace App\Enums;

enum ThesisStarts: int
{
    case Beginning = 1;
    case Middle = 2;

    public function label(): string
    {
        return match($this) {
            static::Beginning => 'Beginning of Year', 
            static::Middle => 'Mid year'
        };
    }
}