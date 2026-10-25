<?php

namespace Tests\Feature;

use App\Enums\CourseGroupType;
use App\Enums\SemesterType;
use App\Exceptions\InvalidData;
use App\Imports\Configuration;
use App\Models\Cluster;
use App\Models\Course;
use App\Models\CourseCourseGroup;
use App\Models\CourseGroup;
use App\Models\CourseGroupSpecialization;
use App\Models\Link;
use App\Models\PageContent;
use App\Models\Specialization;
use App\Models\Thesis;
use App\Services\ConfigurationImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;
use stdClass;
use Tests\Support\ConfigurationWorkbook;
use Tests\Support\InspectsConfigurationTables;
use Tests\Support\PdfDocument;
use Tests\Support\RemovesCreatedFiles;
use Tests\TestCase;

/**
 * Konfigurations-Excel ueber die Sheet-Importer, so wie ConfigurationImport
 * sie aufruft, aber ohne dessen MySQL-Truncate: was aus den Sheets wird und
 * was Planer und PDF daraus machen. Laeuft daher auch auf SQLite und sichert
 * maatwebsite/excel, PhpSpreadsheet und den JSON-Vertrag der SPA im Required
 * Check ab; Admin-Endpoint, Truncate und MySQL-Fehlertexte prueft
 * ConfigurationImportTest.
 */
class ConfigurationSheetsTest extends TestCase
{
    use InspectsConfigurationTables;
    use RefreshDatabase;
    use RemovesCreatedFiles;

    /**
     * Transaktionen, die der letzte Aufruf von importWithService() offen
     * gelassen hat.
     */
    protected int $transactionsLeftOpen = 0;

    protected function setUp(): void
    {
        parent::setUp();

        // GetSemester vervollstaendigt zweistellige Jahre mit dem aktuellen Jahr
        $this->travelTo(Carbon::parse('2026-06-15 10:00:00'));
        $this->removeFilesCreatedDuringTest('local', 'public');
    }

