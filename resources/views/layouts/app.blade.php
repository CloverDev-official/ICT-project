<!DOCTYPE html>
@php
    $siteSettings = $siteSettings ?? [];
    $siteLogo = \App\Models\Setting::resolveAssetUrl($siteSettings['logo'] ?? null, asset('assets/img/logo_smkn_2.png'));
    $siteName = $siteSettings['nama_website'] ?? config('app.name');
    $browserRefreshVersion = $siteSettings['system.browser_refresh_version'] ?? '';
@endphp

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? $siteName }}</title>

        <!-- favicon -->
        <link rel="shortcut icon" href="{{ $siteLogo }}" type="image/x-icon">

        <!-- Fonts loaded via app.css @import -->

        <!-- Register Iconify before the sidebar is parsed to prevent late icon pop-in. -->
        <link rel="preconnect" href="https://code.iconify.design" crossorigin>
        <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
        
        {!! ToastMagic::styles() !!}

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>

    <body class="cursor-auto bg-blue-50 overflow-hidden" x-data="{ openside: false }">
        <main class="h-screen flex justify-start" >
            <livewire:components.sidebar :key="'layout-sidebar'" />
            <div class="flex-1 flex flex-col min-h-0 overflow-y-auto" >
                <div class="p-4">
                    <livewire:components.header :key="'layout-header'" />
                    {{ $slot }}
                </div>
            </div>
        </main>
        @if (app()->environment('local'))
            <livewire:components.test-time :key="'layout-test-time-app'" />
        @endif

        @livewireScripts

        {!! ToastMagic::scripts() !!}

        @auth
            <x-browser-refresh-listener :version="$browserRefreshVersion" />
        @endauth
    </body>
</html>
