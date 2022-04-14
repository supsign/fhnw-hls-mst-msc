<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class Radio extends Component
{

    public string $name;
    public string $value;

    public function updated(): void
    {
        
    }

    public function render(): View
    {
        return view('livewire.radio');
    }
}
