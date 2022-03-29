<a {{ $attributes->merge(['class' => 'link']) }}>
    @isset($attributes['icon'])
        <i aria-hidden="true" class="mr-1 {{ $attributes['icon'] }}"></i>
    @endisset
    {{ $slot }}
</a>
