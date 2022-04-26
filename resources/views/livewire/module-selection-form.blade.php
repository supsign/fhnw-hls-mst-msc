<x-base.card>
    <form wire:submit.prevent="submit" class="flex flex-col justify-center gap-5">
        @csrf
        @dump($this->getErrorBag())
        <x-base.input label="Surname" type="text" name="surname" wire:model="$surname" />
        <x-base.input label="Given Name" type="text" name="givenName" wire:model="$givenName"/>
        <x-base.select wire:model="semesterId" label="Semester" :options="$semesters" />
        <x-base.select wire:model="studyModeId" label="Study Mode" :options="$studyModes" />
        <x-base.select wire:model="specializationId" label="Specialization" :options="$specializations" optionKey="name" placeholder="-- Choose Specialization --"/>
        @if($specializationId)
            <livewire:course-selection
                key="{{ now() }}"
                :ects="$ects"
                :semesterId="(int)$semesterId"
                :studyModeId="$studyModeId"
                :specializationId="$specializationId"
                :selectedCourses="$selectedCourses"
            />

        <livewire:modules-outside-curriculum />
        <x-double-degree />
        <livewire:master-thesis />
        <x-optional-english />
        <x-additional-comments />
        <input type="submit" name="submit" value="Submit" class="button-primary"/>
        @endif
    </form>
</x-base.card>
