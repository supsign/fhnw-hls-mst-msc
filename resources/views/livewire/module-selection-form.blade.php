<x-base.card>
    @dump($selectedCourses)
    <form wire:submit.prevent="submit" class="flex flex-col justify-center gap-5">
        @csrf
        <livewire:input label="Surname" type="text" name="surname" />
        <livewire:input label="Given Name" type="text" name="givenName"/>

        <select wire:model="semesterId">
            @foreach($semesters AS $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>

        <select wire:model="studyModeId">
            @foreach($studyModes AS $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>

        <select wire:model="specializationId">
            @foreach($specializations AS $specialization)
                <option value="{{ $specialization['id'] }}">{{ $specialization['name'] }}</option>
            @endforeach
        </select>

        @if($specializationId)
            <livewire:course-selection
                key="{{ now() }}"
                :semesterId="(int)$semesterId"
                :studyModeId="$studyModeId"
                :specializationId="$specializationId"
                :selectedCourses="$selectedCourses"
            />
        @endif

        <input type="submit" name="submit" value="Submit" class="button-primary"/>
    </form>
</x-base.card>
