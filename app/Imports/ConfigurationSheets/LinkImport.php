<?php

namespace App\Imports\ConfigurationSheets;

use App\Models\Link;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\FlareClient\Http\Exceptions\InvalidData;

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

            try {
                Link::create([
                    'id' => $row['id_link'],
                    'name' => $row['internal_name'],
                    'url' => $row['link'],
                ]);
            } catch (QueryException $e) {
                throw new InvalidData('"internal_name" and "link" column for link id "'.$row['id_link'].'" can not be empty');
            }
        }
    }
}