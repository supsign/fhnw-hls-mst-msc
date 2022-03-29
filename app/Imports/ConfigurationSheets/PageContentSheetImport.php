<?php

namespace App\Imports\ConfigurationSheets;

use App\Models\PageContent;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PageContentSheetImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row)
        {
            $row = $row->ToArray();

            if (!isset($row['key'])) {
                continue;
            }

            PageContent::create([
                'name' => $row['key'],
                'content' => $row['content'],
            ]);
        }
    }
}