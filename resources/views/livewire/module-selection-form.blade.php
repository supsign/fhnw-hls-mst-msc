<x-base.card>
    <form wire:submit.prevent="submit" class="flex flex-col justify-center gap-5">
        @csrf
{{--         <livewire:input label="Surname" type="text" name="surname" />
        <livewire:input label="Given Name" type="text" name="givenName"/>
        <livewire:select label="Semester" name="semester" :options="$semesters" />
        <livewire:select label="Study" name="studyMode" :options="$studyModes" />
        <livewire:select label="Specialization" name="specialization" :options="$specializations" optionKey="name" placeholder="-- Choose Specialization --" />

        @if($specializationId)
            <livewire:course-selection 
                key="{{ now() }}"
                :semesterId="$semesterId"
                :studyModeId="$studyModeId"
                :specializationId="$specializationId">
            </livewire:course-selection>
        @endif --}}

        <input type="submit" name="submit" value="Submit" class="button-primary"/>
    </form>
</x-base.card>
