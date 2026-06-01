<!DOCTYPE html>
@php
    $siteSettings = $siteSettings ?? [];
    $siteLogo = \App\Models\Setting::resolveAssetUrl($siteSettings['logo'] ?? null, asset('assets/img/logo_smkn_2.png'));
    $siteName = $siteSettings['nama_website'] ?? config('app.name');
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? $siteName }}</title>

        <!-- favicon -->
        <link rel="shortcut icon" href="{{ $siteLogo }}" type="image/x-icon">

        <!-- Fonts loaded via app.css @import -->

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
