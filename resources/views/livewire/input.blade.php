<div>
    <label class="input__label">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" class="input__field" wire:model="value"/>
    
    @error($name)
        <span class="text-red-500">{{ $message }}</span>
    @enderror
</div>
