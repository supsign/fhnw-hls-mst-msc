<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Radio extends Component
{

    public string $name;
    public bool $value = false;
    public function render()
    {
        return view('livewire.radio');
    }
}
