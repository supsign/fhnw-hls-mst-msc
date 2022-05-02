<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="{{ isset($robots) ? $robots : 'noindex,nofollow' }}"/>
    <meta name="description" content="{{ isset($description) ? $description : '' }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <title>{{ str_replace('<br>', ',', $title) }} | HLS MST MSc</title>

    <script src="{{ asset('js/app.js') }}" defer></script>

    <link rel="alternate" hreflang="x-default" href="@php echo url()->full() @endphp"/>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/fontawesome-pro/css/all.min.css') }}">
    @livewireStyles


</head>

<body class="font-body">
@include('sweetalert::alert')
<div id="app" class="w-full">
    <div class="flex flex-col h-screen relative">
        <x-layout.top/>
        <x-layout.header/>
        <div id="main" class="flex-grow mb-4 bg-gray-200">
            {{ $slot }}
        </div>
        <x-layout.footer/>
        <x-layout.bottom/>
    </div>
</div>
@livewireScripts

</body>
</html>
