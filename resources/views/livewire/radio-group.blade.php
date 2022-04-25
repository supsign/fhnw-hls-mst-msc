<div class="border-r border-b flex gap-5" wire:ignore>

    {{-- @dump($selectedSemester) --}}

    @foreach($selectableSemesters AS $semesterId)
        <div class="w-20 text-center">
            @if($semesterId)
                <input 
                    wire:model="selectedSemester"
                    type="radio" 
                    name="{{ uniqid().$courseId }}"
                    value="{{ $semesterId }}" 
                    id="{{ $semesterId }}"
                    @if($selectedSemester === $semesterId) 
                        selected 
                    @endif
                />
            @endif
        </div>
    @endforeach
</div>