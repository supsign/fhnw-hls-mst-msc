<x-base.card>
    <form wire:submit.prevent="submit" class="flex flex-col justify-center gap-5">
        @csrf
        @if(count($this->getErrorBag()->messages()) > 0)
            <x-module-selection-form-errors :errors="$this->getErrorBag()->messages()"/>
        @endif
        <x-base.input label="Surname" type="text" name="surname" wire:model="$surname" />
        <x-base.input label="Given Name" type="text" name="givenName" wire:model="$givenName"/>
        <x-base.select wire:model="semesterId" label="Semester" :options="$semesters" />
        <x-base.select wire:model="studyModeId" label="Study Mode" :options="$studyModes" :tooltip="$studyModeTooltip"/>
        <x-base.select wire:model="specializationId" label="Specialization" :options="$specializations" optionKey="name" placeholder="-- Choose Specialization --"/>
        @if($specializationId)
            <livewire:course-selection
                key="{{ now() }}"
                :ects="$ects"
                :semesterId="(int)$semesterId"
                :studyModeId="$studyModeId"
                :specializationId="$specializationId"
                :selectedCourses="$selectedCourses"
                :coursesByCourseGroup="$coursesByCourseGroup"
            />

        <livewire:modules-outside-curriculum />
        <x-double-degree />
        <livewire:master-thesis />
        <x-optional-english />
        <x-additional-comments />
        <input type="submit" name="submit" value="Submit" class="button-primary" x-data x-on:click="window.scrollTo(0, 0)"/>
        @endif
    </form>
</x-base.card>
