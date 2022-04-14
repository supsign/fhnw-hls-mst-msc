<div class="my-5">
    <div class="flex border-b">
        <div class="w-[26rem] p-1"><b>{{$group['name']}}</b></div>
        <div class="w-10"></div>
        <div class="flex gap-5">
            <div class="w-20 text-center">none</div>
            @foreach($nextSemesters AS $semester)
                <div class="w-20 text-center">{{ $semester['name'] }}</div>
            @endforeach
                <div class="w-20 text-center">later</div>
        </div>
    </div>
    @foreach($group['courses'] as $course)
        <livewire:course :internalName="$group['internal_name']" :course="$course" :selectedSemester="$selectedCourses[$course['id']] ?? 0" :nextSemesters="$nextSemesters" key="{{ $course['id'] }}"/>
    @endforeach
</div>
