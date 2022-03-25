<?php

namespace App\Imports\ConfigurationSheets;

use App\Models\Cluster;
use App\Models\Specialization;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SpecialisationSheetImport implements ToCollection, WithHeadingRow
{
    public function __construct()
    {
        $this->validClusterIds = Cluster::all()->pluck('id')->toArray();   
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $row = $row->ToArray();

            if (!isset($row['id'])) {
                continue;
            }

            if (!in_array($row['clustercore'], $this->validClusterIds)) {
                //  error handling

                return;
            }

            Specialization::updateOrCreate([
                'id' => $row['id'],
            ], [
                'cluster_id' => $row['clustercore'],
                'name' => $row['name'],
                'short_name' => $row['shortname'],
            ]);
        }
    }
}