<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Input extends Component
{
    public $input;
    protected $rules = [
        'input' => 'required|min:6',
    ];

    public function updated($input)
    {
        $this->validateOnly($input);
    }

    public function render()
    {
        return view('livewire.input');
    }
}
