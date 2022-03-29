<div {{$attributes->class(['p-2 rounded  bg-white flex flex-col', 'shadow-xl' => !$attributes['completed'], 'opacity-60' => $attributes['completed']]) }}>

    @isset($title)
        <div class="content-center p-2 border-b rounded-t text-base md:text-lg">
            {{ $title }}
        </div>
    @endisset

    <div class="p-3 text-sm md:text-base flex-grow">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t border-gray-200 pt-2">
            {{ $footer }}
        </div>
    @endisset
</div>
