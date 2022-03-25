<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Input extends Component
{
    public $input;
    public bool $typing = false;

    public function changeType(bool $state = false) {
        $this->typing = $state;
    }
    public function render()
    {
        return view('livewire.input');
    }
}