    public function test_all_sheets_are_read_including_their_special_cases(): void
    {
        $this->importSheets(ConfigurationWorkbook::realistic());

        $this->assertSame(ConfigurationWorkbook::REALISTIC_ROW_COUNTS, $this->rowCounts());

        $this->assertSame([
            1 => ['Core Competences', 'yes'],
            2 => ['Chemistry and Biotechnology', null],
            3 => ['Food, Nutrition and Health', null],
        ], Cluster::orderBy('id')->get()->mapWithKeys(fn (Cluster $cluster): array => [
            $cluster->id => [$cluster->name, $cluster->core_competences],
        ])->all());
        $this->assertSame([
            1 => [2, 'Applied Chemistry', 'AC'],
            2 => [2, 'Applied Cell Biology', 'ACB'],
        ], Specialization::orderBy('id')->get()->mapWithKeys(fn (Specialization $specialization): array => [
            $specialization->id => [$specialization->cluster_id, $specialization->name, $specialization->short_name],
        ])->all());

        // Modultyp kommt aus dem Gruppennamen, bei zwei Treffern gewinnt der letzte
        $this->assertSame([
            1 => ['Specialisation Modules Applied Chemistry', 'S-AC', 2, CourseGroupType::Specialization],
            2 => ['Specialisation Modules Applied Cell Biology', 'S-ACB', 2, CourseGroupType::Specialization],
            3 => ['Elective Modules Applied Chemistry', 'E-AC', 1, CourseGroupType::Elective],
            4 => ['Cluster-Specific Modules Chemistry and Biotechnology', 'CS-CB', 1, CourseGroupType::ClusterSpecific],
            5 => ['Core Competences', 'CC', 2, CourseGroupType::CoreCompetences],
            6 => ['Cluster-Specific Elective Pool', 'CS-EP', 1, CourseGroupType::Elective],
        ], CourseGroup::orderBy('id')->get()->mapWithKeys(fn (CourseGroup $group): array => [
            $group->id => [$group->name, $group->internal_name, $group->required_courses_count, $group->type],
        ])->all());
        $this->assertSame([1, 2], $this->specializationIdsOfGroup(4));
        $this->assertSame([2], $this->specializationIdsOfGroup(2));

        $this->assertSame([
            // id => [Typ, Semestertyp, Start, Ende, Slot, Venue, Kurzname, Block, Thesis-Warnung, ECTS, Gruppen]
            101 => [CourseGroupType::Specialization, SemesterType::AutumnStart, '2022-10-01', null, 'Slot 1', 'Muttenz', 'NMR', 0, 0, 3, [1]],
            // AS-B: Blockkurs am Semesterende
            103 => [CourseGroupType::Specialization, SemesterType::AutumnStart, '2022-10-01', null, 'Block 1', 'Muttenz', null, 1, 1, 4, [1]],
            // beide Semester (BS), der erste nicht leere Slot zaehlt. Bekannt und vermutlich falsch: BS startet
            // im Fruehjahr des Startjahrs, SS erst im Folgejahr; wird das korrigiert, auch 303 und 401 anpassen
            104 => [CourseGroupType::Specialization, SemesterType::BothStart, '2022-03-01', null, 'Slot 5', 'Muttenz', null, 0, 0, 3, [1, 6]],
            // Startjahr "2023" als Text, SS beginnt im Folgejahr
            112 => [CourseGroupType::Specialization, SemesterType::SpringStart, '2024-03-01', null, 'Slot B', 'Muttenz', null, 0, 0, 3, [2]],
            // zweistelliges Startjahr 24, Ende 2027
            113 => [CourseGroupType::Specialization, SemesterType::AutumnStart, '2024-10-01', '2027-10-01', 'Slot 3', 'Muttenz', null, 0, 0, 3, [2]],
            201 => [CourseGroupType::ClusterSpecific, SemesterType::AutumnStart, '2022-10-01', null, 'Slot 2', 'Wädenswil/online', null, 0, 0, 3, [4]],
            202 => [CourseGroupType::ClusterSpecific, SemesterType::SpringStart, '2023-03-01', '2026-03-01', 'Slot B', 'Sion', null, 0, 0, 3, [4]],
            301 => [CourseGroupType::CoreCompetences, SemesterType::AutumnStart, '2022-10-01', null, 'Slot 6', 'online/Muttenz', 'DoE', 0, 0, 3, [5]],
            // Venue-Zelle leer
            303 => [CourseGroupType::CoreCompetences, SemesterType::BothStart, '2022-03-01', null, 'Slot 7', null, null, 1, 0, 2, [5]],
            // weder Cluster noch Spezialisierung: optionaler Kurs
            401 => [null, SemesterType::BothStart, '2022-03-01', null, 'Language', 'Muttenz', null, 0, 0, 0, []],
        ], $this->courseSummaries([101, 103, 104, 112, 113, 201, 202, 301, 303, 401]));

        // Texte mit Umlauten, HTML und Sonderzeichen kommen unveraendert an
        $this->assertSame('Lab-on-a-Chip <Microfluidics>', Course::find(113)->name);
        $this->assertSame('Deutsch für Studierende – Übung', Course::find(402)->name);
        $this->assertSame('Deutschkurs: Äöü ß € «Guillemets».', Course::find(402)->content);
        $this->assertSame([
            'trace analysis and environmental chemistry' => 1,
            'green & sustainable synthesis' => 1,
            'Ökobilanzierung & Kreislaufwirtschaft' => 1,
            'cell culture systems & 3D models' => 2,
            'gene- and cell-therapeutics' => 2,
        ], Thesis::orderBy('id')->pluck('specialization_id', 'name')->all());
        $this->assertSame('<p>Compose your study programme for the <b>MSc in Life Sciences</b>.</p>', PageContent::getContentByName('intro_content'));
        $this->assertSame('Choose at least #requiredCoursesCount modules from "#groupName"', PageContent::getContentByName('group_title'));
        $this->assertSame('50', PageContent::getContentByName('required_ects'));
        $this->assertSame('mailto:msls.lifesciences@fhnw.ch', Link::find(2)->url);
    }

    /**
     * Pflichtfelder ohne Wert enden als App\Exceptions\InvalidData (kein
     * Dev-Paket mehr, das bei composer --no-dev fehlt); ConfigurationImport
     * faengt genau diese Klasse und rollt zurueck.
     */
    public function test_link_without_url_raises_invalid_data(): void
    {
        $this->expectException(InvalidData::class);
        $this->expectExceptionMessage('"internal_name" and "link" column for link id "2" can not be empty');

        $this->importSheets(ConfigurationWorkbook::realistic()->withValue('links', 2, 'link', null));
    }

    public function test_text_without_content_raises_invalid_data(): void
    {
        $this->expectException(InvalidData::class);
        $this->expectExceptionMessage('"content" column for key "pdf_text" can not be empty');

        $this->importSheets(ConfigurationWorkbook::realistic()->withValue('Texte', 'pdf_text', 'content', null));
    }

