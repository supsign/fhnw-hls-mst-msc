<div class="flex flex-col">
    <div class="flex gap-5">
        <div class="w-80"></div>
        <div class="w-6"></div>
        @foreach($nextSemesters AS $semester)
            <div class="w-20">{{$semester['name']}}</div>
        @endforeach
            <div>later</div>
    </div>
    <div class="flex flex-col">
        @foreach($coreCompetenceCourseGroup['courses'] as $course)
        <livewire:course :internalName="$coreCompetenceCourseGroup['internal_name']" :course="$course" :nextSemesters="$nextSemesters"/>
        @endforeach
      </div>
    </div>
</div>
