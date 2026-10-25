<?php

namespace Tests\Support;

use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Baut die Admin-Konfigurations-Excel (siehe App\Imports\Configuration) zur
 * Laufzeit aus lesbaren Zeilen statt aus einer Binaerdatei. Der PHP-Typ wird
 * zum Zelltyp: Strings werden immer Textzellen (auch "2023"), int/float
 * Zahlzellen, null bleibt leer. Nur PhpSpreadsheet-APIs aus 1.30 und 5.x.
 */
final class ConfigurationWorkbook
{
    /**
     * Zeilen je Tabelle nach dem Import von realistic() in eine leere
     * Datenbank. Leerzeilen, Abschnittszeilen und Zeilen ohne Schluessel
     * (id/subject/key/id_link) zaehlen nicht mit.
     */
    public const REALISTIC_ROW_COUNTS = [
        'clusters' => 3,
        'specializations' => 2,
        'theses' => 5,
        'course_groups' => 6,
        'course_group_specialization' => 8,
        'courses' => 15,
        'course_course_group' => 14,
        'page_contents' => 16,
        'links' => 2,
        'semesters' => 7,
        'slots' => 11,
        'venues' => 5,
    ];

    /**
     * @param array<string, array{header: list<string>, rows: list<array<string, mixed>>}> $sheets
     */
    private function __construct(private array $sheets)
    {
    }

