<x-base.card>
    {{$test}}
    <form method="POST" action="{{ route('admin.config.post') }}" enctype="multipart/form-data"  class="flex flex-col justify-center gap-5">
        @csrf
        <x-base.input label="Surname" type="text" name="surname"/>
        <x-base.input label="Given Name" type="text" name="given_name" />
        <livewire:select label="Start" name="start" :options="$semesters"/>
        <x-base.select label="Study Mode" name="mode" :options="$studyModes" />
        <livewire:select label="Specialization" name="specialization" :options="$specializations" optionKey="name"/>

        <input type="submit" name="submit" class="button-primary">
    </form>
</x-base.card>