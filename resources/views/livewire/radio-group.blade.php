<div class="border-r border-b flex gap-5">
    <div class="w-20 text-center">
        <input type="radio" name="{{ $courseGroupId }}"/>
    </div>
    
    @foreach($selectableSemesters AS $semester)
        <div class="w-20 text-center">
            @if($semester)
                <input type="radio" name="{{ $courseGroupId }}" value="{{ $semester }}" id="{{ $semester }}"/>
            @endif
        </div>
    @endforeach
    
    <div class="w-20 text-center">
        <input type="radio" name="{{ $courseGroupId }}" value="later"/>
    </div>
</div>