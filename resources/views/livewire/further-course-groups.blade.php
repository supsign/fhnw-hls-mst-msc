<div class="{{$class}}">
    <div class="flex">
        <div class="w-[26rem] p-1 border-b"></div>
        <div class="w-10 border-b"></div>
        <div class="flex gap-5 border-b">
            <div class="w-20 text-center">none</div>
            @foreach($nextSemesters AS $semester)
                <div class="w-20 text-center ">{{ $semester['short_name']}}</div>
            @endforeach
            <div class="w-20 text-center">later</div>
        </div>

    </div>

    @foreach($groups AS $group)
        <livewire:course-group :group="$group" :nextSemesters="$nextSemesters" further="true"/>
    @endforeach
</div>
