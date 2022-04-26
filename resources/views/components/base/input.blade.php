<div>
    <label class="input__label">{{ $attributes->get('label')}}</label>
    <input type="{{ $type }}" name="{{ $name }}" class="input__field"  {{ $attributes->only('wire:model') }} />
</div>
