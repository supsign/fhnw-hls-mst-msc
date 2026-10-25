<?php

namespace App\Imports\ConfigurationSheets;

use App\Exceptions\InvalidData;
use App\Models\PageContent;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PageContentSheetImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $row = $row->ToArray();

            if (!isset($row['key'])) {
                continue;
            }

            try {
                PageContent::create([
                    'name' => $row['key'],
                    'content' => $row['content'],
                ]);
            } catch (QueryException $e) {
                throw new InvalidData('"content" column for key "'.$row['key'].'" can not be empty');
            }
        }
    }
}
