<x-base.card>
    <form wire:submit.prevent="submit" class="flex flex-col justify-center gap-5">
        @csrf
        @if(count($this->getErrorBag()->messages()) > 0)
            <x-module-selection-form-errors :errors="$this->getErrorBag()->messages()"/>
        @endif
        <div class='text-lg'><b>Personal Data</b></div>
        <x-base.input label="Surname" type="text" name="surname" wire:model="surname" />
        <x-base.input label="Given Name" type="text" name="givenName" wire:model="givenName"/>
        <x-base.select wire:model="semesterId" label="Semester" :options="$semesters" />
        <x-base.select wire:model="studyModeId" label="Study Mode" :options="$studyModes" :tooltip="$studyModeTooltip"/>
        <x-base.select wire:model="specializationId" label="Specialization" :options="$specializations" optionKey="name" placeholder="-- Choose Specialization --"/>
        @if($specializationId)
            <livewire:course-selection
                key="{{ microtime() }}"
                :nextSemesters="$nextSemesters"
                :semesterId="(int)$semesterId"
                :studyModeId="$studyModeId"
                :specializationId="$specializationId"
                :selectedCourses="$selectedCourses"
                :coursesByCourseGroup="$coursesByCourseGroup"
                :ects="$ects"
            />
            <livewire:modules-outside-curriculum />
            <x-double-degree wire:model="doubleDegree" />
            <livewire:master-thesis
                key="{{ microtime() }}"
                :doubleDegree="$doubleDegree"
                :semesterId="(int)$semesterId"
                :studyModeId="$studyModeId"
                :specializationId="$specializationId"
                :overwrite-start-of-thesis="$masterThesis['start']['id'] ?? 0 "
                :selected-theses="$masterThesis['theses'] ?? []"
            />
            <x-optional-english
                :nextSemesters="$nextSemesters"
                :selectedCourses="$selectedCourses['main'] ?? []"
            />
            <x-additional-comments wire:model='additionalComments'/>
            <livewire:summary-statistics
                key="{{ microtime() }}" 
                :statistics='$statistics' 
                :ects='$ects'
                :semestersWithEcts='$semestersWithEcts' 
                :masterThesis='$masterThesis'
            />
            <div x-data class='w-80'>
                <input type="submit" name="submit" value="Submit" class="button-primary" @click='window.scrollTo(0,0)'/>
            </div>
        @endif
    </form>
</x-base.card>

