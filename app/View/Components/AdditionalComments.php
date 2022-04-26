<?php

namespace App\View\Components;

use App\Services\PageContents\PageContentService;
use Illuminate\View\Component;
use Illuminate\View\View;

class AdditionalComments extends Component
{
    public ?string $additionalCommentsTitle = null;

    public function __construct(protected PageContentService $pageContentService)
    {
        foreach (($this->pageContentService)(['additional_comments_title']) AS $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function render(): View
    {
        return view('components.additional-comments');
    }
}
