<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Params extends Component
{
    public $paramOfBlade;
    public $paramOfPhp;
    public function render()
    {
        $this->paramOfPhp = $this->paramOfBlade;
        return view('livewire.params');
    }
}
