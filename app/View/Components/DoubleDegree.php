<?php

namespace App\View\Components;

use App\Services\PageContents\PageContentService;
use Illuminate\View\Component;
use Illuminate\View\View;

class DoubleDegree extends Component
{
    public ?string $doubleDegreeTitle;
    public ?string $doubleDegreeDescription;

    protected array $pageContents = [
        'double_degree_title',
        'double_degree_description'
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
