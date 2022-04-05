<?php

namespace App\Enums;

use App\Helpers\GeneralHelper;

enum CourseGroupType: int
{
    case CoreCompetences = 2;
    case ClusterSpecific = 3;
    case Default = 1;
    case Elective = 4;

    public function label(): string
    {
        return match($this) {
            static::CoreCompetences, 
            static::Default,
            static::Elective => GeneralHelper::splitStringOnUppercase($this->name, ' ', 'ucfirst'),
            static::ClusterSpecific => GeneralHelper::splitStringOnUppercase($this->name, '-', 'ucfirst'),
        };
    }

    public static function withoutDefault(): array
    {
        return array_filter(self::cases(), fn($case) => $case->value !== self::Default->value);
    }
}