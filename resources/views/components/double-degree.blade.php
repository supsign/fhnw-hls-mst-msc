<div>
    <div class="mb-5 text-lg"><b>{{ $doubleDegreeTitle }}</b></div>
    <div>{!! $doubleDegreeDescription !!}</div>
    <div class="flex mt-5">
        <input {{ $attributes->only(['wire:model', 'multiple']) }} type="checkbox" name="doubleDegree" class="checkbox__field my-auto mr-3"/>
        <label class="my-auto">{!! $doubleDegreeCheckboxText !!}</label>
    </div>
</div>
