<?php

namespace App\Imports\ConfigurationSheets;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LinkImport implements ToModel, WithHeadingRow
{
    public function model(array $row): void
    {
    	dump($row);
    }
}