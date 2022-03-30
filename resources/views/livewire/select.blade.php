<div>
    <label class="select__label">{{$label}}</label>
    @dd($test)
    <select class="select__field" >
        @foreach($options as $option)
            @if($optionKey)
                <option>{{$option[$optionKey]}}</option>
            @else
                <option>{{$option}}</option>
            @endif
        @endforeach
    </select>
</div>