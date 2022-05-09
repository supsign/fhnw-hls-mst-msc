<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostConfiguration;
use App\Services\ConfigurationImport;
use stdClass;

class AdminController extends Controller
{
    public function postConfiguration(PostConfiguration $request, ConfigurationImport $configurationImport) //: stdClass
    {
        $response = $configurationImport(
            $request->config_file->storeAs('config', $request->config_file->getClientOriginalName())
        );

        if ($response->status !== 'success') {
            abort(500);
        }

        return redirect('http://localhost:3000/admin/config');
    }
}
