<div class="border-r flex gap-5">
    @foreach($nextSemesters AS $semester)
        <div  class="w-20">
            <input type="radio" name="{{ $courseId}}" wire:model="semesterId" value="{{ $semester['id'] }}" id="{{ $semester['id'] }}">
        </div>
    @endforeach
    <div  class="w-20">
        <input type="radio" name="{{ $courseId }}" wire:model="semesterId" value="99"/>
    </div>
</div>
