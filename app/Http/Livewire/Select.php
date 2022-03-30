<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Select extends Component
{
    public string | int $label = "";
    public mixed $options = [];
    public string $optionKey = "";
    public string $test = "";
    public function render()
    {
        $this->label;
        $this->options;
        $this->optionKey;
        $this->test;
        return view('livewire.select');
    }
}
