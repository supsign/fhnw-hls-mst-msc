<div class="mt-10" x-data="{hide: true}">
        <template x-if="!hide">
            <div>{{$input}}</div>
        </template>
    <button @mousedown="hide = false" @mouseup="hide = true" class="border bg-gray-300 shadow-2xl p2">El Button</button>
    <div class="flex flex-col">
    <input wire:model="input" type="text">
        @error('input') <span class="text-red-600">{{ $message }}</span> @enderror
        <div wire:dirty wire:target="input">
            typing...
        </div>
    </div>

</div>
<script>

</script>