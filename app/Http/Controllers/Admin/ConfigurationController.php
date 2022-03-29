<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostConfigurationRequest;

class ConfigurationController extends Controller
{
    public function post(PostConfigurationRequest $request)
    {
        dd($request->validated());


    }

    public function show()
    {
        return view('admin.config');
    }
}
