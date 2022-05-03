<?php

namespace App\Services;

use App\Models\Semester;
use App\Models\Specialization;
use App\Models\Thesis;
use Illuminate\Http\Request;

class GetPdfDataService
{
    protected array $data = [];

    public function __invoke(Request $request): array
    {
        dump(
            $request->all()
        );

        foreach ($request->all() AS $key => $value) {
            switch ($key) {
                case 'specialization':
                    $value = Specialization::find($value);
                    break;

                case 'thesis_start':
                    $value = Semester::find($value);
                    break;

                case 'thesis_subject':
                    $value = Thesis::find($value);
                    break;

                case 'selected_courses':
                    $value = $this->getSelectedCourses($value);
                    break;
            }

            $this->data[$key] = $value;
        }

        return $this->data;
    }

    protected function getSelectedCourses(array $selectedCourseData)
    {
        dump(
            $selectedCourseData
        );
    }
}