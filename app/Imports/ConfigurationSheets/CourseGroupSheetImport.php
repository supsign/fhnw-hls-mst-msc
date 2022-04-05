<?php

namespace App\Imports\ConfigurationSheets;

use App\Enums\CourseGroupType;
use App\Enums\Error;
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
        $courseGroupTypes = CourseGroupType::withoutDefault();

        foreach ($rows as $row)
        {
            $row = $row->ToArray();

            if (!isset($row['id'])) {
                continue;
            }

            try {
                $typeId = null;

                foreach ($courseGroupTypes AS $courseGroupType) {
                    if (str_contains($row['name'], $courseGroupType->label())) {
                        $typeId = $courseGroupType->value;
                    }
                }

                if (empty($typeId)) {
                    $typeId = CourseGroupType::Default->value;
                }

                CourseGroup::create([
                    'id' => $row['id'],
                    'type' => $typeId,
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
                switch (true) {
                    case str_contains($e->getMessage(), 'course_group_specialization_specialization_id_foreign'):
                        $error = 'invalid specialisation id "'.$specialisationId.'" found in module group "'.$row['id'].'" ('.$row['name'].')';
                        break;

                    case str_contains($e->getMessage(), '\'name\' cannot be null'):
                        $error = 'the column "name" found in module group id "'.$row['id'].'" ('.$row['internalname'].') can not be empty';
                        break;

                    case str_contains($e->getMessage(), '\'internal_name\' cannot be null'):
                        $error = 'the column "internalName" found in module group id "'.$row['id'].'" ('.$row['name'].') can not be empty';
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