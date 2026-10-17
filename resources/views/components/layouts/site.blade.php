@props(['title'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

        <title>{{ $title }}</title>
        <meta name="theme-color" content="#fff8ef">
        {{ $head ?? '' }}

        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}">
        <link rel="preload" as="image" href="{{ asset('images/logo-mark.webp') }}" type="image/webp">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen overflow-x-clip">
        {{ $slot }}
    </body>
</html>
