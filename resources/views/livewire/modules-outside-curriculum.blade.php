<div>
    <div class="mb-5 text-lg"><b>{{ $modulesOutsideTitle }}</b></div>
    <div>{!! $modulesOutsideDescription !!}</div>
    @foreach($outsideModules AS $key => $module)
    <div class="flex my-5 gap-5">
        <div><label>Module Title</label><input type="text" name="title" wire:model.lazy="outsideModules.{{$key}}.title" class="input__field"/></div>
        <div><label>ECTS</label><input type="text" name="ects" class="input__field" wire:model.lazy="outsideModules.{{$key}}.ects"  /></div>
        <div><label>University</label><input type="text" name="university" class="input__field" wire:model.lazy="outsideModules.{{$key}}.university" /></div>
    </div>
    @endforeach
    <div class="flex my-5 gap-5">
        <div><label>Module Title</label><input type="text" name="title" wire:model.lazy="outsideModules.{{count($outsideModules)}}.title" class="input__field" /></div>
        <div><label>ECTS</label><input type="text" name="ects" class="input__field" wire:model.lazy="outsideModules.{{count($outsideModules)}}.ects"  /></div>
        <div><label>University</label><input type="text" name="university" class="input__field" wire:model.lazy="outsideModules.{{count($outsideModules)}}.university" /></div>
    </div>
</div>
