<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostConfigurationRequest;

class ConfigurationController extends Controller
{
    public function post(PostConfigurationRequest $request)
    {
        dump($request->validated());

        return 1;
    }

    public function show()
    {
        return view('admin.config');
    }
}
