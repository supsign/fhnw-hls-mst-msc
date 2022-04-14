<?php

namespace App\Http\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class Select extends Component
{
    public string $label;
    public string $name;
    public array|Collection $options;
    public string $optionKey;
    public string|int $selected = 0;
    public string $placeholder;
    public $disablePlaceholder = false;

    protected $listeners = [
        'formErrorBag',
    ];

    public function formErrorBag($errorBag): void
    {
        $this->setErrorBag($errorBag);
    }

    public function updated(): void
    {
        $this->disablePlaceholder = true;
        $this->emit('change'.ucfirst($this->name), $this->selected);
    }

    public function render(): View
    {
        return view('livewire.select');
    }
}
