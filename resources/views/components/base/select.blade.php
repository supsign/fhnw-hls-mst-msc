<div>
    <label class="select__label">{{$attributes->get('label')}}</label>
    <select class="select__field" name="{{$attributes->get('name')}}" >
        @foreach($attributes->get('options') as $option)
            <option class="select__dropdown-option">
                {{$option}}
            </option>

        @endforeach
    </select>
</div>