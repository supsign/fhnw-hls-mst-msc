<div class="border-r flex gap-5">
    @foreach($nextSemesters AS $semester)
        <div  class="w-20">
                <input type="radio" name="{{$courseName}}" wire:model="semesterId" value="{{$semester['id']}}" key="{{$semester['id']}}">
        </div>
    @endforeach
    <div  class="w-20">
        <input type="radio" name="{{$courseName}}" wire:model="semesterId" value="99"/>
    </div>
</div>
