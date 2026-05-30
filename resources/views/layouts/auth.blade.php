<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <!-- favicon -->
        <link rel="shortcut icon" href="{{ asset('assets/img/logo_smkn_2.png') }}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">

        <!-- Iconify -->
        <script defer src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {!! ToastMagic::styles() !!}

        @livewireStyles
    </head>
    <body class="cursor-auto overflow-hidden scroll-hidden" >
        {{ $slot }}

        @livewireScripts

        {!! ToastMagic::scripts() !!}
    </body>
</html>
