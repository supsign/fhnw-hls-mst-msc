<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostConfigurationRequest;
use App\Services\Imports\ConfigurationImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConfigurationController extends Controller
{
    public function post(PostConfigurationRequest $request, ConfigurationImportService $configurationImportService): RedirectResponse
    {
        $response = $configurationImportService(
            $request->config_file->storeAs('config', $request->config_file->getClientOriginalName())
        );

        if ($response['status'] === 'success') {
            toast($response['status'], $response['status']);
        } elseif ($response['status'] === 'error') {
            toast($response['error'], $response['status']);
        }

        return redirect()->route('admin.config.show');
    }

    public function show(): View
    {
        return view('admin.config');
    }
}
