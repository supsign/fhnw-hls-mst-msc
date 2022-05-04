<div>
    @if($title)
        <div class="mb-5 text-lg"><b>{{ $title }}</b></div>
    @endif

    @if($description)
        <div class="mb-5">{!! $description !!}</div>
    @endif

    <div class="flex">
        <div class="w-[26rem] p-1 border-b {{$further ? 'border-x' : ''}}">
            @if($further)
                <b>{{ $courseGroup['name'] }}</b>
            @endif
        </div>

        @if(!$further)
            <div class="w-10 border-b"></div>
        @endif

        <div class="flex gap-5 border-b {{ $further ? 'border-r' : '' }}">
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

    <div class="max-w-min">
        @foreach($courses as $course)
            <livewire:course
                :course="$course"
                :nextSemesters="$nextSemesters"
                :selectedCourses="$selectedCourses"
                key="{{ $course['id'] }}"
                :further="$further"
                :showType="$showType"
                :course-group="$courseGroup"
            />
        @endforeach

        @if(!$further)
            <div class="{{ count($this->selectedCourses) < $this->courseGroup['required_courses_count'] ? 'text-red-500' : 'text-green-500' }} text-right">
                Selected: {{count($this->selectedCourses)}} / {{$this->courseGroup['required_courses_count']}}
            </div>
        @endif
    </div>
</div>
