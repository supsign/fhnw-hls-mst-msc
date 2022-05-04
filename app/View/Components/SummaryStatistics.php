<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SummaryStatistics extends Component
{
    public array $statistics = [];

    public function __construct()
    {
        //
    }

    public function render()
    {
        return view('components.summary-statistics');
    }
}
