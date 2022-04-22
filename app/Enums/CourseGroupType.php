<?php

namespace App\Enums;

use App\Helpers\GeneralHelper;

enum CourseGroupType: int
{
    case CoreCompetences = 2;
    case ClusterSpecific = 3;
    case Specialization = 1;
    case Elective = 4;

    public static function furtherCases(): array
    {
        return [static::ClusterSpecific, static::Specialization];
    }

    public function label(): string
    {
        return match($this) {
            static::CoreCompetences, 
            static::Specialization,
            static::Elective => GeneralHelper::splitStringOnUppercase($this->name, ' ', 'ucfirst'),
            static::ClusterSpecific => GeneralHelper::splitStringOnUppercase($this->name, '-', 'ucfirst'),
        };
    }

    public function labelShort(): string
    {
        return match($this) {
            static::CoreCompetences, 
            static::Specialization,
            static::Elective,
            static::ClusterSpecific => GeneralHelper::getInitialsOnUpperCase($this->name),
        };
    }

    public static function withoutSpecialization(): array
    {
        return array_filter(self::cases(), fn($case) => $case->value !== self::Specialization->value);
    }
}