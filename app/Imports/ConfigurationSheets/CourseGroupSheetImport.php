<?php

namespace App\Imports\ConfigurationSheets;

use App\Models\CourseGroup;
use App\Models\CourseGroupSpecialization;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

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
        }
    }
}