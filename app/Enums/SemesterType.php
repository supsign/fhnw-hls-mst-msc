<?php

namespace App\Enums;

use Carbon\Carbon;

enum SemesterType: int
{
    case AutumnStart = 1;
    case SpringStart = 2;

    public function month(): string
    {
        return match($this) {
            static::AutumnStart => Carbon::parse('1987-'.static::AutumnStart->startDate())->monthName,
            static::SpringStart => Carbon::parse('1992-'.static::SpringStart->startDate())->monthName,
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

    public function startDate(): string
    {
        return match($this) {
            static::AutumnStart => '09-01',
            static::SpringStart => '02-01',
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