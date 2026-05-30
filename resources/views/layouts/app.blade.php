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

        {!! ToastMagic::scripts() !!}
    </body>
</html>
