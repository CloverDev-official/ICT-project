<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('ICT') }}</title>

        <!-- favicon -->
        <link rel="shortcut icon" href="{{ asset('assets/img/logo_smkn_2.png') }}" type="image/x-icon">

        <!-- Iconify -->
        <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>

        <!-- AOS JS -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {!! ToastMagic::styles() !!}

        @livewireStyles
    </head>
    <body class="cursor-auto" >
        {{ $slot }}

        @livewireScripts
        <script>
            AOS.init();
        </script>

        {!! ToastMagic::scripts() !!}
    </body>
</html>