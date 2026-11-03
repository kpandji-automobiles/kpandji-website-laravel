<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@hasSection('title')@yield('title') — KPANDJI @else KPANDJI — Constructeur automobile @endif</title>
@hasSection('meta_description')<meta name="description" content="@yield('meta_description')">@endif
@stack('meta')
<link rel="icon" href="{{ asset('favicon.ico') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap">
<link rel="stylesheet" href="{{ asset('css/kpandji/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('css/kpandji/components.css') }}">
<style>body{margin:0;background:var(--surface);color:var(--ink);font-family:var(--font-sans)}[x-cloak]{display:none!important}</style>
@stack('head')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
</head>
<body class="kp">
@yield('header')
<main id="contenu">
@yield('content')
</main>
@yield('footer')
@stack('scripts')
</body>
</html>
