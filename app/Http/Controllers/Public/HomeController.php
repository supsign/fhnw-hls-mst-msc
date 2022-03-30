<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use App\Models\Specialization;
use App\Services\Semesters\GetSemestersService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public $start = '';
    public $specialization;

    public function show(GetSemestersService $getSemestersService): View
    {
        $introTitle = PageContent::where('name', 'intro_title')->first()->content;
        $introContent = PageContent::where('name', 'intro_content')->first()->content;
        $introLink = PageContent::where('name', 'intro_link')->first()->content;


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
