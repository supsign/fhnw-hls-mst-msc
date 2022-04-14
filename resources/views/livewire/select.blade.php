<div>
    <label class="select__label">{{$label}}</label>
    <select class="select__field" name="{{$name}}" wire:model="selected">
        @if($placeholder && !$disablePlaceholder)
            <option value>{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $key => $value)
            @if(is_string($value))
                <option value="{{ $key }}" >{{ $value }}</option>
            @else
                <option value="{{ $value['id'] }}">{{ $value[$optionKey] }}</option>
            @endif
        @endforeach
    </select>

    @error($name)
        <span class="text-red-500">{{ $message }}</span>
    @enderror
</div>
