<?php

namespace App\View\Components;

use App\Models\Course;
use App\Services\PageContents\PageContentService;
use Illuminate\View\Component;
use Illuminate\View\View;

class OptionalEnglish extends Component
{
    public array $courses;
    public ?string $optionalEnglishTitle;
    public ?string $optionalEnglishDescription;

    protected array $pageContents = [
        'optional_english_title',
        'optional_english_description'
    ];

    public function __construct(
        public array $nextSemesters,
        public array $selectedCourses,
        protected PageContentService $pageContentService
    ) {
        foreach (($this->pageContentService)($this->pageContents) AS $key => $value) {
            $this->{$key} = $value;
        }

        $this->courses = Course::with('semesters')
            ->whereNull('cluster_id')
            ->whereNull('specialization_id')
            ->get()
                ->toArray();
    }

    public function render(): View
    {
        return view('components.optional-english');
    }
}
