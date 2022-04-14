<div>
    @php
        $optionKey = $attributes->get('optionKey');
        $placeholder = $attributes->get('placeholder')
    @endphp
    {{$disablePlaceholder}}
    <label class="select__label">{{ $attributes->get('label') }}</label>
    <select class="select__field" name="{{ $attributes->get('name') }}" >
        @if($placeholder && !$disablePlaceholder)
            <option value>{{ $placeholder }}</option>
        @endif
        @foreach($attributes->get('options') as $key => $value)
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
