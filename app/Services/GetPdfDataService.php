<?php

namespace App\Services;

use App\Models\Semester;
use App\Models\Specialization;
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
                    $this->data[$key] = Specialization::find($value);
                    break;

                case 'thesis_start':
                    $this->data[$key] = Semester::find($value);
                    break;

                case 'selected_courses':
                    $this->data[$key] = $this->getSelectedCourses($value);
                    break;

                default:
                    $this->data[$key] = $value;
                    break;
            }
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