    /**
     * Transaktion, catch und rollBack sind die von ConfigurationImport, nur das
     * Leeren der Tabellen ist ersetzt (siehe importWithService). Das Sheet
     * links kommt zuletzt, alle anderen Tabellen sind dann schon neu befuellt.
     */
    public function test_service_keeps_the_previous_configuration_on_invalid_data(): void
    {
        $this->assertEquals((object)['status' => 'success'], $this->importWithService(ConfigurationWorkbook::realistic()));
        $this->assertSame(0, $this->transactionsLeftOpen);
        $before = $this->tableContents();

        $this->assertEquals((object)[
            'status' => 'error',
            'error' => '"internal_name" and "link" column for link id "2" can not be empty',
        ], $this->importWithService(ConfigurationWorkbook::realistic()->withValue('links', 2, 'link', null)));

        // ohne eigenes rollBack bliebe die Transaktion offen und der halbe Import sichtbar
        $this->assertSame(0, $this->transactionsLeftOpen);
        $this->assertSame($before, $this->tableContents());
    }

    public function test_planner_endpoints_serve_the_imported_configuration(): void
    {
        $this->importSheets(ConfigurationWorkbook::realistic());

        $personalData = $this->getJson('/api/personaldata')
            ->assertOk()
            // cluster_id braucht die SPA (specialization.interface.ts); im JSON steht es nur,
            // weil Specialization::$hidden 'cluter_id' (Tippfehler) statt 'cluster_id' enthaelt
            ->assertJsonPath('specializations', [
                ['id' => 1, 'cluster_id' => 2, 'name' => 'Applied Chemistry', 'short_name' => 'AC'],
                ['id' => 2, 'cluster_id' => 2, 'name' => 'Applied Cell Biology', 'short_name' => 'ACB'],
            ])
            ->assertJsonPath('studyMode.tooltip', 'Full-time: 3 semesters incl. thesis – part-time: 5–6 semesters.');

        // Reihenfolge der Texte ist nicht festgelegt (kein orderBy), die SPA sucht per name
        $this->assertEquals([
            'hints_list' => '<ul><li>You need at least 50 ECTS.</li><li>Achten Sie auf Überschneidungen.</li></ul>',
            'hints_title' => 'Hints',
            'intro_content' => '<p>Compose your study programme for the <b>MSc in Life Sciences</b>.</p>',
            'intro_title' => 'Plan your MSc in Life Sciences',
        ], collect($personalData->json('texts'))->pluck('content', 'name')->all());

        // Personal.vue waehlt das erste Semester, das nicht is_replanning ist
        $semester = collect($personalData->json('semesters'))->firstWhere('is_replanning', false);
        $this->assertSame('2026-10-01T00:00:00.000000Z', $semester['start_date']);

        $courseData = $this->postJson('/api/coursedata/1', ['semester' => $semester['id'], 'study_mode' => 1])->assertOk();

        $this->assertSame(
            ['2026-10-01T00:00:00.000000Z', '2027-03-01T00:00:00.000000Z'],
            array_column($courseData->json('semesters'), 'start_date')
        );
        $this->assertSame([
            ['Choose at least 2 modules from "Specialisation Modules Applied Chemistry"', [101, 102, 103, 104]],
            ['Choose at least 1 modules from "Elective Modules Applied Chemistry"', [111]],
            // 202 lief bis SS 2026 und ist ausgeblendet
            ['Choose at least 1 modules from "Cluster-Specific Modules Chemistry and Biotechnology"', [201]],
            ['Choose at least 2 modules from "Core Competences"', [301, 302, 303]],
        ], array_map(
            fn (array $group): array => [$group['title'], array_column($group['courses'], 'id')],
            $courseData->json('courses.0')
        ));
        $courseData
            ->assertJsonPath('courses.0.3.description', 'Core competence modules are mandatory for all specialisations.')
            ->assertJsonPath('courses.1.0.title', 'Modules of other specialisations')
            ->assertJsonPath('courses.1.0.specializations.0.id', 2)
            ->assertJsonPath('courses.1.1.title', 'Modules of other clusters')
            ->assertJsonPath('courses.1.1.clusters.0.id', 3);
        $this->assertSame([112, 113], array_column($courseData->json('courses.1.0.specializations.0.courses'), 'id'));
        $this->assertSame([211], array_column($courseData->json('courses.1.1.clusters.0.courses'), 'id'));
        $this->assertSame([401, 402], array_column($courseData->json('optional_courses.courses'), 'id'));

        // Home.vue vergleicht strikt (thesis_warning === 1), Typen und Labels kommen aus dem Backend
        $courseData
            ->assertJsonPath('courses.0.0.courses.2.id', 103)
            ->assertJsonPath('courses.0.0.courses.2.thesis_warning', 1)
            ->assertJsonPath('courses.0.0.courses.2.block', 1)
            ->assertJsonPath('courses.0.0.courses.2.type', 1)
            ->assertJsonPath('courses.0.0.courses.2.type_label_short', 'S')
            ->assertJsonPath('courses.0.0.courses.2.type_tooltip', 'Specialization Module (Muttenz)')
            ->assertJsonPath('courses.0.0.courses.2.semester_type', 1)
            ->assertJsonPath('courses.0.0.courses.2.start_semester.start_date', '2022-10-01T00:00:00.000000Z');

        $thesisData = $this->postJson('/api/thesisdata/1', [
            'double_degree' => false,
            'early_start' => false,
            'semester' => $semester['id'],
            'study_mode' => 1,
        ])
            ->assertOk()
            ->assertJsonPath('time_frames.0.start.long_name', 'Mid year 2027')
            ->assertJsonPath('time_frames.0.end', 'spring 2028')
            ->assertJsonPath('texts.0.content', 'Choose at least two subject areas for your MSc thesis.');

        // Thesis-IDs vergibt der Import per Auto-Increment, sie aendern sich bei jedem Import
        $this->assertSame(
            ['trace analysis and environmental chemistry', 'green & sustainable synthesis', 'Ökobilanzierung & Kreislaufwirtschaft'],
            array_column($thesisData->json('theses'), 'name')
        );
    }

