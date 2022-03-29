<?php

namespace App\Imports\ConfigurationSheets;

use App\Models\Course;
use App\Models\CourseCourseGroup;
use App\Models\Slot;
use App\Models\Venue;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\FlareClient\Http\Exceptions\InvalidData;

class CourseSheetImport implements ToCollection, WithHeadingRow
{
    protected array $courseCourseGroupColumns = ['ac', 'acb', 'ba', 'bme', 'bt', 'ce', 'et', 'osc', 'pt'];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row)
        {
            $row = $row->ToArray();

            if (!isset($row['id'])) {
                continue;
            }

            try {
                Course::create([
                    'id' => $row['id'],
                    'cluster_id' => $row['clustercore'],
                    'slot_as_id' => !empty($row['slotas']) ? Slot::firstOrCreate(['name' => $row['slotas']])->id : null,
                    'slot_ss_id' => !empty($row['slotss']) ? Slot::firstOrCreate(['name' => $row['slotss']])->id : null,
                    'specialization_id' => $row['specialisation'],
                    'venue_id' => !empty($row['venue']) ? Venue::firstOrCreate(['name' => $row['venue']])->id : null,
                    'name' => $row['modulename'],
                    'internal_name' => $row['internalcode'],
                    'short_name' => $row['short'],
                    'content' => $row['content'],
                    'ects' => $row['ects'],
                    'block' => $row['block'],
                ]);
            } catch (QueryException $e) {
                throw new InvalidData('invalid specialisation id "'.$row['specialisation'].'" found in module group "'.$row['id'].'" ('.$row['modulename'].')');
            }

            try {
                foreach ($this->courseCourseGroupColumns AS $column) {
                    if (empty($row[$column])) {
                        continue;
                    }

                    CourseCourseGroup::firstOrCreate([
                        'course_id' => $row['id'],
                        'course_group_id' => $row[$column],
                    ]);
                }
            } catch (QueryException $e) {
                throw new InvalidData('invalid module group id "'.$row[$column].'" found in module "'.$row['id'].'" ('.$row['modulename'].')');
            }
        }
    }
}