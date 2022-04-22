<div class="{{$class}}">
{{--     @if(!$further)
        <div class="mb-3"><b>{{ $title }}</b></div>
    @endif --}}

{{--     @if($description)
        <div>{{ $description }}</div>
    @endif --}}
    
    <div class="flex">
        {{-- <div class="w-[26rem] p-1 border-b {{$further ? 'border-l' : '' }}"><b>{{ $further ? $group['specialization']['name']: '' }}</b></div> --}}
        <div class="w-10 border-b"></div>
        <div class="flex gap-5 border-b">
            {{-- <div class="w-20 text-center">{{ !$further ? 'none' : '' }}</div> --}}

{{--             @foreach($nextSemesters AS $semester)
                <div class="w-20 text-center ">{{ !$further ? $semester['short_name'] : '' }}</div>
            @endforeach --}}

            {{-- <div class="w-20 text-center {{ $further ? 'border-r': '' }}">{{ !$further ? 'later' : '' }}</div> --}}
        </div>
    </div>

{{--     @php
        $courses = $further ? $group['courses_filtered'] : $group['courses'];
    @endphp

    @foreach($courses as $course)
        <livewire:course
            :courseGroupTypeShortName="$group['course_group_type_short_name']"
            :course="$course"
            :selectedSemester="$selectedCourses[$course['id']] ?? null"
            :nextSemesters="$nextSemesters"
            :groupId="$group['id']"
            key="{{ $course['id'] }}"
        />
    @endforeach --}}
</div>
