<?php

namespace App\Http\Controllers;

use App\Services\Imports\ConfigurationImportService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function test(ConfigurationImportService $configurationImportSerivce)
    {
        return $configurationImportSerivce('Konfiguration_v1.3.xlsx');
    }
}