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
        @if($coreCompetenceCourses)
            <livewire:select label="Core Course Competence" name="coreCompetenceCourse" :options="$coreCompetenceCourses" optionKey="name" placeholder="-- Choose Core Competence Course --" />
        @endif
        @if($clusterSpecificCourses)
            <livewire:select label="Cluster Course Competence" name="coreCompetenceCourse" :options="$clusterSpecificCourses" optionKey="name" placeholder="-- Choose Core Competence Course --" />
        @endif
        <input type="submit" name="submit" class="button-primary" />
    </form>
</x-base.card>
