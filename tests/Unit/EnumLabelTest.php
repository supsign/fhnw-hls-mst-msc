<?php

namespace Tests\Unit;

use App\Enums\CourseGroupType;
use App\Enums\Error;
use App\Enums\SemesterType;
use App\Enums\StudyMode;
use App\Enums\ThesisStarts;
use PHPUnit\Framework\TestCase;

/**
 * Die Labels erscheinen in der SPA (coursedata/personaldata) und im PDF.
 */
class EnumLabelTest extends TestCase
{
    public function test_module_types_have_label_short_label_and_tooltip(): void
    {
        $this->assertSame([
            'CoreCompetences' => ['Core Competences', 'CC', 'Core Competences Module (online & Muttenz)'],
            'ClusterSpecific' => ['Cluster-Specific', 'CS', 'Cluster-Specific Module (various locations, partly online)'],
            'Specialization' => ['Specialization', 'S', 'Specialization Module (Muttenz)'],
            'Elective' => ['Elective', 'E', 'Elective Module'],
        ], array_combine(
            array_map(fn (CourseGroupType $type): string => $type->name, CourseGroupType::cases()),
            array_map(fn (CourseGroupType $type): array => [$type->label(), $type->labelShort(), $type->tooltip()], CourseGroupType::cases())
        ));
    }

    public function test_study_modes_are_offered_with_their_labels(): void
    {
        $this->assertSame([
            ['id' => 1, 'label' => 'Full-time'],
            ['id' => 2, 'label' => 'Part-time'],
        ], StudyMode::asArray());

        // Formular-Requests liefern Strings
        $this->assertSame(StudyMode::PartTime, StudyMode::getByValue('2'));
        $this->assertNull(StudyMode::getByValue(3));
    }

    public function test_semester_types_have_short_and_long_name_start_date_and_tooltip(): void
    {
        $this->assertSame([
            'AutumnStart' => ['AS', 'Autumn Semester', '10-01', 'Autumn Semester (September – January) '],
            'SpringStart' => ['SS', 'Spring Semester', '03-01', 'Spring Semester (February – June)'],
            'BothStart' => ['BS', 'Either Semester', '', 'Either Semester'],
        ], array_combine(
            array_map(fn (SemesterType $type): string => $type->name, SemesterType::cases()),
            array_map(fn (SemesterType $type): array => [$type->shortName(), $type->longName(), $type->startDate(), $type->tooltip()], SemesterType::cases())
        ));
    }

    public function test_thesis_starts_and_fallback_error_have_labels(): void
    {
        $this->assertSame('Beginning of year', ThesisStarts::Beginning->label());
        $this->assertSame('Mid year', ThesisStarts::Middle->label());
        $this->assertSame('unknown error', Error::Unknown->label());
    }
}
