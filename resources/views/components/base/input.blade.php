<div>
    <label class="input__label">{{$attributes->get('label')}}</label>
    <input type="{{$attributes->get('type')}}" name="{{$attributes->get('name')}}" class="input__field" />
    @error($attributes->get('name')) <span class="text-red-500">{{ $message }}</span> @enderror
</div>
