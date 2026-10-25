<?php

namespace Tests\Feature;

use App\Enums\SemesterType;
use App\Models\Cluster;
use App\Models\Course;
use App\Models\PageContent;
use App\Models\Semester;
use App\Models\Specialization;
use App\Models\Thesis;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\Support\PdfDocument;
use Tests\Support\RemovesCreatedFiles;
use Tests\TestCase;

/**
 * POST /api/pdf mit Payloads in der Form, die pdfData.service.ts aus Home.vue
 * schickt. Die PDF wird ueber Storage::path('public') auf die public-Disk
 * geschrieben (ausgeliefert per storage:link) und hier wieder ausgelesen.
 */
class PdfExportTest extends TestCase
{
    use RefreshDatabase;
    use RemovesCreatedFiles;

    /** @var array<string, Semester> Startdatum => Semester */
    protected array $semesters = [];

    /** @var array<string, Thesis> Thema => Thesis */
    protected array $theses = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Dateiname und Semesterlogik haengen am aktuellen Datum
        $this->travelTo(Carbon::parse('2026-06-15 10:00:00'));
        $this->removeFilesCreatedDuringTest('public');
        $this->seedStudyProgramme();
    }

    public function test_full_time_plan_is_stored_on_the_public_disk_as_a4_pdf(): void
    {
        $response = $this->postJson('/api/pdf', $this->fullTimePlan());

        // Home.vue oeffnet den Pfad relativ zur SPA: window.open('storage/...')
        $response->assertOk();
        $this->assertSame('storage/muster_anna_as26_2026-06-15.pdf', $response->getContent());
        Storage::disk('public')->assertExists('muster_anna_as26_2026-06-15.pdf');

        $pdf = PdfDocument::fromFile(Storage::disk('public')->path('muster_anna_as26_2026-06-15.pdf'));

        $this->assertSame(2, $pdf->pageCount());

        foreach (range(1, $pdf->pageCount()) as $page) {
            $this->assertSame([595.28, 841.89], $pdf->pageSize($page), "Seite {$page} ist nicht A4 hochkant");
        }
    }

    /**
     * Bekannte Upgrade-Regression: laravel-dompdf 3 laedt keine Remote-URLs
     * mehr (enable_remote=false). Wird das Logo wieder per asset()/URL
     * eingebunden, fehlt es ohne Fehlermeldung (dompdf zeichnet den alt-Text).
     */
    public function test_logo_is_embedded_on_the_first_page(): void
    {
        $pdf = $this->exportPdf($this->fullTimePlan());
        [$width, $height] = getimagesize(public_path('img/logos/fhnw-logo-klein.png'));

        $this->assertSame([['width' => $width, 'height' => $height]], $pdf->images(1));
        $this->assertSame([], $pdf->images(2));
    }

    public function test_plan_lists_each_semester_with_module_type_ects_and_venue(): void
    {
        $lines = $this->exportPdf($this->fullTimePlan())->lines();

        $this->assertSame([
            // "FHWN" ist ein bekannter Tippfehler im Template; wird er korrigiert, hier anpassen
            'Your Study Plan MSc in Life Sciences FHWN',
            'Anna Muster',
            'Specialization: Applied Cell Biology',
            'Study Programme',
        ], array_slice($lines, 0, 4));

        $this->assertSame([
            '2026 Autumn Semester' => [
                'Genomics S 3 Muttenz',
                'Cellular imaging S 3 Muttenz',
                'Advanced Cell Culture Systems S 4 Muttenz',
                'Biocatalysis and Enzyme Engineering CS 3 Wädenswil/online',
                'Polymers and Applications CS 3 Fribourg',
                'Design and Analysis of Experiments CC 3 online/Muttenz',
                'Data and Ethics CC 3 online/Muttenz',
                'Handling and Visualizing Data CC 3 online/Muttenz',
            ],
            '2027 Spring Semester' => [
                'Gene- and Cell-Therapeutics S 3 Muttenz',
                'Bioassays: engineered Cells, Tissues and Organisms S 3 Muttenz',
                'Molecular & Translational Imaging S 4 Muttenz',
                'Bioprocess Engineering CS 3 Olten/online',
                'Natural Products Chemistry CS 4 Sion',
                'Management and Leadership for Life Sciences CC 3 online/Muttenz',
                'Innovation and Project Management CC 3 online/Muttenz',
                'Business Administration for Life Sciences CC 3 online/Muttenz',
                // optionaler Kurs ohne Modultyp
                'Deutsch für Studierende – Übung 0 Muttenz',
            ],
        ], $this->semesterTables($lines, 'Mid year 2027 to spring 2028'));
    }

    public function test_thesis_preferences_and_time_frame_are_printed_in_the_chosen_order(): void
    {
        $plan = $this->fullTimePlan();
        $plan['master_thesis']['theses'] = [
            $this->theses['gene- and cell-therapeutics']->id,
            $this->theses['cellular imaging']->id,
            $this->theses['cell culture systems & 3D models']->id,
        ];
        $plan['master_thesis']['further_details'] = 'Interesse an einer Kooperation mit dem Universitätsspital Basel.';

        $this->assertLinesInOrder([
            'Mid year 2027 to spring 2028',
            'Projected time frame of the MSc Thesis in subject area(s):',
            '1st preference: gene- and cell-therapeutics',
            '2nd preference: cellular imaging',
            '3rd preference: cell culture systems & 3D models',
            'Further Details on Thesis (Optional)',
            'Interesse an einer Kooperation mit dem Universitätsspital Basel.',
        ], $this->exportPdf($plan)->lines());
    }

    /**
     * Das Frontend schickt leere Felder als "" bzw. []; ConvertEmptyStringsToNull
     * macht daraus null, und das Template laesst die Bloecke weg.
     */
    public function test_optional_sections_are_left_out_when_the_student_skipped_them(): void
    {
        $lines = $this->exportPdf($this->fullTimePlan())->lines();

        $this->assertNotContains('Further Details on Thesis (Optional)', $lines);
        $this->assertNotContains('Modules outside the Curriculum', $lines);
        $this->assertNotContains('Additional Comments', $lines);
        $this->assertNotContains('Possible Double-Degree Semester at Partner University', $lines);
    }

    public function test_double_degree_semester_follows_the_last_planned_semester(): void
    {
        $plan = $this->fullTimePlan();
        $plan['double_degree'] = true;
        $plan['master_thesis']['time_frames'] = $this->thesisTimeFrame('2027-03-01', 'Beginning of year 2028', 'autumn 2028');

        $this->assertLinesInOrder([
            'Deutsch für Studierende – Übung 0 Muttenz',
            '2027 Autumn Semester',
            'Possible Double-Degree Semester at Partner University',
            'Beginning of year 2028 to autumn 2028',
        ], $this->exportPdf($plan)->lines());
    }

    public function test_modules_outside_comments_overlaps_and_statistics_are_printed(): void
    {
        $plan = $this->fullTimePlan();
        $plan['modules_outside'] = [
            ['title' => 'Biostatistik II', 'ects' => 3, 'university' => 'Universität Basel'],
            ['title' => 'Clinical Trials & GCP', 'ects' => 2, 'university' => 'ETH Zürich'],
        ];
        $plan['additional_comments'] = 'Ich arbeite 50 % – bitte Blockkurse im Frühling berücksichtigen.';
        $plan['statistics']['outside'] = 2;
        $plan['statistics']['ects'] = 56;

        $lines = $this->exportPdf($plan)->lines();

        $this->assertLinesInOrder([
            'Modules outside the Curriculum',
            'Module Title ECTS University',
            'Biostatistik II 3 Universität Basel',
            'Clinical Trials & GCP 2 ETH Zürich',
            'Additional Comments',
            'Ich arbeite 50 % – bitte Blockkurse im Frühling berücksichtigen.',
            'Overlapping Courses',
            '2026 Autumn Semester',
            'Genomics',
            'Biocatalysis and Enzyme Engineering',
            'Design and Analysis of Experiments',
            'Data and Ethics',
            '2027 Spring Semester',
            'Gene- and Cell-Therapeutics',
            'Bioprocess Engineering',
            'Summary Statistics',
            'Number of Specialization Modules: 6',
            'Number of Cluster-specific Modules: 4',
            'Number Core Competence Modules: 6',
            'Number Modules outside the Curriculum: 2',
            'Total number of ECTS: 56 (50 required)',
        ], $lines);

        // pdf_text ist Admin-HTML aus der Konfiguration und wird gerendert, nicht escaped
        $flat = implode(' ', $lines);
        $this->assertStringContainsString('Please note that the module offer and the timing of the modules may change in the future. Änderungen vorbehalten – Stand: HS 2026.', $flat);
        $this->assertStringNotContainsString('<b>', $flat);
        $this->assertSame('Agreed, Date Signature Student Signature Director of Study Programme', end($lines));
    }

    public function test_user_input_is_printed_literally_not_as_html(): void
    {
        $plan = $this->fullTimePlan();
        $plan['given_name'] = 'Zoë-Chloé';
        $plan['surname'] = "O'Brien & Müller";
        $plan['additional_comments'] = '<script>alert("x")</script> & <b>fett</b> – „Anführungszeichen“';
        $plan['master_thesis']['further_details'] = 'Thema: "Mikroschadstoffe" <i>in</i> Kläranlagen & Gewässern';
        $plan['modules_outside'] = [['title' => 'Module <b>X</b> & "Y"', 'ects' => 2, 'university' => 'Université de Genève (UNIGE)']];

        $lines = $this->exportPdf($plan)->lines();

        $this->assertContains("Zoë-Chloé O'Brien & Müller", $lines);
        $this->assertContains('<script>alert("x")</script> & <b>fett</b> – „Anführungszeichen“', $lines);
        $this->assertContains('Thema: "Mikroschadstoffe" <i>in</i> Kläranlagen & Gewässern', $lines);
        $this->assertContains('Module <b>X</b> & "Y" 2 Université de Genève (UNIGE)', $lines);
    }

    /**
     * pdf_text ist Admin-HTML und wird ungefiltert gerendert. Mit enable_php
     * fuehrte dompdf darin <script type="text/php"> aus, also Code fuer jeden,
     * der eine Konfiguration hochladen kann.
     */
    public function test_php_in_the_admin_pdf_text_is_not_executed(): void
    {
        PageContent::where('name', 'pdf_text')->update([
            'content' => '<p>Hinweis</p><script type="text/php">$GLOBALS["pdf_text_php_executed"] = true;</script>',
        ]);

        $lines = $this->exportPdf($this->fullTimePlan())->lines();

        $this->assertArrayNotHasKey('pdf_text_php_executed', $GLOBALS);
        $this->assertContains('Hinweis', $lines);
    }

    public function test_long_part_time_plan_continues_over_several_pages(): void
    {
        $semester = fn (string $date): int => $this->semesters[$date]->id;
        $selected = [
            '2026-10-01' => [141, 142, 201, 223, 224, 301, 302, 303],
            '2027-03-01' => [143, 144, 202, 221, 222, 304, 305],
            '2027-10-01' => [111, 145, 146, 203, 306, 307],
            '2028-03-01' => [112, 113, 204, 401],
            'later' => [114, 402],
        ];
        $plan = [
            'additional_comments' => str_repeat('Teilzeitstudium neben einer Anstellung von 60 %, Präsenztage Montag und Donnerstag. ', 6),
            'double_degree' => false,
            'given_name' => 'Jonas',
            'master_thesis' => [
                'further_details' => str_repeat('Masterarbeit idealerweise mit einem Industriepartner aus der Region Basel. ', 4),
                'theses' => [
                    $this->theses['additive manufacturing']->id,
                    $this->theses['biomedical sensors and signal processing']->id,
                    $this->theses['active implants']->id,
                ],
                'time_frames' => $this->thesisTimeFrame('2027-10-01', 'Mid year 2028', 'spring 2029'),
            ],
            'modules_outside' => [
                ['title' => 'Deep Learning for Computer Vision', 'ects' => 4, 'university' => 'EPFL'],
                ['title' => 'Statistical Genetics', 'ects' => 3, 'university' => 'Universität Bern'],
                ['title' => 'Research Data Management', 'ects' => 2, 'university' => 'ZHAW Wädenswil'],
            ],
            'overlapping_courses' => [
                ['courses' => [[143, 221, 222]], 'semesterId' => $semester('2027-03-01')],
            ],
            'selected_courses' => array_map(
                fn (string $date, array $courses): array => [
                    'courses' => $courses,
                    'semesterId' => $date === 'later' ? 'later' : $semester($date),
                ],
                array_keys($selected),
                $selected
            ),
            'semester' => $semester('2026-10-01'),
            'specialization' => 5,
            'statistics' => [
                'cluster' => 8,
                'core' => 7,
                'ects' => 87,
                'moduleGroupCount' => [],
                'outside' => 3,
                'specialization' => 10,
            ],
            'study_mode' => 2,
            'surname' => 'Brändli',
        ];

        $pdf = $this->exportPdf($plan);

        $this->assertGreaterThan(1, $pdf->pageCount());

        foreach (range(1, $pdf->pageCount()) as $page) {
            $this->assertSame([595.28, 841.89], $pdf->pageSize($page), "Seite {$page} ist nicht A4 hochkant");
            $this->assertCount($page === 1 ? 1 : 0, $pdf->images($page), "Logo nur im Kopf der ersten Seite (Seite {$page})");
        }

        // kein Modul geht am Seitenumbruch verloren, "later" kommt als eigener Block
        $this->assertSame([
            '2026 Autumn Semester' => [
                'Sensors and Signal Processing S 3 Muttenz',
                'Biostructures and Solid State Sciences S 3 Muttenz',
                'Biocatalysis and Enzyme Engineering CS 3 Wädenswil/online',
                'Medical Technology Regulations CS 3 Bern/online',
                'Biomaterials CS 3 Fribourg',
                'Design and Analysis of Experiments CC 3 online/Muttenz',
                'Data and Ethics CC 3 online/Muttenz',
                'Handling and Visualizing Data CC 3 online/Muttenz',
            ],
            '2027 Spring Semester' => [
                'Implant Design and Manufacturing S 3 Muttenz',
                'Medical Device Development S 3 Muttenz',
                'Polymers and Applications CS 3 Fribourg',
                'Physiology and Immunotherapies CS 3 Bern/online',
                'Tissue Engineering for Drug Discovery CS 3 Olten/Bern/online',
                'Management and Leadership for Life Sciences CC 3 online/Muttenz',
                'Innovation and Project Management CC 3 online/Muttenz',
            ],
            '2027 Autumn Semester' => [
                'Genomics S 3 Muttenz',
                'Surgical Robotics S 3 Muttenz',
                'Process Transfer and Scale-up S 4 Muttenz',
                'Bioprocess Engineering CS 3 Olten/online',
                'Business Administration for Life Sciences CC 3 online/Muttenz',
                'Politics and Society CC 3 online/Muttenz',
            ],
            '2028 Spring Semester' => [
                'Cellular imaging S 3 Muttenz',
                'Advanced Cell Culture Systems S 4 Muttenz',
                'Natural Products Chemistry CS 4 Sion',
                'English for MSc Students 0 Muttenz',
            ],
            'Later' => [
                'Gene- and Cell-Therapeutics S 3 Muttenz',
                'Deutsch für Studierende – Übung 0 Muttenz',
            ],
        ], $this->semesterTables($pdf->lines(), 'Mid year 2028 to spring 2029'));

        $this->assertSame(
            'Agreed, Date Signature Student Signature Director of Study Programme',
            last($pdf->lines($pdf->pageCount()))
        );
    }

    protected function seedStudyProgramme(): void
    {
        Cluster::create(['id' => 1, 'name' => 'Core Competences', 'core_competences' => 'yes']);
        Cluster::create(['id' => 2, 'name' => 'Chemistry and Biotechnology']);
        Cluster::create(['id' => 4, 'name' => 'Medical and Pharmaceutical Technologies']);
        Specialization::create(['id' => 2, 'cluster_id' => 2, 'name' => 'Applied Cell Biology', 'short_name' => 'ACB']);
        Specialization::create(['id' => 5, 'cluster_id' => 4, 'name' => 'Biomedical Technology', 'short_name' => 'BT']);

        // inkl. Folgesemester nach dem Plan (Double-Degree-Semester)
        foreach (['2026-03-01', '2026-10-01', '2027-03-01', '2027-10-01', '2028-03-01', '2028-10-01'] as $date) {
            $this->semesters[$date] = Semester::create([
                'start_date' => Carbon::parse($date),
                'type' => str_ends_with($date, '-10-01') ? SemesterType::AutumnStart : SemesterType::SpringStart,
            ]);
        }

        $courses = [
            // id => [Name, Spezialisierung, Cluster, Venue, ECTS]
            111 => ['Genomics', 2, null, 'Muttenz', 3],
            112 => ['Cellular imaging', 2, null, 'Muttenz', 3],
            113 => ['Advanced Cell Culture Systems', 2, null, 'Muttenz', 4],
            114 => ['Gene- and Cell-Therapeutics', 2, null, 'Muttenz', 3],
            115 => ['Bioassays: engineered Cells, Tissues and Organisms', 2, null, 'Muttenz', 3],
            116 => ['Molecular & Translational Imaging', 2, null, 'Muttenz', 4],
            141 => ['Sensors and Signal Processing', 5, null, 'Muttenz', 3],
            142 => ['Biostructures and Solid State Sciences', 5, null, 'Muttenz', 3],
            143 => ['Implant Design and Manufacturing', 5, null, 'Muttenz', 3],
            144 => ['Medical Device Development', 5, null, 'Muttenz', 3],
            145 => ['Surgical Robotics', 5, null, 'Muttenz', 3],
            146 => ['Process Transfer and Scale-up', 5, null, 'Muttenz', 4],
            201 => ['Biocatalysis and Enzyme Engineering', null, 2, 'Wädenswil/online', 3],
            202 => ['Polymers and Applications', null, 2, 'Fribourg', 3],
            203 => ['Bioprocess Engineering', null, 2, 'Olten/online', 3],
            204 => ['Natural Products Chemistry', null, 2, 'Sion', 4],
            221 => ['Physiology and Immunotherapies', null, 4, 'Bern/online', 3],
            222 => ['Tissue Engineering for Drug Discovery', null, 4, 'Olten/Bern/online', 3],
            223 => ['Medical Technology Regulations', null, 4, 'Bern/online', 3],
            224 => ['Biomaterials', null, 4, 'Fribourg', 3],
            301 => ['Design and Analysis of Experiments', null, 1, 'online/Muttenz', 3],
            302 => ['Data and Ethics', null, 1, 'online/Muttenz', 3],
            303 => ['Handling and Visualizing Data', null, 1, 'online/Muttenz', 3],
            304 => ['Management and Leadership for Life Sciences', null, 1, 'online/Muttenz', 3],
            305 => ['Innovation and Project Management', null, 1, 'online/Muttenz', 3],
            306 => ['Business Administration for Life Sciences', null, 1, 'online/Muttenz', 3],
            307 => ['Politics and Society', null, 1, 'online/Muttenz', 3],
            401 => ['English for MSc Students', null, null, 'Muttenz', 0],
            402 => ['Deutsch für Studierende – Übung', null, null, 'Muttenz', 0],
        ];

        foreach ($courses as $id => [$name, $specialization, $cluster, $venue, $ects]) {
            Course::create([
                'id' => $id,
                'specialization_id' => $specialization,
                'cluster_id' => $cluster,
                'venue_id' => Venue::firstOrCreate(['name' => $venue])->id,
                'start_semester_id' => $this->semesters['2026-03-01']->id,
                'semester_type' => SemesterType::BothStart,
                'name' => $name,
                'internal_name' => 'MSLS-'.$id,
                'content' => $name,
                'ects' => $ects,
                'block' => false,
            ]);
        }

        $subjects = [
            2 => ['cell culture systems & 3D models', 'gene- and cell-therapeutics', 'cellular imaging'],
            5 => ['additive manufacturing', 'biomedical sensors and signal processing', 'active implants'],
        ];

        foreach ($subjects as $specialization => $names) {
            foreach ($names as $name) {
                $this->theses[$name] = Thesis::create(['specialization_id' => $specialization, 'name' => $name]);
            }
        }

        PageContent::create([
            'name' => 'pdf_text',
            'content' => '<p><b>Please note</b> that the module offer and the timing of the modules may change in the future. Änderungen vorbehalten – Stand: HS 2026.</p>',
        ]);
    }

    /**
     * Vollzeit ab HS 2026, Applied Cell Biology (vgl. harness/fixtures/pdf/ft-acb-as26-basic.json).
     */
    protected function fullTimePlan(): array
    {
        $autumn = $this->semesters['2026-10-01']->id;
        $spring = $this->semesters['2027-03-01']->id;

        return [
            'additional_comments' => '',
            'double_degree' => false,
            'given_name' => 'Anna',
            'master_thesis' => [
                'further_details' => '',
                'theses' => [
                    $this->theses['cell culture systems & 3D models']->id,
                    $this->theses['gene- and cell-therapeutics']->id,
                ],
                'time_frames' => $this->thesisTimeFrame('2026-10-01', 'Mid year 2027', 'spring 2028'),
            ],
            'modules_outside' => [],
            'overlapping_courses' => [
                ['courses' => [[111, 201], [301, 302]], 'semesterId' => $autumn],
                ['courses' => [[114, 203]], 'semesterId' => $spring],
            ],
            'selected_courses' => [
                ['courses' => [111, 112, 113, 201, 202, 301, 302, 303], 'semesterId' => $autumn],
                ['courses' => [114, 115, 116, 203, 204, 304, 305, 306, 402], 'semesterId' => $spring],
            ],
            'semester' => $autumn,
            'specialization' => 2,
            'statistics' => [
                'cluster' => 4,
                'core' => 6,
                'ects' => 51,
                'moduleGroupCount' => [],
                'outside' => 0,
                'specialization' => 6,
            ],
            'study_mode' => 1,
            'surname' => 'Muster',
        ];
    }

    /**
     * Zeitraum, wie ihn POST /api/thesisdata liefert und das Frontend
     * unveraendert zurueckschickt.
     */
    protected function thesisTimeFrame(string $start, string $longName, string $end): array
    {
        $semester = $this->semesters[$start];

        return [
            'start' => [
                'id' => $semester->id,
                'type' => $semester->type->value,
                'start_date' => $semester->start_date->toJSON(),
                'long_name' => $longName,
                'name' => $semester->name,
                'short_name' => $semester->short_name,
                'long_name_with_short' => $longName,
                'tooltip' => $semester->tooltip,
                'year' => $semester->year,
                'is_replanning' => $semester->is_replanning,
            ],
            'end' => $end,
        ];
    }

    protected function exportPdf(array $plan): PdfDocument
    {
        $path = $this->postJson('/api/pdf', $plan)->assertOk()->getContent();

        $this->assertStringStartsWith('storage/', $path);
        $file = substr($path, strlen('storage/'));
        Storage::disk('public')->assertExists($file);

        return PdfDocument::fromFile(Storage::disk('public')->path($file));
    }

    /**
     * Tabellen des Abschnitts "Study Programme" bis zur Zeile $end:
     * Semester-Ueberschrift => Zeilen "Titel Typ ECTS Venue".
     *
     * @param list<string> $lines
     * @return array<string, list<string>>
     */
    protected function semesterTables(array $lines, string $end): array
    {
        $this->assertContains('Study Programme', $lines);
        $this->assertContains($end, $lines);

        $start = array_search('Study Programme', $lines, true) + 1;
        $section = array_slice($lines, $start, array_search($end, $lines, true) - $start);
        $tables = [];
        $heading = null;

        foreach ($section as $index => $line) {
            if (($section[$index + 1] ?? null) === 'Module Title Type ECTS Venue') {
                $heading = $line;
                $tables[$heading] = [];
            } elseif ($line !== 'Module Title Type ECTS Venue') {
                $tables[$heading][] = $line;
            }
        }

        return $tables;
    }

    /**
     * @param list<string> $expected
     * @param list<string> $lines
     */
    protected function assertLinesInOrder(array $expected, array $lines): void
    {
        $start = array_search($expected[0], $lines, true);

        $this->assertNotFalse($start, "Zeile '{$expected[0]}' fehlt im PDF:\n".implode("\n", $lines));
        $this->assertSame($expected, array_slice($lines, $start, count($expected)));
    }
}
