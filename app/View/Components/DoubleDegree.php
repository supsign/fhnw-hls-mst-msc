<?php

namespace App\View\Components;

use App\Services\PageContents\PageContentService;
use Illuminate\View\Component;
use Illuminate\View\View;

class DoubleDegree extends Component
{
    public ?string $doubleDegreeTitle;
    public ?string $doubleDegreeDescription;
    public ?string $doubleDegreeCheckboxText;

    protected array $pageContents = [
        'double_degree_title',
        'double_degree_description',
        'double_degree_checkbox_text'

    ];

    public function __construct(protected PageContentService $pageContentService)
    {
        foreach (($this->pageContentService)($this->pageContents) AS $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function render(): View
    {
        return view('components.double-degree');
    }
}
