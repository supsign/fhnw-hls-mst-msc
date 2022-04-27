@php
    $errors = $attributes->get('errors');
@endphp
<div class="">
    <div class="text-2xl">Error Messages</div>
    <ul class="text-red-500 list-disc list-inside">
        @foreach($errors AS $key => $value)
           @foreach($value AS $message)
               <li>{{$message}}</li>
            @endforeach
        @endforeach
    </ul>
</div>
