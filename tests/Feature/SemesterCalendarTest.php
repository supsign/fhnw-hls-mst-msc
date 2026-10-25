<?php

namespace Tests\Feature;

use App\Enums\SemesterType;
use App\Models\Cluster;
use App\Models\Semester;
use App\Models\Specialization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Semesterauswahl, Semesterplan und Thesis-Zeitraeume haengen am aktuellen
 * Datum (Carbon::now, subYears/subMonths, Datumsvergleiche). Feste Stichtage
 * machen sichtbar, wenn ein Carbon- oder Framework-Update etwas verschiebt.
 */
class SemesterCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_planner_offers_eight_semesters_starting_two_and_a_half_years_ago(): void
    {
        $this->travelTo(Carbon::parse('2026-06-15 10:00:00'));

        $semesters = $this->getJson('/api/personaldata')->assertOk()->json('semesters');

        // is_replanning: Semester hat schon begonnen; Personal.vue waehlt das erste "false" vor
        $this->assertSame([
            '2024-03-01' => true,
            '2024-10-01' => true,
            '2025-03-01' => true,
            '2025-10-01' => true,
            '2026-03-01' => true,
            '2026-10-01' => false,
            '2027-03-01' => false,
            '2027-10-01' => false,
        ], $this->replanningByStartDate($semesters));

        $this->assertSame([
            'is_replanning' => true,
            'long_name' => '2024 Spring Semester',
            'long_name_with_short' => 'Spring Semester (SS) 2024',
            'name' => '2024 SS',
            'short_name' => 'SS24',
            'start_date' => '2024-03-01T00:00:00.000000Z',
            'tooltip' => 'Spring Semester (February – June)',
            'type' => SemesterType::SpringStart->value,
            'year' => 2024,
        ], $this->withoutId($semesters[0]));
        $this->assertSame([
            'is_replanning' => false,
            'long_name' => '2026 Autumn Semester',
            'long_name_with_short' => 'Autumn Semester (AS) 2026',
            'name' => '2026 AS',
            'short_name' => 'AS26',
            'start_date' => '2026-10-01T00:00:00.000000Z',
            'tooltip' => 'Autumn Semester (September – January) ',
            'type' => SemesterType::AutumnStart->value,
            'year' => 2026,
        ], $this->withoutId($semesters[5]));
    }

    public function test_semester_counts_as_replanning_from_its_first_day(): void
    {
        $expected = [
            '2024-10-01' => true,
            '2025-03-01' => true,
            '2025-10-01' => true,
            '2026-03-01' => true,
            '2026-10-01' => false,
            '2027-03-01' => false,
            '2027-10-01' => false,
            '2028-03-01' => false,
        ];

        $this->travelTo(Carbon::parse('2026-09-30 23:59:59'));
        $this->assertSame($expected, $this->offeredSemesters());

        // exakt um Mitternacht noch nicht: Semester::is_replanning vergleicht start_date < now
        $this->travelTo(Carbon::parse('2026-10-01 00:00:00'));
        $this->assertSame($expected, $this->offeredSemesters());

        $this->travelTo(Carbon::parse('2026-10-01 08:00:00'));
        $this->assertSame(array_replace($expected, ['2026-10-01' => true]), $this->offeredSemesters());
    }

    public function test_year_change_does_not_shift_the_offered_semesters(): void
    {
        $expected = [
            '2024-10-01' => true,
            '2025-03-01' => true,
            '2025-10-01' => true,
            '2026-03-01' => true,
            '2026-10-01' => true,
            '2027-03-01' => false,
            '2027-10-01' => false,
            '2028-03-01' => false,
        ];

        $this->travelTo(Carbon::parse('2026-12-31 23:59:59'));
        $this->assertSame($expected, $this->offeredSemesters());

        $this->travelTo(Carbon::parse('2027-01-01 00:00:00'));
        $this->assertSame($expected, $this->offeredSemesters());
    }

    /**
     * Bekannter Fehler in GetUpcomingSemesters: now()->subYears(2)->subMonths(6)
     * laeuft am 31.08. ueber das Februarende hinaus (Carbon-Default
     * monthOverflow), dadurch faellt an diesem Tag das Fruehjahrssemester von vor
     * 2,5 Jahren aus der Auswahl. Der Test haelt das fest, damit ein Carbon- oder
     * Framework-Update die Auswahl nicht unbemerkt verschiebt; wer den Fehler
     * behebt (z.B. subMonthsNoOverflow), passt die Erwartung fuer den 31.08. an.
     */
    public function test_known_bug_month_overflow_on_august_31_shifts_the_semester_window(): void
    {
        $firstAndLast = [];

        foreach (['2026-08-30 12:00:00', '2026-08-31 12:00:00', '2026-09-01 00:00:00'] as $now) {
            $this->travelTo(Carbon::parse($now));
            $startDates = array_keys($this->offeredSemesters());
            $firstAndLast[$now] = [reset($startDates), end($startDates)];
        }

        $this->assertSame([
            '2026-08-30 12:00:00' => ['2024-03-01', '2027-10-01'],
            '2026-08-31 12:00:00' => ['2024-10-01', '2028-03-01'],
            '2026-09-01 00:00:00' => ['2024-03-01', '2027-10-01'],
        ], $firstAndLast);
    }

    public function test_course_data_plans_two_semesters_full_time_and_four_part_time(): void
    {
        $this->travelTo(Carbon::parse('2026-06-15 10:00:00'));
        $semesters = $this->createSemesters();
        $specialization = $this->createSpecialization();

        $plan = fn (int $studyMode): array => array_map(
            fn (array $semester): string => substr($semester['start_date'], 0, 10),
            $this->postJson("/api/coursedata/{$specialization->id}", [
                'semester' => $semesters['2026-10-01']->id,
                'study_mode' => $studyMode,
            ])->assertOk()->json('semesters')
        );

        $this->assertSame(['2026-10-01', '2027-03-01'], $plan(1));
        $this->assertSame(['2026-10-01', '2027-03-01', '2027-10-01', '2028-03-01'], $plan(2));
    }

    public function test_thesis_time_frames_follow_study_mode_double_degree_and_early_start(): void
    {
        $this->travelTo(Carbon::parse('2026-06-15 10:00:00'));
        $semester = $this->createSemesters()['2026-10-01'];
        $specialization = $this->createSpecialization();

        $timeFrames = fn (array $options): array => array_map(
            fn (array $timeFrame): string => substr($timeFrame['start']['start_date'], 0, 10).' '.$timeFrame['start']['long_name'].' to '.$timeFrame['end'],
            $this->postJson("/api/thesisdata/{$specialization->id}", $options + [
                'double_degree' => false,
                'early_start' => false,
                'semester' => $semester->id,
                'study_mode' => 1,
            ])->assertOk()->json('time_frames')
        );

        // Vollzeit: ab dem Startsemester
        $this->assertSame([
            '2026-10-01 Mid year 2027 to spring 2028',
            '2027-03-01 Beginning of year 2028 to autumn 2028',
            '2027-10-01 Mid year 2028 to spring 2029',
        ], $timeFrames([]));

        // Teilzeit: zwei Semester spaeter
        $this->assertSame([
            '2027-10-01 Mid year 2028 to spring 2029',
            '2028-03-01 Beginning of year 2029 to autumn 2029',
            '2028-10-01 Mid year 2029 to spring 2030',
        ], $timeFrames(['study_mode' => 2]));

        // Double Degree: ein Semester spaeter
        $this->assertSame([
            '2027-03-01 Beginning of year 2028 to autumn 2028',
            '2027-10-01 Mid year 2028 to spring 2029',
            '2028-03-01 Beginning of year 2029 to autumn 2029',
        ], $timeFrames(['double_degree' => true]));

        // frueher Start (keine Module im letzten Semester): ein Semester frueher
        $this->assertSame([
            '2026-03-01 Beginning of year 2027 to autumn 2027',
            '2026-10-01 Mid year 2027 to spring 2028',
            '2027-03-01 Beginning of year 2028 to autumn 2028',
        ], $timeFrames(['early_start' => true]));
    }

    /**
     * @return array<string, bool> Startdatum => is_replanning
     */
    protected function offeredSemesters(): array
    {
        return $this->replanningByStartDate($this->getJson('/api/personaldata')->assertOk()->json('semesters'));
    }

    /**
     * @param list<array<string, mixed>> $semesters
     * @return array<string, bool>
     */
    protected function replanningByStartDate(array $semesters): array
    {
        $result = [];

        foreach ($semesters as $semester) {
            $result[substr($semester['start_date'], 0, 10)] = $semester['is_replanning'];
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $semester
     * @return array<string, mixed>
     */
    protected function withoutId(array $semester): array
    {
        $semester = Arr::except($semester, 'id');
        ksort($semester);

        return $semester;
    }

    /**
     * Alle Folgesemester existieren schon. Fehlen welche, legt
     * GetUpcomingSemesters sie zwar an, liefert beim ersten Aufruf aber die
     * Semester ab heute minus 2,5 Jahre statt ab dem gewaehlten Semester.
     *
     * @return array<string, Semester>
     */
    protected function createSemesters(): array
    {
        $semesters = [];

        foreach (['2026-03-01', '2026-10-01', '2027-03-01', '2027-10-01', '2028-03-01', '2028-10-01', '2029-03-01'] as $date) {
            $semesters[$date] = Semester::create([
                'start_date' => Carbon::parse($date),
                'type' => str_ends_with($date, '-10-01') ? SemesterType::AutumnStart : SemesterType::SpringStart,
            ]);
        }

        return $semesters;
    }

    protected function createSpecialization(): Specialization
    {
        $cluster = Cluster::create(['name' => 'Chemistry and Biotechnology']);

        return Specialization::create([
            'cluster_id' => $cluster->id,
            'name' => 'Applied Cell Biology',
            'short_name' => 'ACB',
        ]);
    }
}
