<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
@hasSection('seo_title')
<title>@yield('seo_title')</title>
@else
<title>@hasSection('title')@yield('title') — KPANDJI @else KPANDJI — Constructeur automobile @endif</title>
@endif
@hasSection('meta_description')<meta name="description" content="@yield('meta_description')">@endif
@stack('meta')
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="16x16 32x32 48x48">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-icon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap">
<link rel="stylesheet" href="{{ asset('css/kpandji/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('css/kpandji/components.css') }}">
<link rel="stylesheet" href="{{ asset('css/kpandji/site.css') }}">
<style>body{margin:0;background:var(--surface);color:var(--ink);font-family:var(--font-sans)}[x-cloak]{display:none!important}</style>
@stack('head')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
</head>
<body class="kp">
<a class="kp-skip" href="#contenu">Aller au contenu</a>
@hasSection('header')
@yield('header')
@else
@include('site.header')
@endif
<main id="contenu">
@yield('content')
</main>
@hasSection('footer')
@yield('footer')
@else
@include('site.footer')
@endif
@include('site.ad-popup')
@stack('scripts')
</body>
</html>
