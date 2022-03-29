<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function show(): View
    {
        return view('home', [
            'semesters' => [],
            'specializations' => Specialization::all(),
            'studyMode' => [],
        ]);
    }
}
