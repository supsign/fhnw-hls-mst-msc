<?php

namespace App\Http\Controllers\Public;

use App\Enums\StudyMode;
use App\Http\Controllers\Controller;
use App\Models\Specialization;
use App\Services\PageContents\PageContentService;
use App\Services\GetPdfDataService;
use App\Services\Semesters\GetSemestersForSelectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;


class HomeController extends Controller
{
    public function show(GetSemestersForSelectService $getSemestersForSelectService, PageContentService $pageContentService): View
    {
        extract($pageContentService(['intro_content', 'intro_title']));

        return view('home', [
            'semesters' => $getSemestersForSelectService(),
            'specializations' => Specialization::all()->toArray(),
            'studyModes' => StudyMode::asArray(),
            'introContent' => $introContent,
            'introTitle' => $introTitle,
        ]);
    }

    public function pdf(Request $request, GetPdfDataService $getPdfDataService) 
    {

        dd(
            $getPdfDataService($request)
        );

        $pdf = App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option('enable_php', true);
        $pdf->loadView('pdf', $getPdfDataService($request));

        return $pdf->stream();
    }
}
