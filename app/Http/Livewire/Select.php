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
    public int|string $selected = '';

    public function updated(): void
    {
        $this->emit('change'.ucfirst($this->name), $this->selected);
    }

    public function render(): View
    {
        return view('livewire.select');
    }
}
