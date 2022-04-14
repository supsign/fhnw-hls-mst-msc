<div class="my-5">
    <div class="flex gap-5">
        <div class="w-96"><b>{{$group['name']}}</b></div>
        <div class="w-6"></div>
        @foreach($nextSemesters AS $semester)
            <div class="w-20">{{ $semester['name'] }}</div>
        @endforeach
        <div>later</div>
    </div>
    @foreach($group['courses'] as $course)
        <livewire:course :internalName="$group['internal_name']" :course="$course" :selectedSemester="$selectedCourses[$course['id']] ?? 0" :nextSemesters="$nextSemesters" key="{{ $course['id'] }}"/>
    @endforeach
</div>
