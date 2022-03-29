<?php

namespace App\Imports\ConfigurationSheets;

use App\Models\CourseGroup;
use App\Models\CourseGroupSpecialization;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\FlareClient\Http\Exceptions\InvalidData;

class CourseGroupSheetImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row)
        {
            $row = $row->ToArray();

            if (!isset($row['id'])) {
                continue;
            }

            try {
                CourseGroup::create([
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'internal_name' => $row['internalname'],
                    'required_courses_count' => $row['requiredmodulescount'],
                ]);

                foreach (explode(',', $row['specialisation']) AS $specialisationId) {
                    CourseGroupSpecialization::create([
                        'course_group_id' => $row['id'],
                        'specialization_id' => $specialisationId,
                    ]);
                }
            } catch (QueryException $e) {
                throw new InvalidData('invalid specialisation id "'.$specialisationId.'" found in module group "'.$row['id'].'" ('.$row['name'].')');
            }
        }
    }
}