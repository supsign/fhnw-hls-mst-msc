<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostConfigurationRequest;
use App\Services\Imports\ConfigurationImportService;
use RealRashid\SweetAlert\Facades\Alert;

class ConfigurationController extends Controller
{
    public function post(PostConfigurationRequest $request, ConfigurationImportService $configurationImportService)
    {
        $status = $configurationImportService(
            $request->config_file->storeAs('config', $request->config_file->getClientOriginalName())
        );

        //  Sweet Alert with $status meldung einbauen

        return redirect()->route('admin.config.show');
    }

    public function show()
    {
        return view('admin.config');
    }
}
