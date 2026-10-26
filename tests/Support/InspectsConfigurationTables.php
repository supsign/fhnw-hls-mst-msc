<?php

namespace Tests\Support;

use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Blick auf die Tabellen, die der Konfigurations-Import fuellt (dieselben wie
 * in ConfigurationWorkbook::REALISTIC_ROW_COUNTS). Die Konfigurationstabellen
 * leert ConfigurationImport vorher, Semester, Slots und Venues ergaenzt er nur.
 */
trait InspectsConfigurationTables
{
    /**
     * @return array<string, int>
     */
    protected function rowCounts(): array
    {
        $counts = [];

        foreach (array_keys(ConfigurationWorkbook::REALISTIC_ROW_COUNTS) as $table) {
            $counts[$table] = DB::table($table)->count();
        }

        return $counts;
    }

    /**
     * Vollstaendiger Inhalt der Tabellen, sortiert.
     *
     * @return array<string, list<array<string, mixed>>>
     */
    protected function tableContents(): array
    {
        $contents = [];

        foreach (array_keys(ConfigurationWorkbook::REALISTIC_ROW_COUNTS) as $table) {
            $rows = DB::table($table)->get()->map(fn (stdClass $row): array => (array)$row)->all();
            sort($rows);
            $contents[$table] = $rows;
        }

        return $contents;
    }
}