    /**
     * Kleine, aber vollstaendige Konfiguration; Besonderheiten sind kommentiert.
     */
    public static function realistic(): self
    {
        $moduleGroupColumns = ['AC', 'ACB', 'BA', 'DS', 'BT', 'CE', 'ET', 'OSC', 'PT'];

        return new self([
            // fremdes Sheet und abweichende Reihenfolge: der Import liest nur die benannten Sheets
            'Info' => [
                'header' => ['Studienplaner MSc Life Sciences'],
                'rows' => [['Studienplaner MSc Life Sciences' => 'Stand HS 2026']],
            ],
            'Modules' => [
                'header' => array_merge(
                    ['id', 'ClusterCore', 'specialisation', 'start', 'end', 'semShort', 'semLong', 'slotAS', 'slotSS', 'venue'],
                    ['moduleName', 'internalCode', 'short', 'content', 'ECTS', 'block'],
                    $moduleGroupColumns,
                    ['comment'],
                ),
                'rows' => [
                    ['id' => 101, 'specialisation' => 1, 'start' => 2022, 'semShort' => 'AS', 'semLong' => 'AS-A', 'slotAS' => 'Slot 1', 'venue' => 'Muttenz', 'moduleName' => 'Advanced NMR Spectroscopy', 'internalCode' => 'MSLS-S-AC-01', 'short' => 'NMR', 'content' => 'Multidimensional NMR techniques.', 'ECTS' => 3, 'block' => 0, 'AC' => 1],
                    // short leer -> NULL
                    ['id' => 102, 'specialisation' => 1, 'start' => 2022, 'semShort' => 'SS', 'semLong' => 'SS-A', 'slotSS' => 'Slot A', 'venue' => 'Muttenz', 'moduleName' => 'Green Chemistry', 'internalCode' => 'MSLS-S-AC-02', 'content' => 'Principles of green chemistry.', 'ECTS' => 3, 'block' => 0, 'AC' => 1],
                    // Blockkurs am Semesterende (AS-B) -> thesis_warning
                    ['id' => 103, 'specialisation' => 1, 'start' => 2022, 'semShort' => 'AS', 'semLong' => 'AS-B', 'slotAS' => 'Block 1', 'venue' => 'Muttenz', 'moduleName' => 'Reaction Technology', 'internalCode' => 'MSLS-S-AC-03', 'content' => 'Reactor design and scale-up.', 'ECTS' => 4, 'block' => 1, 'AC' => 1],
                    // beide Semester (BS), zwei Slots -> der erste gewinnt; in zwei Gruppen
                    ['id' => 104, 'specialisation' => 1, 'start' => 2022, 'semShort' => 'BS', 'semLong' => 'BS', 'slotAS' => 'Slot 5', 'slotSS' => 'Slot E', 'venue' => 'Muttenz', 'moduleName' => 'Process Development and Technology', 'internalCode' => 'MSLS-S-AC-04', 'content' => 'From lab to plant.', 'ECTS' => 3, 'block' => 0, 'AC' => 1, 'ACB' => 6],
                    ['id' => 111, 'specialisation' => 2, 'start' => 2022, 'semShort' => 'AS', 'semLong' => 'AS-A', 'slotAS' => 'Slot 2', 'venue' => 'Muttenz', 'moduleName' => 'Genomics', 'internalCode' => 'MSLS-S-ACB-01', 'content' => 'Next generation sequencing.', 'ECTS' => 3, 'block' => 0, 'AC' => 3, 'ACB' => 2],
                    // Startjahr als Text "2023"; SS beginnt im Folgejahr
                    ['id' => 112, 'specialisation' => 2, 'start' => '2023', 'semShort' => 'SS', 'semLong' => 'SS-A', 'slotSS' => 'Slot B', 'venue' => 'Muttenz', 'moduleName' => 'Gene- and Cell-Therapeutics', 'internalCode' => 'MSLS-S-ACB-02', 'content' => 'Viral vectors, CAR-T, CRISPR.', 'ECTS' => 3, 'block' => 0, 'ACB' => 2],
                    // zweistelliges Startjahr, Endjahr, spitze Klammern im Namen
                    ['id' => 113, 'specialisation' => 2, 'start' => 24, 'end' => 2027, 'semShort' => 'AS', 'semLong' => 'AS-A', 'slotAS' => 'Slot 3', 'venue' => 'Muttenz', 'moduleName' => 'Lab-on-a-Chip <Microfluidics>', 'internalCode' => 'MSLS-S-ACB-03', 'content' => 'Microfluidic devices.', 'ECTS' => 3, 'block' => 0, 'ACB' => 2],
                    ['moduleName' => '--- Cluster-specific modules ---', 'comment' => 'Abschnittszeile ohne id'],
                    // gleiche Gruppe in zwei Spalten -> nur eine Zuordnung
                    ['id' => 201, 'ClusterCore' => 2, 'start' => 2022, 'semShort' => 'AS', 'semLong' => 'AS-A', 'slotAS' => 'Slot 2', 'venue' => 'Wädenswil/online', 'moduleName' => 'Biocatalysis and Enzyme Engineering', 'internalCode' => 'MSLS-CS-CB-01', 'content' => 'Enzyme discovery.', 'ECTS' => 3, 'block' => 0, 'AC' => 4, 'ACB' => 4],
                    // bis SS 2026 angeboten -> fuer Plaene ab HS 2026 ausgeblendet
                    ['id' => 202, 'ClusterCore' => 2, 'start' => 2022, 'end' => 2025, 'semShort' => 'SS', 'semLong' => 'SS-A', 'slotSS' => 'Slot B', 'venue' => 'Sion', 'moduleName' => 'Food Biotechnology (discontinued)', 'internalCode' => 'MSLS-CS-CB-02', 'content' => 'Fermented food.', 'ECTS' => 3, 'block' => 0, 'AC' => 4, 'ACB' => 4],
                    // Cluster ohne eigene Spezialisierung, keiner Gruppe zugeordnet
                    ['id' => 211, 'ClusterCore' => 3, 'start' => 2022, 'semShort' => 'AS', 'semLong' => 'AS-A', 'slotAS' => 'Slot 3', 'venue' => 'Olten', 'moduleName' => 'Progresses in Food Processing', 'internalCode' => 'MSLS-CS-FNH-01', 'content' => 'Novel food processing.', 'ECTS' => 3, 'block' => 0],
                    ['id' => 301, 'ClusterCore' => 1, 'start' => 2022, 'semShort' => 'AS', 'semLong' => 'AS-A', 'slotAS' => 'Slot 6', 'venue' => 'online/Muttenz', 'moduleName' => 'Design and Analysis of Experiments', 'internalCode' => 'MSLS-CC-01', 'short' => 'DoE', 'content' => 'Statistical design of experiments.', 'ECTS' => 3, 'block' => 0, 'AC' => 5, 'ACB' => 5],
                    ['id' => 302, 'ClusterCore' => 1, 'start' => 2022, 'semShort' => 'SS', 'semLong' => 'SS-A', 'slotSS' => 'Slot F', 'venue' => 'online/Muttenz', 'moduleName' => 'Management and Leadership for Life Sciences', 'internalCode' => 'MSLS-CC-02', 'content' => 'Leadership and communication.', 'ECTS' => 3, 'block' => 0, 'AC' => 5, 'ACB' => 5],
                    // Venue leer (Ort noch offen)
                    ['id' => 303, 'ClusterCore' => 1, 'start' => 2022, 'semShort' => 'BS', 'semLong' => 'BS', 'slotAS' => 'Slot 7', 'slotSS' => 'Slot G', 'moduleName' => 'Digital Health Summer School', 'internalCode' => 'MSLS-CC-03', 'content' => 'Location tba.', 'ECTS' => 2, 'block' => 1, 'AC' => 5, 'ACB' => 5],
                    // weder Cluster noch Spezialisierung -> optionaler Kurs
                    ['id' => 401, 'start' => 2022, 'semShort' => 'BS', 'semLong' => 'BS', 'slotAS' => 'Language', 'slotSS' => 'Language', 'venue' => 'Muttenz', 'moduleName' => 'English for MSc Students', 'internalCode' => 'MSLS-OPT-01', 'content' => 'Academic English, B2/C1.', 'ECTS' => 0, 'block' => 0],
                    ['id' => 402, 'start' => 2022, 'semShort' => 'AS', 'semLong' => 'AS-A', 'slotAS' => 'Language', 'venue' => 'Muttenz', 'moduleName' => 'Deutsch für Studierende – Übung', 'internalCode' => 'MSLS-OPT-02', 'content' => 'Deutschkurs: Äöü ß € «Guillemets».', 'ECTS' => 0, 'block' => 0],
                ],
            ],
            'Module Groups' => [
                'header' => ['id', 'name', 'internalName', 'requiredModulesCount', 'specialisation'],
                'rows' => [
                    ['id' => 1, 'name' => 'Specialisation Modules Applied Chemistry', 'internalName' => 'S-AC', 'requiredModulesCount' => 2, 'specialisation' => '1'],
                    // Spezialisierung als Zahlzelle statt Text
                    ['id' => 2, 'name' => 'Specialisation Modules Applied Cell Biology', 'internalName' => 'S-ACB', 'requiredModulesCount' => 2, 'specialisation' => 2],
                    ['id' => 3, 'name' => 'Elective Modules Applied Chemistry', 'internalName' => 'E-AC', 'requiredModulesCount' => 1, 'specialisation' => '1'],
                    ['id' => 4, 'name' => 'Cluster-Specific Modules Chemistry and Biotechnology', 'internalName' => 'CS-CB', 'requiredModulesCount' => 1, 'specialisation' => '1,2'],
                    ['id' => 5, 'name' => 'Core Competences', 'internalName' => 'CC', 'requiredModulesCount' => 2, 'specialisation' => '1,2'],
                    // enthaelt "Cluster-Specific" und "Elective" -> der letzte Treffer (Elective) gewinnt
                    ['id' => 6, 'name' => 'Cluster-Specific Elective Pool', 'internalName' => 'CS-EP', 'requiredModulesCount' => 1, 'specialisation' => '2'],
                ],
            ],
            'Specialisation' => [
                'header' => ['id', 'ClusterCore', 'name', 'shortName', 'comment'],
                'rows' => [
                    ['id' => 1, 'ClusterCore' => 2, 'name' => 'Applied Chemistry', 'shortName' => 'AC'],
                    ['id' => 2, 'ClusterCore' => 2, 'name' => 'Applied Cell Biology', 'shortName' => 'ACB', 'comment' => 'since HS24'],
                ],
            ],
            'ClusterCore' => [
                'header' => ['id', 'cluster', 'coreCompetences'],
                'rows' => [
                    ['id' => 1, 'cluster' => 'Core Competences', 'coreCompetences' => 'yes'],
                    ['id' => 2, 'cluster' => 'Chemistry and Biotechnology'],
                    // Leerzeile zwischen Datenzeilen -> uebersprungen
                    [],
                    ['id' => 3, 'cluster' => 'Food, Nutrition and Health'],
                ],
            ],
            'Thesis' => [
                'header' => ['subject', 'specialisation'],
                'rows' => [
                    ['subject' => 'trace analysis and environmental chemistry', 'specialisation' => 1],
                    ['subject' => 'green & sustainable synthesis', 'specialisation' => 1],
                    ['subject' => 'Ökobilanzierung & Kreislaufwirtschaft', 'specialisation' => 1],
                    ['subject' => 'cell culture systems & 3D models', 'specialisation' => 2],
                    ['subject' => 'gene- and cell-therapeutics', 'specialisation' => 2],
                    // ohne Thema -> uebersprungen
                    ['specialisation' => 2],
                ],
            ],
            'Texte' => [
                'header' => ['key', 'content'],
                'rows' => [
                    ['key' => 'intro_title', 'content' => 'Plan your MSc in Life Sciences'],
                    ['key' => 'intro_content', 'content' => '<p>Compose your study programme for the <b>MSc in Life Sciences</b>.</p>'],
                    ['key' => 'hints_title', 'content' => 'Hints'],
                    ['key' => 'hints_list', 'content' => '<ul><li>You need at least 50 ECTS.</li><li>Achten Sie auf Überschneidungen.</li></ul>'],
                    ['key' => 'study_mode_tooltip', 'content' => 'Full-time: 3 semesters incl. thesis – part-time: 5–6 semesters.'],
                    ['key' => 'group_title', 'content' => 'Choose at least #requiredCoursesCount modules from "#groupName"'],
                    ['key' => 'core_competences_description', 'content' => 'Core competence modules are mandatory for all specialisations.'],
                    ['key' => 'further_specialisation_title', 'content' => 'Modules of other specialisations'],
                    ['key' => 'further_specialisation_description', 'content' => 'Specialisation modules of other specialisations can be taken as electives.'],
                    ['key' => 'further_other_cluster_title', 'content' => 'Modules of other clusters'],
                    ['key' => 'further_other_cluster_description', 'content' => 'Cluster-specific modules of the other clusters.'],
                    ['key' => 'optional_english_title', 'content' => 'Optional language courses'],
                    ['key' => 'optional_english_description', 'content' => 'These courses do not count towards the 50 ECTS.'],
                    ['key' => 'thesis_text', 'content' => 'Choose at least two subject areas for your MSc thesis.'],
                    ['key' => 'pdf_text', 'content' => '<p><b>Please note</b> that the module offer may change. Änderungen vorbehalten – Stand: HS 2026.</p>'],
                    // Zahlzelle in einer Textspalte
                    ['key' => 'required_ects', 'content' => 50],
                    ['content' => 'Zeile ohne key -> uebersprungen'],
                ],
            ],
            'links' => [
                'header' => ['id_link', 'internal_name', 'link'],
                'rows' => [
                    ['id_link' => 1, 'internal_name' => 'msc_programme', 'link' => 'https://www.fhnw.ch/en/degree-programmes/life-sciences/master-of-science-in-life-sciences'],
                    ['id_link' => 2, 'internal_name' => 'contact', 'link' => 'mailto:msls.lifesciences@fhnw.ch'],
                    ['internal_name' => 'draft', 'link' => 'https://example.org/not-imported'],
                ],
            ],
        ]);
    }

