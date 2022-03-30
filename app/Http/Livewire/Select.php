<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Select extends Component
{
    public string | int $label = '';
    public mixed $options = [];
    public string $optionKey = '';
    public $selected;
    public string $name ='';

    public function updated(){
        $this->emit('test', $this->selected);
    }

    public function render()
    {
        return view('livewire.select');
    }
}