    public function test_study_plan_pdf_can_be_created_from_the_imported_configuration(): void
    {
        $this->importSheets(ConfigurationWorkbook::realistic());

        // Ablauf wie in der SPA: personaldata -> coursedata -> thesisdata -> pdf
        $semesters = collect($this->getJson('/api/personaldata')->json('semesters'));
        $autumn = $semesters->firstWhere('start_date', '2026-10-01T00:00:00.000000Z')['id'];
        $spring = $semesters->firstWhere('start_date', '2027-03-01T00:00:00.000000Z')['id'];
        $this->postJson('/api/coursedata/1', ['semester' => $autumn, 'study_mode' => 1])->assertOk();
        $thesisData = $this->postJson('/api/thesisdata/1', [
            'double_degree' => false,
            'early_start' => false,
            'semester' => $autumn,
            'study_mode' => 1,
        ])->assertOk();
        $thesisIds = collect($thesisData->json('theses'))->pluck('id', 'name');

        $path = $this->postJson('/api/pdf', [
            'additional_comments' => '',
            'double_degree' => false,
            'given_name' => 'Peter',
            'master_thesis' => [
                'further_details' => '',
                'theses' => [
                    $thesisIds['Ökobilanzierung & Kreislaufwirtschaft'],
                    $thesisIds['trace analysis and environmental chemistry'],
                ],
                'time_frames' => $thesisData->json('time_frames.0'),
            ],
            'modules_outside' => [
                ['title' => 'Biostatistik II', 'ects' => 6, 'university' => 'Universität Basel'],
                ['title' => 'Clinical Trials & GCP', 'ects' => 6, 'university' => 'ETH Zürich'],
                ['title' => 'Research Data Management', 'ects' => 4, 'university' => 'ZHAW Wädenswil'],
            ],
            'overlapping_courses' => [['courses' => [[111, 201], [113, 211]], 'semesterId' => $autumn]],
            'selected_courses' => [
                ['courses' => [101, 103, 111, 113, 201, 211, 301, 402], 'semesterId' => $autumn],
                ['courses' => [102, 104, 112, 302], 'semesterId' => $spring],
            ],
            'semester' => $autumn,
            'specialization' => 1,
            'statistics' => [
                'cluster' => 2,
                'core' => 2,
                'ects' => 50,
                'moduleGroupCount' => [],
                'outside' => 3,
                'specialization' => 7,
            ],
            'study_mode' => 1,
            'surname' => 'Meier',
        ])->assertOk()->getContent();

        $this->assertSame('storage/meier_peter_as26_2026-06-15.pdf', $path);
        $pdf = PdfDocument::fromFile(Storage::disk('public')->path('meier_peter_as26_2026-06-15.pdf'));
        $lines = $pdf->lines();

        $this->assertCount(1, $pdf->images(1));
        $this->assertContains('Specialization: Applied Chemistry', $lines);
        $this->assertContains('Reaction Technology S 4 Muttenz', $lines);
        $this->assertContains('Lab-on-a-Chip <Microfluidics> S 3 Muttenz', $lines);
        $this->assertContains('Biocatalysis and Enzyme Engineering CS 3 Wädenswil/online', $lines);
        $this->assertContains('Progresses in Food Processing CS 3 Olten', $lines);
        $this->assertContains('Design and Analysis of Experiments CC 3 online/Muttenz', $lines);
        $this->assertContains('Deutsch für Studierende – Übung 0 Muttenz', $lines);
        $this->assertContains('Mid year 2027 to spring 2028', $lines);
        $this->assertContains('1st preference: Ökobilanzierung & Kreislaufwirtschaft', $lines);
        $this->assertContains('2nd preference: trace analysis and environmental chemistry', $lines);
        $this->assertStringContainsString(
            'Please note that the module offer may change. Änderungen vorbehalten – Stand: HS 2026.',
            implode(' ', $lines)
        );
    }

