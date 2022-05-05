<div {{ $attributes->only('class')->merge(['class' => 'relative']) }} x-data="{ hover: false }" >
    @php
        $optionKey = $attributes->get('optionKey');
        $placeholder = $attributes->get('placeholder');
        $tooltip = $attributes->get('tooltip');
    @endphp

    <label class="select__label" x-on:mouseover="hover = true" x-on:mouseout="hover = false">
        @if($attributes->get('bold'))<b>{{ $attributes->get('label') }}</b>@else{{ $attributes->get('label') }}@endif
    </label>

    @if($tooltip)
        <x-tooltip x-show="hover" class="w-[22rem]">{{ $tooltip}}</x-tooltip>
    @endif

    <select class="select__field" name="{{ $attributes->get('name') }}" {{ $attributes->only(['wire:model', 'multiple']) }}>
        @if($placeholder)
            <option value="{{ 0 }}">{{ $placeholder }}</option>
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
