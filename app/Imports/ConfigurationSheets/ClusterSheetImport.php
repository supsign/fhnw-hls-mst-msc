<?php

namespace App\Imports\ConfigurationSheets;

use App\Models\Cluster;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClusterSheetImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row)
        {
            $row = $row->ToArray();

            if (!isset($row['id'])) {
                continue;
            }

            Cluster::create([
                'id' => $row['id'],
                'name' => $row['cluster'],
                'core_competences' => $row['corecompetences'],
            ]);
        }
    }
}