<div class="text-xl mt-10">Counter</div>
<div class="flex justify-between text-xl">
    <button wire:click="increment" class="p-2 border">+</button>
    <h1>{{ $count }}</h1>
    <button wire:click="decrement" class="p-2 border">-</button>
</div>