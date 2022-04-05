<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Input extends Component
{
    public string $label;
    public string $name;
    public string $type;
    public int|string $value = '';

    protected $listeners = [
        'formErrorBag',
    ];

    public function formErrorBag($errorBag)
    {
        $this->setErrorBag($errorBag);
    }

    public function updated(): void
    {
        $this->emit('change'.ucfirst($this->name), $this->value);
    }

    public function render()
    {
        return view('livewire.input');
    }
}
