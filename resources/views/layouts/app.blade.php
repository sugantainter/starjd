<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'StarJD connects brands with vetted creators. Find creators, book packages, and get content that performs.')">
    <title>@yield('title', config('app.name', 'StarJD'))</title>
    {{-- Favicons & PWA (generated from logo via php scripts/generate-favicons.php) --}}
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48x48.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon-180x180.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicon-180x180.png') }}">
    <link rel="apple-touch-icon" sizes="167x167" href="{{ asset('favicon-180x180.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#e63946">
    <meta name="msapplication-TileColor" content="#e63946">
    <meta name="msapplication-config" content="{{ asset('browserconfig.xml') }}">
    <meta name="yandex-verification" content="6f4978de44202583">
    {{-- Default social preview (pages can override with @stack / @section later) --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name', 'StarJD') }}">
    <meta property="og:title" content="@yield('og_title', config('app.name', 'StarJD'))">
    <meta property="og:image" content="{{ asset('logo.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ asset('logo.png') }}">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-DWM5PFHP2J"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-DWM5PFHP2J');
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="antialiased">
    @yield('content')
    @stack('scripts')
</body>
</html>
