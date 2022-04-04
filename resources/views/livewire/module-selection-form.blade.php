<x-base.card>
    @if ($errors->count())
        @dump($errors)
    @endif
    <form wire:submit.prevent="submit" class="flex flex-col justify-center gap-5">
        @csrf
        <livewire:input label="Surname" type="text" name="surname" />
        <livewire:input label="Given Name" type="text" name="givenName"/>
        <livewire:select label="Semester" name="semester" :options="$semesters" />
        <x-base.select label="Study Mode" name="mode" :options="$studyModes" />
        <livewire:select label="Specialization" name="specialization" :options="$specializations" optionKey="name" placeholder="-- Choose Specialization --" />

        <input type="submit" name="submit" class="button-primary" />
    </form>
</x-base.card>
