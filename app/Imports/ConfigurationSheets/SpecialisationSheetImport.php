<?php

namespace App\Imports\ConfigurationSheets;

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
                throw new InvalidData('invalid cluster id "'.$row['clustercore'].'" found in specialization id "'.$row['id'].'" ('.$row['name'].')');
            }
        }
    }
}