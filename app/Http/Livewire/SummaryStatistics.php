<?php

namespace App\Http\Livewire;

use Livewire\Component;

class SummaryStatistics extends Component
{
    public array $statistics = [];
    public array $semestersWithEcts = [];
    public int $ects = 0;


    public function render()
    {
        return view('livewire.summary-statistics');
    }
}
