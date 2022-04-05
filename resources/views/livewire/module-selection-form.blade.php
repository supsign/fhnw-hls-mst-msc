<x-base.card>
    @if ($errors->count())
        @dump($errors)
    @endif
    <form wire:submit.prevent="submit" class="flex flex-col justify-center gap-5">
        @csrf
        <livewire:input label="Surname" type="text" name="surname" />
        <livewire:input label="Given Name" type="text" name="givenName"/>
        <livewire:select label="Semester" name="semester" :options="$semesters" />
        <livewire:select label="Study Mode" name="semester" :options="$studyModes" optionKey="label" />
        {{-- <x-base.select label="Study Mode" name="mode" :options="$studyModes" optionKey="label" /> --}}
        <livewire:select label="Specialization" name="specialization" :options="$specializations" optionKey="name" placeholder="-- Choose Specialization --" />
        @if($specialization)
            <livewire:course-selection></livewire:course-selection>
        @endif
        <input type="submit" name="submit" class="button-primary" />

    </form>
</x-base.card>
