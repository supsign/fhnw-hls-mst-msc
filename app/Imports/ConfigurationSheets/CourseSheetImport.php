<?php

namespace App\Imports\ConfigurationSheets;

use App\Enums\Error;
use App\Models\Course;
use App\Models\CourseCourseGroup;
use App\Models\Slot;
use App\Models\Venue;
use App\Services\GetSemester;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\FlareClient\Http\Exceptions\InvalidData;

class CourseSheetImport implements ToCollection, WithHeadingRow
{
    protected array $courseCourseGroupColumns = ['ac', 'acb', 'ba', 'bme', 'bt', 'ce', 'et', 'osc', 'pt'];

    public function collection(Collection $rows): void
    {
        $getSemesterService = App::make(GetSemester::class);

        foreach ($rows as $row) {
            $row = $row->ToArray();

            if (!isset($row['id'])) {
                continue;
            }

            try {
                if (!empty($row['end'])) {
                    $endSemester = $row['semshort'] === 'SS' 
                        ? $getSemesterService($row['end'] + 1, false)
                        : $getSemesterService($row['end'], true);
                } else {
                    $endSemester = null;
                }

                Course::create([
                    'id' => $row['id'],
                    'cluster_id' => $row['clustercore'],
                    'end_semester_id' => $endSemester?->id,
                    'slot_as_id' => !empty($row['slotas']) ? Slot::firstOrCreate(['name' => $row['slotas']])->id : null,
                    'slot_ss_id' => !empty($row['slotss']) ? Slot::firstOrCreate(['name' => $row['slotss']])->id : null,
                    'specialization_id' => $row['specialisation'],
                    'start_semester_id' => $getSemesterService($row['start'], $row['semshort'] === 'AS')->id,
                    'venue_id' => !empty($row['venue']) ? Venue::firstOrCreate(['name' => $row['venue']])->id : null,
                    'semester_type' => $row['semshort'] === 'SS' ? 2 : 1,
                    'name' => $row['modulename'],
                    'internal_name' => $row['internalcode'],
                    'short_name' => $row['short'],
                    'content' => $row['content'],
                    'ects' => $row['ects'],
                    'block' => $row['block'],
                ]);
            } catch (QueryException $e) {
                switch (true) {
                    case str_contains($e->getMessage(), 'courses_specialization_id_foreign'):
                        $error = 'invalid specialisation id "'.$row['specialisation'].'" found in module group "'.$row['id'].'" ('.$row['modulename'].')';
                        break;

                    case str_contains($e->getMessage(), '\'name\' cannot be null'):
                        $error = 'the column "moduleName" found in course id "'.$row['id'].'" ('.$row['internalcode'].') can not be empty';
                        break;

                    case str_contains($e->getMessage(), '\'internal_name\' cannot be null'):
                        $error = 'the column "internalCode" found in course id "'.$row['id'].'" ('.$row['modulename'].') can not be empty';
                        break;

                    case str_contains($e->getMessage(), '\'content\' cannot be null'):
                        $error = 'the column "content" found in course id "'.$row['id'].'" ('.$row['modulename'].') can not be empty';
                        break;

                    case str_contains($e->getMessage(), '\'ects\' cannot be null'):
                        $error = 'the column "ects" found in course id "'.$row['id'].'" ('.$row['modulename'].') can not be empty';
                        break;

                    case str_contains($e->getMessage(), '\'block\' cannot be null'):
                        $error = 'the column "block" found in course id "'.$row['id'].'" ('.$row['modulename'].') can not be empty';
                        break;

                    default:
                        dump($e->getMessage());
                        $error = Error::Unknown->label();
                        break;
                }

                throw new InvalidData($error);
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
                switch (true) {
                    case str_contains($e->getMessage(), 'course_course_group_course_group_id_foreign'):
                        $error = 'invalid module group id "'.$row[$column].'" found in module "'.$row['id'].'" ('.$row['modulename'].')';
                        break;

                    default:
                        $error = Error::Unknown->label();
                        break;
                }

                throw new InvalidData($error);
            }
        }
    }
}