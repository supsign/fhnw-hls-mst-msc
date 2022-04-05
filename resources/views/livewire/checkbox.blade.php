<div>
    <input type="checkbox" name="{{$name}}" class="checkbox" wire:model="value"/>
    @error($name) <span class="text-red-500">{{ $message }}</span> @enderror
</div>
