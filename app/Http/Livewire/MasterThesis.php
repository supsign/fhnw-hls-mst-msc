<?php

namespace App\Http\Livewire;

use App\Enums\StudyMode;
use App\Models\Semester;
use App\Models\Thesis;
use App\Services\Semesters\GetUpcomingSemestersService;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Livewire\Component;

class MasterThesis extends Component
{
    public array $availibleStarts;
    public array $startOfThesis;
    public string $endOfThesis;
    public array $theses;
    public array $selectedTheses = [];
    public ?string $furtherDetails = null;

    public array $masterThesis;

    public bool $doubleDegree;

    public int $overwriteStartOfThesis = 0;
    public int $semesterId;
    public int $specializationId;
    public int $studyModeId;

    protected GetUpcomingSemestersService $getUpcomingSemestersService;

    public function boot(): void
    {
        $this->getUpcomingSemestersService = App::make(GetUpcomingSemestersService::class);
    }

    public function mount(): void
    {
        $this->theses = Thesis::where('specialization_id', $this->specializationId)->get()->toArray();
        $this->getStartOfThesis();
    }

    public function render(): View
    {
        return view('livewire.master-thesis');
    }

    public function updated(): void
    {
        $this->getStartOfThesis();
        $this->endOfThesis = $this->getThesisEndDate($this->startOfThesis);

        $this->emit('updateMasterThesis', $this->startOfThesis, $this->endOfThesis, $this->selectedTheses , $this->furtherDetails);
    }

    protected function getStartOfThesis(): self
    {
        $offset = $this->studyModeId === StudyMode::FullTime->value ? 3 : 6;

        if ($this->doubleDegree) {
            $offset++;
        }

        $startOfThesis = ($this->getUpcomingSemestersService)(
            $offset,
            Semester::find($this->semesterId)->start_date,
        )->last();

        $this->availibleStarts = ($this->getUpcomingSemestersService)(4, $startOfThesis->start_date)->toArray();
        $this->startOfThesis = $this->overwriteStartOfThesis
            ? Semester::find($this->overwriteStartOfThesis)->toArray()
            : $startOfThesis->toArray();        

        return $this;
    }

    protected function getThesisEndDate($semester) 
    {
        $start = Carbon::parse($semester['start_date']);

        switch ($start->month) {
            case 2:
                return $start->addMonth(8)->toDateTimeString();
            case 6:
                return $start->addMonth(9)->toDateTimeString();
        }

        return $semester['start_date'];
    }
}
