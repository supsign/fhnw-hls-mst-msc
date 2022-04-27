<div class="">
    @if(!$further)
        <div class="mb-5 text-lg"><b>{{ $title }}</b></div>
    @endif

    @if($description)
        <div class="mb-5">{!! $description !!}</div>
    @endif
    
    <div class="flex">
        <div class="w-[26rem] p-1 border-b {{$further ? 'border-l' : '' }}"><b>{{ $further ? $courseGroup['specialization']['name']: '' }}</b></div>
        <div class="w-10 border-b"></div>
        <div class="flex gap-5 border-b">
            <div class="w-20 text-center">{{ !$further ? 'none' : '' }}</div>
            @foreach($nextSemesters AS $semester)
                <div class="relative"  x-data="{ hover: false }">
                    <div class="w-20 text-center" x-on:mouseover="hover = true" x-on:mouseout="hover = false">{{ !$further ? $semester['short_name'] : '' }}</div>
                    <x-tooltip x-show="hover" class="w-80">{{ $semester['tooltip'] }}</x-tooltip>
                </div>
            @endforeach
            <div class="w-20 text-center {{ $further ? 'border-r': '' }}">{{ !$further ? 'later' : '' }}</div>
        </div>
    </div>
    @php
        $courses = $further ? $courseGroup['courses_filtered'] : $courseGroup['courses'];
    @endphp
    <div class="max-w-min">
        @foreach($courses as $course)
            <livewire:course
                :courseGroupTypeShortName="$courseGroup['course_group_type_short_name']"
                :courseGroupTypeTooltip="$courseGroup['course_group_type_tooltip']"
                :course="$course"
                :courseGroupId="$courseGroup['id']"
                :further="$further"
                :nextSemesters="$nextSemesters"
                :selectedCourses="$selectedCourses"
                key="{{ $course['id'] }}"
            />
        @endforeach
        @if(!$further)
            <div class="{{ count($this->selectedCourses) < $this->courseGroup['required_courses_count'] ? 'text-red-500' : 'text-green-500' }} text-right">Selected: {{count($this->selectedCourses)}} / {{$this->courseGroup['required_courses_count']}}</div>
        @endif
    </div>
</div>
