<span {{$attributes->except('class')}}{{ $attributes->merge(['class' => 'bg-gray-200 absolute z-10 bottom-[125%] left-0 p-2 transition-opacity rounded-md']) }}>{{ $slot }}</span>
