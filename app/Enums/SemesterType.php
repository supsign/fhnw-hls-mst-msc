<?php

namespace App\Enums;

use Carbon\Carbon;

enum SemesterType: int
{
    case AutumnStart = 1;
    case SpringStart = 2;
    case BothStart = 3;

    public function month(): string
    {
        return match($this) {
            static::AutumnStart => Carbon::parse('1987-'.static::AutumnStart->startDate())->monthName,
            static::SpringStart => Carbon::parse('1992-'.static::SpringStart->startDate())->monthName,
            static::BothStart => '',
        };
    }

    public function longName(): string
    {
        return match($this) {
            static::AutumnStart => 'Autumn Semester',
            static::SpringStart => 'Spring Semester',
            static::BothStart => 'Either Semester',
        };
    }

    public function shortName(): string
    {
        return match($this) {
            static::AutumnStart => 'AS',
            static::SpringStart => 'SS',
            static::BothStart => 'BS',
        };
    }

    public function startDate(): string
    {
        return match($this) {
            static::AutumnStart => '10-01',
            static::SpringStart => '03-01',
            static::BothStart => '',
        };
    }

    public function tooltip(): string
    {
        return match($this) {
            static::AutumnStart => static::AutumnStart->longName().' (September – January) ',
            static::SpringStart => static::SpringStart->longName().' (February – June)',
            static::BothStart => static::BothStart->longName(),
        };
    }
}