<?php

namespace App\View\Components;

use App\Models\PageContent;
use Illuminate\View\Component;

class AdditionalComments extends Component
{

    public ?string $additionalCommentsTitle = null;

    public function __construct()
    {
        $this->additionalCommentsTitle = PageContent::where('name','additional_comments_title')->first()->content;
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
