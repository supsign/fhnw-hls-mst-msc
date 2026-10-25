<?php

namespace Tests\Unit;

use App\Enums\SemesterType;
use App\Models\Semester;
use App\Services\GetSemester;
use App\Services\GetUpcomingSemesters;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * GetSemester uebersetzt die Jahresangaben der Konfigurations-Excel (start/end)
 * in Semester; zweistellige Jahre werden mit dem aktuellen Jahr ergaenzt.
 */
class GetSemesterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-06-15 10:00:00'));
    }

    public function test_autumn_semester_starts_in_october_and_spring_semester_in_march(): void
    {
        $autumn = app(GetSemester::class)(2026, true);
        $spring = app(GetSemester::class)(2027, false);

        $this->assertSame(['2026-10-01', SemesterType::AutumnStart], [$autumn->start_date->toDateString(), $autumn->type]);
        $this->assertSame(['2027-03-01', SemesterType::SpringStart], [$spring->start_date->toDateString(), $spring->type]);
    }

    public function test_two_digit_year_is_completed_with_the_current_century(): void
    {
        $this->assertSame('2024-10-01', app(GetSemester::class)(24, true)->start_date->toDateString());
    }

    public function test_existing_semester_is_reused(): void
    {
        $semester = app(GetSemester::class)(2026, true);

        $this->assertTrue($semester->is(app(GetSemester::class)(26, true)));
        $this->assertSame(1, Semester::count());
    }

    /**
     * Import (GetSemester) und Planer (GetUpcomingSemesters) suchen Semester per
     * exaktem start_date. Die eingefrorene Uhrzeit (10:00) darf deshalb nicht im
     * start_date landen, sonst legt der Planer neben jedes importierte Semester
     * ein zweites um Mitternacht.
     */
    public function test_semester_from_the_configuration_is_reused_by_the_planner(): void
    {
        $semester = app(GetSemester::class)(2026, true);

        app(GetUpcomingSemesters::class)(8);

        $this->assertSame([$semester->id], Semester::whereDate('start_date', '2026-10-01')->pluck('id')->all());
    }

    public function test_years_after_2037_are_rejected(): void
    {
        // semesters.start_date ist ein TIMESTAMP
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Year_2038_problem');

        app(GetSemester::class)(2038, true);
    }
}
