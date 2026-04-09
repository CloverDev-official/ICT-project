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

        <!-- Alpine JS -->
        <script src="//unpkg.com/alpinejs" defer></script>

        <!-- AOS JS -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        
        {!! ToastMagic::styles() !!}

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="cursor-auto bg-blue-50 overflow-hidden" x-data="{openside: false}" x-init="$watch('open', value => sidebarOpen = value)" >
        <main class="h-screen flex justify-start" >
            <livewire:components.sidebar/>
            <div class="flex-1 flex flex-col min-h-0 overflow-y-auto" >
                <div class="p-4">
                    <livewire:components.header/>
                    {{ $slot }}
                </div>
            </div>
        </main>
        @livewireScripts
        <script>
            AOS.init();
        </script>

        {!! ToastMagic::scripts() !!}
    </body>
</html>