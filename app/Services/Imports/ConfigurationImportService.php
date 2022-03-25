<?php

namespace App\Services\Imports;

use App\Imports\ConfigurationImport;
use Exception;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Facades\Storage;

class ConfigurationImportService
{
    public function __construct(protected ConfigurationImport $configurationImport, protected Excel $excel)
    {
        
    }

    public function __invoke(string $file)
    {
        if (!Storage::exists($file)) {
            throw new Exception('"'.$file.'" not found');
        }

        $this->excel->import($this->configurationImport, $file);
    }
}