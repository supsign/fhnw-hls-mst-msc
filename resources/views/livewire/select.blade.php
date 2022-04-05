<div>
    <label class="select__label">{{$label}}</label>
    <select class="select__field" name="{{$name}}" wire:model="selected">
        @if($placeholder)<option value>{{ $placeholder }} </option>@endif
    @foreach($options as $option)
            @if($optionKey)
                <option value="{{ is_int($option) ? $option : $option->id ?? $option->value }}">{{ is_int($option) ? $option : $option->{$optionKey} ?? $option->{$optionKey}() }}</option>
            @else
                <option>{{ $option }}</option>
            @endif
        @endforeach
    </select>
    @error($name)<span class="text-red-500">{{ $message }}</span> @enderror
</div>
