<?php

namespace App\Imports\ConfigurationSheets;

use App\Enums\Error;
use App\Models\Specialization;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\FlareClient\Http\Exceptions\InvalidData;

class SpecialisationSheetImport implements ToCollection, WithHeadingRow
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
                Specialization::create([
                    'id' => $row['id'],
                    'cluster_id' => $row['clustercore'],
                    'name' => $row['name'],
                    'short_name' => $row['shortname'],
                ]);
            } catch (QueryException $e) {
                switch (true) {
                    case str_contains($e->getMessage(), 'specializations_cluster_id_foreign'):
                        $error = 'invalid cluster id "'.$row['clustercore'].'" found in specialization id "'.$row['id'].'" ('.$row['name'] ?? $row['shortname'].')';
                        break;

                    case str_contains($e->getMessage(), '\'name\' cannot be null'):
                        $error = 'the column "name" found in specialization id "'.$row['id'].'" ('.$row['shortname'].') can not be empty';
                        break;

                    case str_contains($e->getMessage(), '\'short_name\' cannot be null'):
                        $error = 'the column "shortName" found in specialization id "'.$row['id'].'" ('.$row['name'].') can not be empty';
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