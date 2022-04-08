<?php

namespace App\Enums;

use Carbon\Carbon;

enum Semester: string
{
    case AutumnStart = '09-01';
    case SpringStart = '02-01';

    public function month(): string
    {
        return match($this) {
            static::AutumnStart => Carbon::parse('2022-'.static::AutumnStart->value)->monthName,
            static::SpringStart => Carbon::parse('2022-'.static::SpringStart->value)->monthName,
        };
    }
}