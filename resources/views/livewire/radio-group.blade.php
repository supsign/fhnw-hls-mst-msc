<div class="border-r flex gap-5">
    @foreach($selectableSemesters AS $semester)
        <div class="w-20 text-center">
            @if($semester)
                <input type="radio" name="{{ $courseId}}" wire:model="semesterId" value="{{ $semester}}" id="{{ $semester }}">
            @endif
        </div>
    @endforeach
    <div class="w-20 text-center">
        <input type="radio" name="{{ $courseId }}" wire:model="semesterId" value="later"/>
    </div>
</div>
