<div class="{{$class}}">
    <div class="flex">
            <div class="w-[26rem] p-1 border-b {{$further ? 'border-l' : '' }}"><b>{{$further ? $group['specialization']['name']: ""}}</b></div>
            <div class="w-10 border-b"></div>
            <div class="flex gap-5 border-b">
                <div class="w-20 text-center">{{!$further ? 'none' : ''}}</div>
                @foreach($nextSemesters AS $semester)
                    <div class="w-20 text-center ">{{ !$further ? $semester['short_name'] : '' }}</div>
                @endforeach
                <div class="w-20 text-center {{$further ? 'border-r': ''}}">{{!$further ? 'later' : ''}}</div>
            </div>
    </div>
    @foreach($group['courses'] as $course)
        <livewire:course
                :internalName="$group['internal_name']"
                :course="$course" :selectedSemester="$selectedCourses[$course['id']] ?? null"
                :nextSemesters="$nextSemesters"
                key="{{ $course['id'] }}"
        />
    @endforeach
</div>
