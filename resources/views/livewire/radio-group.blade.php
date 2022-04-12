<div class="border-r flex gap-5">
    @foreach($nextSemesters AS $semester)
                <input type="radio" name="{{$courseName}}" class="radio" wire:model="{{$semester['name']}}" value="{{$semester['name']}}"/>
    @endforeach
        <input type="radio" name="{{$courseName}}" class="radio" wire:model="later" value="later"/>
</div>
