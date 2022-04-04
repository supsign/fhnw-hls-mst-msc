<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use App\Services\Home\HomePageContentService;
use App\Services\Semesters\GetSemestersService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public $start = '';
    public $specialization;

    public function show(GetSemestersService $getSemestersService, HomePageContentService $homePageContentService): View
    {
        extract($homePageContentService());

        return view('home', [
            'semesters' => $getSemestersService(),
            'specializations' => Specialization::all(),
            'studyModes' => config('constants.studyModes'),
            'introContent' => $introContent,
            'introLink' => $introLink,
            'introTitle' => $introTitle,
            'start' => $this->start,
        ]);
    }
}