    /**
     * Setzt in der Zeile, deren erste Spalte (id/subject/key/id_link) $key ist,
     * den Wert einer Spalte (null leert die Zelle).
     */
    public function withValue(string $sheet, int|string $key, string $column, mixed $value): self
    {
        $copy = clone $this;
        $header = $this->header($sheet);

        if (!in_array($column, $header, true)) {
            throw new InvalidArgumentException("Sheet '{$sheet}' hat keine Spalte '{$column}'");
        }

        $copy->sheets[$sheet]['rows'][$this->rowIndex($sheet, $key)][$column] = $value;

        return $copy;
    }

    /**
     * @param array<string, mixed> $row
     */
    public function withRow(string $sheet, array $row): self
    {
        $copy = clone $this;
        $this->header($sheet);
        $copy->sheets[$sheet]['rows'][] = $row;

        return $copy;
    }

    public function withoutRow(string $sheet, int|string $key): self
    {
        $copy = clone $this;
        unset($copy->sheets[$sheet]['rows'][$this->rowIndex($sheet, $key)]);
        $copy->sheets[$sheet]['rows'] = array_values($copy->sheets[$sheet]['rows']);

        return $copy;
    }

    public function withSheetRenamed(string $sheet, string $newName): self
    {
        $this->header($sheet);
        $copy = clone $this;
        $copy->sheets = [];

        foreach ($this->sheets as $name => $content) {
            $copy->sheets[$name === $sheet ? $newName : $name] = $content;
        }

        return $copy;
    }

