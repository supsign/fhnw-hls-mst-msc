<div class="mt-10" x-data="{hide: true}">
    @if($typing)
        <div>typing...</div>
    @else
        <template x-if="!hide">
        <div >{{$input}}</div>
        </template>
    @endif

    <input wire:model="input" wire:keydown="changeType({{true}})" wire:keyup.debounce="changeType" type="text">
    <button @mousedown="hide = false" @mouseup="hide = true">Show Message</button>
</div>
