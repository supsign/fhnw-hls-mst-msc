<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
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

    public function formErrorBag($errorBag): void
    {
        $this->setErrorBag($errorBag);
    }

    public function updated(): void
    {
        $this->emit('change'.ucfirst($this->name), $this->value);
    }

    public function render(): View
    {
        return view('livewire.input');
    }
}
