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
    <body class="cursor-auto flex justify-start h-screen bg-blue-50 overflow-hidden">
        <aside class="hidden md:block w-64 bg-blue-deep p-4 rounded-tr-4xl h-screen overflow-y-auto shadow-[6px_0_15px_rgba(0,0,0,0.1)]" >
            <div class="flex items-center justify-center gap-4 pt-4">
                <img src="{{ asset('assets/img/logo_smkn_2.png')}}" class="w-10" alt="">
                <h1 class="bg-blue- text-start text-white text-shadow-2xs text-sm font-semibold uppercase">
                    Operator Petugas Absensi
                </h1>
            </div>
            <hr class="text-white mt-5" >
            <ul class="mt-5 flex flex-col gap-2" >
                <li>
                    <x-nav-link href="{{ route('dashboard') }}" icon="dashboard">
                        Dashboard
                    </x-nav-link>
                </li>
                <li>
                    <x-nav-link href="{{ route('absensi-murid') }}" icon="absenMurid">
                        absensi murid
                    </x-nav-link>
                </li>
                <li>
                    <x-nav-link href="{{ route('absensi-guru') }}" icon="absenGuru">
                        absensi guru
                    </x-nav-link>
                </li>
                <li>
                    <x-nav-link href="{{ route('data-murid') }}" icon="dataMurid">
                        data murid
                    </x-nav-link>
                </li>
                <li>
                    <x-nav-link href="{{ route('data-guru') }}" icon="dataGuru">
                        data guru
                    </x-nav-link>
                </li>
                <li>
                    <x-nav-link href="{{ route('data-kelas-jurusan') }}" icon="dataKelasJurusan">
                        data kelas & jurusan
                    </x-nav-link>
                </li>
            </ul>
        </aside>
        <main class="flex-1 flex flex-col min-h-0 p-4 overflow-y-auto" >
            <livewire:components.header/>
            {{ $slot }}
        </main>

        @livewireScripts
        <script>
            AOS.init();
        </script>

        {!! ToastMagic::scripts() !!}
    </body>
</html>