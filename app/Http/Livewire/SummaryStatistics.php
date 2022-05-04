<?php

namespace App\Http\Livewire;

use Livewire\Component;

class SummaryStatistics extends Component
{
    public array $statistics = [];

    public function render()
    {
        return view('livewire.summary-statistics');
    }
}
