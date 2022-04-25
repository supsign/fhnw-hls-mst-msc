<div>
    <div class="mb-5 text-lg"><b>{{ $modulesOutsideTitle }}</b></div>
    <div>{!! $modulesOutsideDescription !!}</div>
    <div><div>Module Title</div><div>ECTS</div><div>University</div></div>
    <div><input type="text" name="title" wire:model="" /><input type="text" name="ects" /><input type="text" name="university" /><button wire:click="saveInput"></button></div>
</div>
