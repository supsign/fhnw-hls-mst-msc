<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use App\Services\Semesters\GetSemestersService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function show(GetSemestersService $getSemestersService): View
    {
        return view('home', [
            'semesters' => $getSemestersService(),
            'specializations' => Specialization::all(),
            'studyModes' => config('constants.studyModes'),
        ]);
    }
}
