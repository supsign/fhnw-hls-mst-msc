<div>
    <label class="select__label">{{$label}}</label>
    <select class="select__field">
        @foreach($options as $option)
            <option class="select__dropdown-option">
                {{$option}}
            </option>

        @endforeach
    </select>
</div>