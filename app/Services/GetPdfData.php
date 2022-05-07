<?php

namespace App\Services;

use App\Enums\StudyMode;
use App\Helpers\GeneralHelper;
use App\Http\Requests\PostPdfData;

class GetPdfData
{
    protected array $pdfData = [];

    public function __invoke(PostPdfData $request): array
    {
        $this->addToPdfData(
            $request->only([
                'given_name', 
                'surname'
            ])
        )->addToPdfData(
            ['studyMode' => StudyMode::getByValue($request->study_mode)]
        )->addModels(
            $request->only([
                'semester', 
                'specialization',
            ])
        )->addSelectedCourses($request->selected_courses);

        return $this->pdfData;
    }

    protected function addModels(array $data): self
    {
        foreach ($data AS $key => $value) {
            $model = 'App\\Models\\'.ucfirst($key);
            $data[$key] = $model::find($value);
        }

        return $this->addToPdfData($data);
    }

    protected function addSelectedCourses(array $selectedCourses): self
    {



        return $this->addToPdfData(['selectedCourses' => $selectedCourses]);
    }

    protected function addToPdfData(array $data): self
    {
        $this->pdfData = $this->pdfData + $this->arrayKeysToCamelCase($data);

        return $this;
    }

    protected function arrayKeysToCamelCase(array $data): array
    {
        foreach ($data AS $key => $value) {
            $newKey = GeneralHelper::snakeToCamelCase($key);

            if ($newKey === $key) {
                continue;
            }

            $data[$newKey] = $value;
            unset($data[$key]);
        }

        return $data;
    }
}