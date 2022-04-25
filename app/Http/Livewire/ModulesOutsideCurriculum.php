<?php

namespace App\Http\Livewire;

use App\Helpers\GeneralHelper;
use App\Models\PageContent;
use Livewire\Component;

class ModulesOutsideCurriculum extends Component
{

    public ?array $module  = [];
    public ?string $moduleOutsideTitle = null;
    public ?string $modulesOutsideDescription = null;
    public array $outsideModules = [];

    public function __construct()
    {
        $this->getPageContents();
    }
    protected array $pageContents = [
        'modules_outside_title',
        'modules_outside_description'
    ];

    protected function getPageContents(): self
    {
        $pageContents = PageContent::whereIn('name', $this->pageContents)->get();

        foreach ($pageContents AS $pageContent) {
            $this->{GeneralHelper::snakeToCamelCase($pageContent->name)} = $pageContent->content;
        }
        return $this;
    }

    function saveInput($index) {
        $this->outsideModules[$index] = $this->module;
        $this->module = [];
    }
    public function render()
    {
        return view('livewire.modules-outside-curriculum');
    }
}
