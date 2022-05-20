<?php

namespace App\Http\Controllers\Api;

use App\Enums\StudyMode;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\PostCourseData;
use App\Http\Requests\PostPdfData;
use App\Http\Requests\PostThesisData;
use App\Models\Semester;
use App\Models\Specialization;
use App\Services\GetCourseData;
use App\Services\GetPdfData;
use App\Services\GetPersonalData;
use App\Services\GetThesisData;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use stdClass;

class Controller extends BaseController
{
    public function getPersonalData(GetPersonalData $getPersonalData): stdClass
    {
        return $getPersonalData();
    }

    public function postCourseData(
        Specialization $specialization, 
        PostCourseData $request, 
        GetCourseData $getCourseData
    ): stdClass {
        return $getCourseData(
            $specialization,
            $request->semester ? Semester::find($request->semester) : null,
            $request->study_mode ? StudyMode::getByValue($request->study_mode) : null,
        );
    }

    public function postPdf(PostPdfData $request, GetPdfData $getPdfData): string
    {
        $data = $getPdfData($request);
        $path = Storage::path('public');
        $filename = $data['filename'];
        $pdf = App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option('enable_php', true);
        $pdf->loadView('pdf', $data);
        $pdf->save($path.'/'.$filename);

        return 'storage/'.$filename;
    }

    public function postThesisData(
        Specialization $specialization, 
        PostThesisData $request, 
        GetThesisData $getThesisData
    ): stdClass {
        return $getThesisData(
            $specialization,
            $request->double_degree,
            $request->semester ? Semester::find($request->semester) : null,
            $request->study_mode ? StudyMode::getByValue($request->study_mode) : null,
        );
    }
}
