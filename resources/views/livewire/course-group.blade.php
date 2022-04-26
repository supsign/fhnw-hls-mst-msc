<div class="">
    @if($title)
        <div class="mb-5 text-lg"><b>{{ $title }}</b></div>
    @endif

    @if($description)
        <div class="mb-5">{!! $description !!}</div>
    @endif
    
    <div class="flex">
        <div class="w-[26rem] p-1 border-b"><b>{{ 'some name' }}</b></div>
        <div class="w-10 border-b"></div>
        <div class="flex gap-5 border-b">
            <div class="w-20 text-center">{{ 'none' }}</div>

            @foreach($nextSemesters AS $semester)
                <div class="w-20 text-center ">{{ $semester['short_name'] }}</div>
            @endforeach

            <div class="w-20 text-center ">{{ 'later' }}</div>
        </div>
    </div>

    @php
        $courses = $courseGroup['courses'];
    @endphp
    <div class="max-w-min">
        @foreach($courses as $course)
            <livewire:course
                :courseGroupTypeShortName="$courseGroup['course_group_type_short_name'] ?? null"
                :course="$course"
                :courseGroupId="$courseGroup['id']"
                :nextSemesters="$nextSemesters"
                :selectedCourses="$selectedCourses"
                key="{{ $course['id'] }}"
            />
        @endforeach
        <div class="{{ count($this->selectedCourses) < $this->courseGroup['required_courses_count'] ? 'text-red-500' : 'text-green-500' }} text-right">Selected: {{count($this->selectedCourses)}} / {{$this->courseGroup['required_courses_count']}}</div>
    </div>
</div>
