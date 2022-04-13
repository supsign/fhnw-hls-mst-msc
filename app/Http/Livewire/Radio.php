<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Radio extends Component
{

    public string $name;
    public string $value;

    public function updated(): void
    {
        
    }

    public function render()
    {
        return view('livewire.radio');
    }
}
