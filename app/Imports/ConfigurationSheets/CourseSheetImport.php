<?php

namespace App\Imports\ConfigurationSheets;

use App\Models\Course;
use App\Models\Slot;
use App\Models\Venue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CourseSheetImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row)
        {
            $row = $row->ToArray();

            if (!isset($row['id'])) {
                continue;
            }

            Course::create([
                'id' => $row['id'],
                'cluster_id' => $row['clustercore'],
                'slot_as_id' => Slot::firstOrCreate(['name' => $row['slotas']])->id,
                'slot_ss_id' => Slot::firstOrCreate(['name' => $row['slotas']])->id,
                'specialization_id' => $row['specialisation'],
                'venue_id' => Venue::firstOrCreate(['name' => $row['venue']])->id,
                'name' => $row['modulename'],
                'internal_name' => $row['internalcode'],
                'short_name' => $row['short'],
                'content' => $row['content'],
                'ects' => $row['ects'],
                'block' => $row['block'],
            ]);

        }
    }
}