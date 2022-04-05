<?php

namespace App\Enums;

use App\Helpers\GeneralHelper;

enum CourseGroupType: int
{
    case default = 1;
    case coreCompetences = 2;
    case clusterSpecific = 3;
    case elective = 4;

    public function label(): string
    {
        return match($this) {
            static::coreCompetences, 
            static::elective, 
            static::default 
                => GeneralHelper::splitStringOnUppercase($this->name, ' ', 'ucfirst'),

            static::clusterSpecific 
                => GeneralHelper::splitStringOnUppercase($this->name, '-', 'ucfirst'),
        };
    }

    public static function withoutDefault(): array
    {
        return array_filter(self::cases(), fn($case) => $case->value !== self::default->value);
    }
}