<?php

namespace App\Imports\ConfigurationSheets;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CourseGroupSheetImport implements ToModel, WithHeadingRow
{
    public function model(array $row): void
    {
    	// dump($row);
    }
}