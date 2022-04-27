<x-base.card>
    <form wire:submit.prevent="submit" class="flex flex-col justify-center gap-5">
        @csrf
        <x-base.input label="Surname" type="text" name="surname" />
        <x-base.input label="Given Name" type="text" name="givenName"/>
        <x-base.select wire:model="semesterId" label="Semester" :options="$semesters" />
        <x-base.select wire:model="studyModeId" label="Study Mode" :options="$studyModes" :tooltip="$studyModeTooltip"/>
        <x-base.select wire:model="specializationId" label="Specialization" :options="$specializations" optionKey="name" placeholder="-- Choose Specialization --"/>
        @if($specializationId)
            <livewire:course-selection
                key="{{ now() }}"
                :nextSemesters="$nextSemesters"
                :semesterId="(int)$semesterId"
                :studyModeId="$studyModeId"
                :specializationId="$specializationId"
                :selectedCourses="$selectedCourses"
            />

        <livewire:modules-outside-curriculum />
        <x-double-degree />
        <livewire:master-thesis />
        <x-optional-english 
            :nextSemesters="$nextSemesters"
        />
        <x-additional-comments />
        <input type="submit" name="submit" value="Submit" class="button-primary"/>
        @endif
    </form>
</x-base.card>
