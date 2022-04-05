<div>
    @php
        $optionKey = $attributes->get('optionKey');
    @endphp

    <label class="select__label">{{ $attributes->get('label') }}</label>
    <select class="select__field" name="{{ $attributes->get('name') }}" >
        @foreach($attributes->get('options') as $option)
            <option class="select__dropdown-option">
                @if ($optionKey)
                    {{ is_array($option) ? $option[$optionKey] : $option->{$optionKey} ?? $option->{$optionKey}() }}
                @else
                    {{ $option }}
                @endif
            </option>
        @endforeach
    </select>
</div>