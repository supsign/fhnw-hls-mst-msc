<div>
    <input type="radio" name="{{$name}}" class="radio" wire:model="value"/>
    @error($name) <span class="text-red-500">{{ $message }}</span> @enderror
</div>
