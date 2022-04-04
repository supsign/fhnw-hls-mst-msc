<div>
    <label class="select__label">{{ $label }}</label>

    <select class="select__field" name="{{ $name }}" wire:model="selected">
        @foreach($options as $option)
            @if($optionKey)
                <option value="{{ $option->id }}">{{ $option[$optionKey] }}</option>
            @else
                <option>{{ $option }}</option>
            @endif
        @endforeach
    </select>
</div>