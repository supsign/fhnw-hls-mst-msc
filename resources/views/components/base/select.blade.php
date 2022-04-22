<div {{ $attributes->only('class')->merge(['class' => '']) }} >
    @php
        $optionKey = $attributes->get('optionKey');
        $placeholder = $attributes->get('placeholder')
    @endphp
    <label class="select__label">{{  $attributes->get('label') }}</label>
    <select class="select__field" name="{{ $attributes->get('name') }}" {{ $attributes->only('wire:model') }}>
        @if($placeholder)
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
</div>