    public function save(string $path): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach ($this->sheets as $name => ['header' => $header, 'rows' => $rows]) {
            $worksheet = $spreadsheet->createSheet();
            $worksheet->setTitle($name);

            foreach ([array_combine($header, $header), ...$rows] as $rowIndex => $row) {
                $unknown = array_diff(array_keys($row), $header);

                if ($unknown !== []) {
                    throw new InvalidArgumentException("Sheet '{$name}': unbekannte Spalten ".implode(', ', $unknown));
                }

                foreach ($header as $columnIndex => $column) {
                    $value = $row[$column] ?? null;

                    if ($value === null) {
                        continue;
                    }

                    $worksheet->setCellValueExplicit(
                        Coordinate::stringFromColumnIndex($columnIndex + 1).($rowIndex + 1),
                        $value,
                        is_string($value) ? DataType::TYPE_STRING : DataType::TYPE_NUMERIC
                    );
                }
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        (new Xlsx($spreadsheet))->save($path);
    }

    /**
     * @return list<string>
     */
    private function header(string $sheet): array
    {
        if (!isset($this->sheets[$sheet])) {
            throw new InvalidArgumentException("Sheet '{$sheet}' gibt es nicht");
        }

        return $this->sheets[$sheet]['header'];
    }

    private function rowIndex(string $sheet, int|string $key): int
    {
        $keyColumn = $this->header($sheet)[0];

        foreach ($this->sheets[$sheet]['rows'] as $index => $row) {
            if (($row[$keyColumn] ?? null) === $key) {
                return $index;
            }
        }

        throw new InvalidArgumentException("Sheet '{$sheet}' hat keine Zeile mit {$keyColumn} = {$key}");
    }
}
