<?php

namespace App\View\Components;

use App\Services\PageContents\PageContentService;
use Illuminate\View\Component;

class AdditionalComments extends Component
{

    public ?string $additionalCommentsTitle = null;

    public function __construct(protected PageContentService $pageContentService)
    {
        foreach (($this->pageContentService)(['additional_comments_title']) AS $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.additional-comments');
    }
}
