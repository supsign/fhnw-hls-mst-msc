<div>
    <div class="mb-5 text-lg"><b>{{ $doubleDegreeTitle }}</b></div>
    <div>{!! $doubleDegreeDescription !!}</div>
    <div class="flex">  <label class="my-auto mr-3">Are you interested in the Double-Degree option:</label>
    
    <input {{ $attributes->only(['wire:model', 'multiple']) }} type="checkbox" name="doubleDegree" class="checkbox__field my-auto"/></div>
</div>
