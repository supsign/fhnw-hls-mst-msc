<?php

namespace App\Http\Livewire;

use App\Enums\StudyMode;
use App\Models\Semester;
use App\Models\Thesis;
use App\Services\Semesters\GetUpcomingSemestersService;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class MasterThesis extends Component
{
    public array $startOfThesis;
    public array $theses;

    public int $semesterId;
    public int $specializationId;
    public int $studyModeId;

    protected GetUpcomingSemestersService $getUpcomingSemestersService;

    public function update(): void
    {
        dump('test');
    }

    public function mount(): void
    {
        $this->getUpcomingSemestersService = App::make(GetUpcomingSemestersService::class);
        $this->theses = Thesis::where('specialization_id', $this->specializationId)->get()->toArray();

        $this->getStartOfThesis();  
    }

    public function render()
    {
        return view('livewire.master-thesis');
    }

    protected function getStartOfThesis(): self
    {
        $this->startOfThesis = ($this->getUpcomingSemestersService)(
            $this->studyModeId === StudyMode::FullTime->value ? 3 : 5,
            Semester::find($this->semesterId)->start_date,
            
        )->last()->toArray();

        return $this;
    }
}
