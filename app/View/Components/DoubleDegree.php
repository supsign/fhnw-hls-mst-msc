<?php

namespace App\View\Components;

use App\Helpers\GeneralHelper;
use App\Models\PageContent;
use Illuminate\View\Component;

class DoubleDegree extends Component
{
    public ?string $doubleDegreeTitle = null;
    public ?string  $doubleDegreeDescription = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->getPageContents();
    }
    protected array $pageContents = [
        'double_degree_title',
        'double_degree_description'
    ];

    protected function getPageContents(): self
    {
        $pageContents = PageContent::whereIn('name', $this->pageContents)->get();

        foreach ($pageContents AS $pageContent) {
            $this->{GeneralHelper::snakeToCamelCase($pageContent->name)} = $pageContent->content;
        }
        return $this;
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.double-degree');
    }
}
