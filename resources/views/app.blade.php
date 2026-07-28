<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Coxinhas da Lily') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('img/logolilyconfondo.png') }}">

        <!-- Meta tags para compartir en redes sociales -->
        <meta name="description" content="Coxinhas, churros, empanadas y salgados brasileños artesanales. Pedí por WhatsApp y retirá en Posadas, Misiones.">
        <meta property="og:title" content="Coxinhas da Lily">
        <meta property="og:description" content="Coxinhas, churros, empanadas y salgados brasileños artesanales. Pedí por WhatsApp.">
        <meta property="og:image" content="{{ asset('img/logolilyconfondo.png') }}">
        <meta property="og:type" content="website">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Coxinhas da Lily">
        <meta name="twitter:description" content="Coxinhas, churros, empanadas y salgados brasileños artesanales.">
        <meta name="twitter:image" content="{{ asset('img/logolilyconfondo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fredoka:400,500,600,700|inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Google Analytics -->
        @if(env('GOOGLE_ANALYTICS_ID'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GOOGLE_ANALYTICS_ID') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ env('GOOGLE_ANALYTICS_ID') }}');
        </script>
        @endif

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
