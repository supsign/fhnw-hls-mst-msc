<?php

namespace App\Http\Controllers\Public;

use App\Enums\StudyMode;
use App\Http\Controllers\Controller;
use App\Models\Specialization;
use App\Services\Home\HomePageContentService;
use App\Services\Semesters\GetSemestersForSelectService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function show(GetSemestersForSelectService $getSemestersForSelectService, HomePageContentService $homePageContentService): View
    {
        extract($homePageContentService());

        if (!isset($introContent) || !isset($introLink) || !isset($introTitle)) {
            //  Abfangen wenn required config values fehlen, was anzeigen?
            abort(500);
        }

        return view('home', [
            'semesters' => $getSemestersForSelectService(),
            'specializations' => Specialization::all()->toArray(),
            'studyModes' => StudyMode::asArray(),
            'introContent' => $introContent,
            'introLink' => $introLink,
            'introTitle' => $introTitle,
        ]);
    }
}
