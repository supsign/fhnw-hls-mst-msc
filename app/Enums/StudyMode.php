<?php

namespace App\Enums;

use App\Helpers\GeneralHelper;

enum StudyMode: int
{
    case FullTime = 1;
    case PartTime = 2;

    public function label(): string
    {
        return match($this) {
            static::FullTime,
            static::PartTime => ucfirst(GeneralHelper::splitStringOnUppercase($this->name, '-', 'strtolower')),
        };
    }
}