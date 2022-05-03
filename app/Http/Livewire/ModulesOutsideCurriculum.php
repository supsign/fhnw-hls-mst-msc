<?php

namespace App\Http\Livewire;

use App\Services\PageContents\PageContentService;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Livewire\Component;

class ModulesOutsideCurriculum extends Component
{

    public array $module  = [];
    public array $outsideModules = [];

    public ?string $moduleOutsideTitle;
    public ?string $modulesOutsideDescription;

    protected array $pageContents = [
        'modules_outside_title',
        'modules_outside_description'
    ];
    protected PageContentService $pageContentService;

    public function mount(): void
    {  
        $this->getPageContents();
    }

    protected function getPageContents(): self
    {
        $this->pageContentService = App::make(PageContentService::class);

        foreach (($this->pageContentService)($this->pageContents) AS $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    public function render(): View
    {
        return view('livewire.modules-outside-curriculum');
    }
}
