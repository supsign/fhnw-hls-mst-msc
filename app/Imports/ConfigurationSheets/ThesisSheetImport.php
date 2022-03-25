<?php

namespace App\Imports\ConfigurationSheets;

use App\Models\Thesis;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ThesisSheetImport implements ToCollection, WithHeadingRow
{
    public function __construct()
    {
        // $this->validSpecializationIds = $validSpecializationIds;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row)
        {
            $row = $row->ToArray();

            if (!isset($row['subject'])) {
                continue;
            }

            // if (!in_array($row['specialisation'], $this->validSpecializationIds)) {
            //     //  error handling

            //     return;
            // }

            Thesis::create([
                'name' => $row['subject'],
                'specialization_id' => $row['specialisation'],
            ]);
        }
    }
}