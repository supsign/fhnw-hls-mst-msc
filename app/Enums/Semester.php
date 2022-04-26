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
            static::AutumnStart => Carbon::parse('1987-'.static::AutumnStart->value)->monthName,
            static::SpringStart => Carbon::parse('1992-'.static::SpringStart->value)->monthName,
        };
    }

    public function longName(): string
    {
        return match($this) {
            static::AutumnStart => 'autumn semester',
            static::SpringStart => 'spring semester',
        };
    }

    public function shortName(): string
    {
        return match($this) {
            static::AutumnStart => 'AS',
            static::SpringStart => 'SS',
        };
    }

    public function tooltip(): string
    {
        return match($this) {
            static::AutumnStart => static::AutumnStart->longName().' (September – January) ',
            static::SpringStart => static::SpringStart->longName().' (February – June)',
        };
    }
}