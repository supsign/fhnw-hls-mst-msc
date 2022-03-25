<?php

namespace App\Imports\ConfigurationSheets;

use App\Models\Specialization;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ThesisSheetImport implements ToCollection, WithHeadingRow
{
    public function __construct()
    {
        $this->validSpecializationIds = Specialization::all()->pluck('id')->toArray();   
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $row = $row->ToArray();

            if (!isset($row['subject'])) {
                continue;
            }

            if (!in_array($row['specialisation'], $this->validSpecializationIds)) {
                //  error handling

                return;
            }

            dump($row);
        }
    }
}