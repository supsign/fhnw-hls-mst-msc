<?php

namespace App\View\Components;

use App\Helpers\GeneralHelper;
use App\Models\PageContent;
use Illuminate\View\Component;

class optionalEnglish extends Component
{
    public ?string $optionalEnglishTitle = null;
    public ?string $optionalEnglishDescription = null;

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
        'optional_english_title',
        'optional_english_description'
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
        return view('components.optional-english');
    }
}
