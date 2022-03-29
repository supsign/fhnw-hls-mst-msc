<?php

namespace App\Imports\ConfigurationSheets;

use App\Models\Link;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LinkImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row)
        {
            $row = $row->ToArray();

            if (!isset($row['id_link'])) {
                continue;
            }

            Link::create([
                'id' => $row['id_link'],
                'name' => $row['internal_name'],
                'url' => $row['link'],
            ]);
        }
    }
}