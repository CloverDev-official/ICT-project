<link rel="manifest" href="{{ route('pwa.manifest', [], false) }}">
<meta name="theme-color" content="#ffffff">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ $siteSettings['nama_website'] ?? config('app.name') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ request()->getBaseUrl() }}/assets/img/pwa/apple-touch-icon.png">
