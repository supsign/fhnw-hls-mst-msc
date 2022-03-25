<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Inline extends Component
{

    public function render()
    {
        return <<<'blade'
            <div>
               Im an Inline Component with no blade
            </div>
        blade;
    }
}
