<div>
    @foreach($semesters AS $semester)
        <input type="radio" name="{{$name}}" class="radio" wire:model="{{$courseName}}" value="{{$semester}}"/>
    @endforeach
</div>
