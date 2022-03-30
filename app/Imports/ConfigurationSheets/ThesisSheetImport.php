<?php

namespace App\Imports\ConfigurationSheets;

use App\Models\Thesis;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\FlareClient\Http\Exceptions\InvalidData;

class ThesisSheetImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row)
        {
            $row = $row->ToArray();

            if (!isset($row['subject'])) {
                continue;
            }

            try {
                Thesis::create([
                    'name' => $row['subject'],
                    'specialization_id' => $row['specialisation'],
                ]);
            } catch (QueryException $e) {


                switch (true) {
                    case str_contains($e->getMessage(), 'theses_specialization_id_foreign'):
                        $error = 'invalid specialisation id "'.$row['specialisation'].'" found in thesis "'.$row['subject'].'"';
                        break;

                    default:
                        $error = config('constants.errors.unknown');
                        break;
                }

                throw new InvalidData($error);
            }
        }
    }
}