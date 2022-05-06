<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostConfiguration;
use App\Services\ConfigurationImport;
use stdClass;

class AdminController extends Controller
{
    public function postConfiguration(PostConfiguration $request, ConfigurationImport $configurationImport): stdClass
    {
        return $configurationImport(
            $request->config_file->storeAs('config', $request->config_file->getClientOriginalName())
        );
    }
}