    /**
     * Wie ConfigurationImport: Datei auf der Default-Disk, Excel::import().
     */
    protected function importSheets(ConfigurationWorkbook $workbook): void
    {
        app(Excel::class)->import(app(Configuration::class), $this->storeOnDefaultDisk($workbook));
    }

    /**
     * Der echte ConfigurationImport; nur leert er die Tabellen in FK-Reihenfolge
     * statt mit SET FOREIGN_KEY_CHECKS (MySQL-SQL, auf SQLite ein Syntaxfehler).
     * Was er an Transaktionen offen laesst, landet in $transactionsLeftOpen und
     * wird danach zurueckgerollt.
     */
    protected function importWithService(ConfigurationWorkbook $workbook): stdClass
    {
        $service = new class(app(Configuration::class), app(Excel::class)) extends ConfigurationImport {
            protected function truncateTables(): self
            {
                foreach ([CourseCourseGroup::class, CourseGroupSpecialization::class, Course::class, CourseGroup::class, Thesis::class, Specialization::class, Cluster::class, Link::class, PageContent::class] as $table) {
                    $table::getQuery()->delete();
                }

                return $this;
            }
        };

        $level = DB::transactionLevel();

        try {
            return $service($this->storeOnDefaultDisk($workbook));
        } finally {
            // Bei anderen Exceptions als InvalidData rollt der Service nicht zurueck
            // (bekannter Fehler). Seit Laravel 11 teilen sich die Tests die SQLite-
            // In-Memory-Verbindung, eine offene Transaktion risse alle Folgetests mit.
            $this->transactionsLeftOpen = DB::transactionLevel() - $level;
            DB::rollBack($level);
        }
    }

    protected function storeOnDefaultDisk(ConfigurationWorkbook $workbook): string
    {
        Storage::disk('local')->makeDirectory('config');
        $workbook->save(Storage::disk('local')->path('config/phpunit-konfiguration.xlsx'));

        return 'config/phpunit-konfiguration.xlsx';
    }

    /**
     * @return list<int>
     */
    protected function specializationIdsOfGroup(int $courseGroupId): array
    {
        return DB::table('course_group_specialization')
            ->where('course_group_id', $courseGroupId)
            ->orderBy('specialization_id')
            ->pluck('specialization_id')
            ->all();
    }

    /**
     * Block und Thesis-Warnung bleiben bewusst ungecastet: das JSON fuer die SPA
     * enthaelt sie als 0/1, Home.vue prueft thesis_warning === 1.
     *
     * @param list<int> $ids
     * @return array<int, list<mixed>>
     */
    protected function courseSummaries(array $ids): array
    {
        return Course::with(['slot', 'venue', 'startSemester', 'endSemester'])
            ->findMany($ids)
            ->sortBy('id')
            ->mapWithKeys(fn (Course $course): array => [$course->id => [
                $course->type,
                $course->semester_type,
                $course->startSemester->start_date->toDateString(),
                $course->endSemester?->start_date->toDateString(),
                $course->slot->name,
                $course->venue?->name,
                $course->short_name,
                $course->block,
                $course->thesis_warning,
                $course->ects,
                DB::table('course_course_group')
                    ->where('course_id', $course->id)
                    ->orderBy('course_group_id')
                    ->pluck('course_group_id')
                    ->all(),
            ]])
            ->all();
    }
}
