<div>
    <label class="select__label">{{$attributes->get('label')}}</label>
    <select class="select__field">
        @foreach($attributes->get('options') as $option)
            <option class="select__dropdown-option">
                {{$option}}
            </option>

        @endforeach
    </select>
</div>