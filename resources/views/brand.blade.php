{{-- Kpandji Automobiles — port de app/kpandji-automobiles/page.tsx + components/kp/BrandPageContent.tsx (+ Reveal.tsx, kp-scroll-dot de globals.css).
     Contenu : Admin › Kpandji Automobiles (/admin/kpandji-automobiles) — clés CMS « brand.* » --}}
@extends('layouts.app')
@php($en = app()->getLocale() === 'en')
@php($c = fn ($k) => $en ? (content('brand.'.$k.'_en') ?: content('brand.'.$k)) : content('brand.'.$k))
@php($li = fn ($row, $f) => $en ? (($row[$f.'_en'] ?? '') ?: ($row[$f] ?? '')) : ($row[$f] ?? ''))
@php($fe = fn ($row) => $en ? (($row['en'] ?? '') ?: ($row['fr'] ?? '')) : ($row['fr'] ?? ''))
@php($stats = collect(content('brand.stats'))->values())
@php($marquee = collect(content('brand.marquee'))->map($fe)->filter()->values())
@php($pillars = collect(content('brand.pillars'))->values())
@php($gallery = collect(content('brand.gallery'))->filter(fn ($g) => filled($g['image'] ?? null))->values())
@php($icons = [
  'pulse' => '<path d="M4 12h4l2-7 4 14 2-7h4" stroke-linecap="round" stroke-linejoin="round"/>',
  'star' => '<path d="M12 2l3 6 7 1-5 5 1 7-6-3-6 3 1-7-5-5 7-1z" stroke-linejoin="round"/>',
  'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/>',
  'pin' => '<path d="M12 22s-8-4.5-8-12a8 8 0 0116 0c0 7.5-8 12-8 12z" stroke-linejoin="round"/><circle cx="12" cy="10" r="3"/>',
  'truck' => '<rect x="3" y="7" width="13" height="10" rx="1.5"/><path d="M16 11h4l1 2v3h-5z"/><circle cx="7" cy="18" r="1.6"/><circle cx="18" cy="18" r="1.6"/>',
  'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/>',
])
@php($ico = fn ($k) => $icons[$k ?? ''] ?? $icons['star'])
@php($btnPrimary = 'kp-br-press group inline-flex items-center justify-center gap-2 rounded-full bg-white px-7 py-3.5 text-[12px] font-semibold uppercase tracking-[0.2em] text-black transition-all duration-300 hover:bg-white/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/55')
@php($btnGhost = 'group inline-flex items-center justify-center gap-2 rounded-full border border-white/15 bg-white/5 px-7 py-3.5 text-[12px] font-semibold uppercase tracking-[0.2em] text-white/85 backdrop-blur-sm transition-all duration-300 hover:border-kp-gold/45 hover:bg-white/[0.08] hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/55')
@php($eyebrow = 'inline-flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.28em] text-kp-gold/95')
@php($h2 = 'font-serif text-[clamp(1.9rem,4vw,3rem)] font-normal leading-[1.1] tracking-tight text-white')
@php($arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-x-0.5" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>')
@php($rv = fn ($from = 'bottom', $d = 0) => 'data-kp-br-rv data-from="'.$from.'"'.($d ? ' style="transition-delay:'.$d.'ms"' : ''))
@section('seo_title', content('brand.seo_title'))
@section('meta_description', content('brand.seo_description'))
@push('meta')
<link rel="canonical" href="{{ url('/kpandji-automobiles') }}">
<meta property="og:title" content="{{ content('brand.og_title') }}">
<meta property="og:description" content="{{ content('brand.og_description') }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="fr_FR">
<meta property="og:url" content="{{ url('/kpandji-automobiles') }}">
<link rel="preload" as="image" href="{{ cms_asset(content('brand.hero_image')) }}" fetchpriority="high">
@endpush
@section('content')
<style>
[data-kp-br-rv]{opacity:0;transform:translateY(40px);transition:opacity .75s cubic-bezier(.22,1,.36,1),transform .75s cubic-bezier(.22,1,.36,1)}
[data-kp-br-rv][data-from=left]{transform:translateX(-40px)}[data-kp-br-rv][data-from=right]{transform:translateX(40px)}
[data-kp-br-rv].is-in{opacity:1;transform:none}
@keyframes kp-br-marquee{from{transform:translateX(0)}to{transform:translateX(-50%)}}
.kp-br-marquee{animation:kp-br-marquee 38s linear infinite}
@keyframes kp-scroll-bounce{0%,100%{transform:translateY(0);opacity:.85}50%{transform:translateY(10px);opacity:.35}}
.kp-scroll-dot{animation:kp-scroll-bounce 1.8s ease-in-out infinite}
.kp-br-press:hover{transform:scale(1.02)}.kp-br-press:active{transform:scale(.98)}
.kp-br-lift:hover{transform:translateY(-2px)}
@media (prefers-reduced-motion:reduce){[data-kp-br-rv]{opacity:1!important;transform:none!important;transition:none!important}.kp-br-marquee,.kp-scroll-dot{animation:none!important}.kp-br-press:hover,.kp-br-press:active,.kp-br-lift:hover{transform:none}}
</style>
<div class="-mt-14 min-h-screen bg-kp-bg text-kp-accent sm:-mt-16 md:-mt-[125px]">
  {{-- HERO --}}
  <section aria-label="{{ $c('hero_aria') }}" class="relative isolate flex min-h-[92svh] items-end overflow-hidden pt-[110px] md:min-h-[96svh] md:pt-[132px]">
    {!! cms_edit(route('admin.brand').'#hero', 'Modifier cette section', '8.5rem') !!}
    <div class="absolute inset-0 -z-10"><img src="{{ cms_asset(content('brand.hero_image')) }}" alt="" fetchpriority="high" decoding="async" class="absolute inset-0 h-full w-full object-cover object-[50%_38%]"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-b from-black/60 via-black/35 to-black/95"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(900px_520px_at_18%_22%,rgba(201,169,98,0.20),transparent_60%),radial-gradient(900px_700px_at_50%_50%,transparent_55%,rgba(0,0,0,0.78)_100%)]"></div>
    <div aria-hidden="true" class="kp-grain pointer-events-none absolute inset-0 -z-10 opacity-[0.35] mix-blend-overlay"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-6 hidden lg:block">
      <span class="absolute left-0 top-0 h-8 w-8 border-l border-t border-white/15"></span><span class="absolute right-0 top-0 h-8 w-8 border-r border-t border-white/15"></span>
      <span class="absolute bottom-0 left-0 h-8 w-8 border-b border-l border-white/15"></span><span class="absolute bottom-0 right-0 h-8 w-8 border-b border-r border-white/15"></span>
    </div>
    <div aria-hidden="true" class="pointer-events-none absolute right-8 top-1/2 hidden -translate-y-1/2 lg:block">
      <div class="flex flex-col items-center gap-4"><span class="text-[10px] font-semibold uppercase tracking-[0.4em] text-white/55 [writing-mode:vertical-rl]">{{ $c('hero_chapter') }}</span><span class="h-24 w-px bg-gradient-to-b from-kp-gold/70 via-kp-gold/30 to-transparent"></span></div>
    </div>
    <div class="relative z-10 mx-auto w-full max-w-[1280px] px-5 pb-20 sm:px-8 md:pb-36 lg:px-12">
      <div {!! $rv() !!}><div class="{{ $eyebrow }}"><span aria-hidden="true" class="h-px w-10 bg-kp-gold/80 sm:w-16"></span>{{ $c('hero_kicker') }}</div></div>
      <div {!! $rv('bottom', 120) !!}><h1 class="mt-5 max-w-[20ch] font-serif text-[clamp(2.4rem,6.4vw,5.4rem)] font-medium leading-[1.02] tracking-[-0.025em] text-white"><span class="block">{{ $c('hero_title1') }}</span><span class="block bg-gradient-to-r from-white via-white/90 to-kp-gold/85 bg-clip-text text-transparent">{{ $c('hero_title2') }}</span></h1></div>
      <div {!! $rv('bottom', 200) !!}><p class="mt-6 max-w-2xl font-sans text-[15px] leading-relaxed text-white/[0.72] sm:text-[16px] md:text-[17px]">{{ $c('hero_text1') }} <span class="text-white">{{ content('brand.hero_brand') }}</span> {{ $c('hero_text2') }}</p></div>
      <div {!! $rv('bottom', 280) !!}><div class="mt-9 flex flex-wrap items-center gap-3 sm:gap-4">
        <a href="{{ cms_url(content('brand.discover_url')) }}" class="{{ $btnPrimary }}"><span>{{ $c('btn_discover') }}</span>{!! $arrow !!}</a>
        <a href="{{ cms_url(content('brand.contact_url')) }}" class="{{ $btnGhost }}">{{ $c('btn_contact') }}</a>
      </div></div>
      <div {!! $rv('bottom', 420) !!}><div class="mt-14 hidden items-center gap-3 sm:flex"><span aria-hidden="true" class="flex h-9 w-5 items-start justify-center rounded-full border border-white/20"><span class="kp-scroll-dot mt-1.5 h-1 w-1 rounded-full bg-white/80"></span></span><span class="text-[10px] font-semibold uppercase tracking-[0.32em] text-white/55">{{ $c('scroll') }}</span></div></div>
    </div>
    <div class="absolute inset-x-0 bottom-0 z-10 hidden border-t border-white/[0.08] bg-black/40 backdrop-blur-md md:block">
      <div class="mx-auto grid max-w-[1280px] grid-cols-4 px-8 lg:px-12">
        @foreach($stats as $i => $s)
        <div class="group relative flex flex-col gap-1 py-5 transition-colors duration-500 hover:bg-white/[0.03] {{ $i > 0 ? 'border-l border-white/[0.08] pl-6' : '' }}">
          <span aria-hidden="true" class="absolute inset-x-0 top-0 h-px origin-left scale-x-0 bg-gradient-to-r from-kp-gold/80 via-kp-gold/40 to-transparent transition-transform duration-700 group-hover:scale-x-100"></span>
          <span class="font-serif text-2xl font-medium tracking-tight text-white md:text-3xl">{{ $s['value'] ?? '' }}</span><span class="text-[11px] uppercase tracking-[0.18em] text-white/55">{{ $fe($s) }}</span>
        </div>
        @endforeach
      </div>
    </div>
  </section>
  <section class="border-y border-white/[0.08] bg-kp-surface md:hidden">
    <div class="mx-auto grid max-w-[1280px] grid-cols-2 gap-px bg-white/[0.08]">
      @foreach($stats as $s)<div class="flex flex-col gap-1 bg-kp-surface px-5 py-5"><span class="font-serif text-2xl font-medium tracking-tight text-white">{{ $s['value'] ?? '' }}</span><span class="text-[10px] uppercase tracking-[0.18em] text-white/55">{{ $fe($s) }}</span></div>@endforeach
    </div>
  </section>

  {{-- HISTOIRE / FONDATEUR --}}
  <section aria-labelledby="histoire-heading" class="relative isolate overflow-hidden bg-kp-bg">
    {!! cms_edit(route('admin.brand').'#histoire', 'Modifier cette section', '1rem') !!}
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(900px_500px_at_85%_10%,rgba(201,169,98,0.10),transparent_60%),radial-gradient(700px_500px_at_10%_90%,rgba(255,255,255,0.04),transparent_55%)]"></div>
    <div class="mx-auto max-w-[1280px] px-5 py-20 sm:px-8 md:py-28 lg:px-12">
      <div {!! $rv() !!}><div class="mx-auto max-w-2xl text-center">
        <p class="{{ $eyebrow }} justify-center"><span aria-hidden="true" class="h-px w-8 bg-kp-gold/60"></span>{{ $c('story_label') }}<span aria-hidden="true" class="h-px w-8 bg-kp-gold/60"></span></p>
        <h2 id="histoire-heading" class="mt-5 {{ $h2 }}">{{ $c('story_title1') }}<br class="hidden sm:block"> {{ $c('story_title2') }}</h2>
      </div></div>
      <div class="mt-14 grid gap-12 lg:mt-20 lg:grid-cols-[minmax(0,440px)_1fr] lg:items-start lg:gap-16">
        <div {!! $rv('left') !!}>
          <div class="relative mx-auto w-full max-w-[440px] lg:sticky lg:top-28 lg:mx-0">
            <div aria-hidden="true" class="pointer-events-none absolute -inset-4 rounded-[28px] bg-gradient-to-br from-kp-gold/[0.28] via-transparent to-white/5 opacity-80 blur-md"></div>
            <figure class="relative overflow-hidden rounded-2xl border border-white/10 bg-kp-elevated/40 shadow-[0_28px_90px_-42px_rgba(0,0,0,0.95)] backdrop-blur-md">
              <div class="relative aspect-[3/4] w-full bg-kp-elevated/60">
                <img src="{{ cms_asset(content('brand.founder_image')) }}" alt="{{ $c('founder_alt') }}" loading="eager" decoding="async" class="absolute inset-0 h-full w-full object-cover transition-transform duration-[1200ms] ease-out hover:scale-[1.03] motion-reduce:transition-none">
                <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
                <div aria-hidden="true" class="absolute right-4 top-4 flex h-12 w-12 items-center justify-center rounded-full border border-kp-gold/40 bg-black/45 font-serif text-[14px] font-semibold tracking-widest text-kp-gold/95 backdrop-blur-md sm:h-14 sm:w-14 sm:text-[15px]">{{ content('brand.founder_seal') }}</div>
                <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6"><p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-kp-gold/95">{{ $c('founder_kicker') }}</p><p class="mt-2 font-serif text-xl font-medium text-white sm:text-2xl">{{ $c('founder_title') }}</p></div>
              </div>
            </figure>
            <div class="mt-5 flex items-center justify-between gap-3">
              <div class="flex items-center gap-3"><span aria-hidden="true" class="h-1.5 w-1.5 rounded-full bg-kp-gold/90 shadow-[0_0_10px_rgba(201,169,98,0.45)]"></span><p class="text-[11px] uppercase tracking-[0.22em] text-white/55">{{ content('brand.founder_place') }}</p></div>
              <span class="text-[10px] font-semibold uppercase tracking-[0.28em] text-white/35">{{ content('brand.founder_index') }}</span>
            </div>
          </div>
        </div>
        <div class="space-y-12">
          @foreach([['origin', 80], ['mission', 140]] as [$k, $d])
          <div {!! $rv('right', $d) !!}><div class="relative pl-6 sm:pl-8">
            <span aria-hidden="true" class="absolute left-0 top-2 h-full w-px bg-gradient-to-b from-kp-gold/60 via-kp-gold/15 to-transparent"></span>
            <p class="{{ $eyebrow }}">{{ $c($k.'_label') }}</p>
            <h3 class="mt-3 font-serif text-2xl font-normal tracking-tight text-white md:text-3xl">{{ $c($k.'_title') }}</h3>
            <p class="mt-5 text-[15px] leading-[1.8] text-white/65 md:text-[16px]">{{ $c($k.'_text') }}</p>
          </div></div>
          @endforeach
          <div {!! $rv('bottom', 160) !!}>
            <figure class="relative overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br from-kp-elevated/[0.65] via-kp-elevated/30 to-kp-elevated/10 p-7 shadow-[0_24px_80px_-40px_rgba(0,0,0,0.9)] backdrop-blur-md md:p-10">
              <div aria-hidden="true" class="pointer-events-none absolute -right-6 -top-6 font-serif text-[120px] leading-none text-kp-gold/15 sm:-right-4 sm:-top-2 sm:text-[160px]">“</div>
              <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold/45 to-transparent"></div>
              <blockquote class="relative"><p class="font-serif text-lg italic leading-[1.55] text-white/90 md:text-2xl md:leading-[1.45]">{{ $c('quote') }}</p>
                <figcaption class="mt-6 flex items-center gap-3"><span aria-hidden="true" class="h-px w-8 bg-kp-gold/70"></span><span class="text-[11px] uppercase tracking-[0.24em] text-white/60">{{ $c('quote_by') }}</span></figcaption></blockquote>
            </figure>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- BANDEAU DE VALEURS --}}
  <section class="relative overflow-hidden border-y border-white/[0.08] bg-kp-bg py-7">
    {!! cms_edit(route('admin.brand').'#bandeau', 'Modifier le bandeau', '.4rem') !!}
    <div aria-hidden="true">
      <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-24 bg-gradient-to-r from-kp-bg to-transparent"></div>
      <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-24 bg-gradient-to-l from-kp-bg to-transparent"></div>
      <div class="kp-br-marquee flex w-max items-center gap-12 whitespace-nowrap text-white/55">
        @for($dup = 0; $dup < 2; $dup++)<div class="flex items-center gap-12">@foreach($marquee as $v)<span class="flex items-center gap-12 font-serif text-2xl font-normal tracking-tight md:text-3xl"><span class="hover:text-white">{{ $v }}</span><span class="h-1.5 w-1.5 shrink-0 rounded-full bg-kp-gold/80 shadow-[0_0_12px_rgba(201,169,98,0.4)]"></span></span>@endforeach</div>@endfor
      </div>
    </div>
  </section>

  {{-- ENGAGEMENTS --}}
  <section aria-labelledby="engagements-heading" class="relative isolate overflow-hidden bg-kp-surface">
    {!! cms_edit(route('admin.brand').'#engagements', 'Modifier cette section', '1rem') !!}
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 opacity-[0.22] bg-[linear-gradient(rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.03)_1px,transparent_1px)] bg-[length:72px_72px]"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold/55 to-transparent"></div>
    <div class="mx-auto max-w-[1280px] px-5 py-20 sm:px-8 md:py-28 lg:px-12">
      <div {!! $rv() !!}><div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between md:gap-10">
        <div class="max-w-2xl"><p class="{{ $eyebrow }}"><span aria-hidden="true" class="h-px w-8 bg-kp-gold/60"></span>{{ $c('pil_label') }}</p><h2 id="engagements-heading" class="mt-4 {{ $h2 }}">{{ $c('pil_title') }}</h2></div>
        <p class="max-w-md text-[14px] leading-relaxed text-white/55 md:text-[15px]">{{ $c('pil_text') }}</p>
      </div></div>
      <div class="mt-12 grid gap-4 md:mt-16 md:grid-cols-6 md:gap-5">
        @foreach($pillars as $i => $p)
        @php($n = str_pad($i + 1, 2, '0', STR_PAD_LEFT))
        @if($i === 0)
        <div class="md:col-span-3 md:row-span-2" {!! $rv() !!}>
          <article class="kp-br-lift group relative isolate h-full overflow-hidden rounded-2xl border border-white/10 bg-kp-bg p-7 transition-all duration-500 hover:border-kp-gold/30 md:p-10">
            <div class="absolute inset-0 -z-10">
              <img src="{{ cms_asset(content('brand.pillar_image')) }}" alt="" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover opacity-40 transition-all duration-1000 group-hover:scale-[1.04] group-hover:opacity-50 motion-reduce:transition-none">
              <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-gradient-to-tr from-black via-black/80 to-black/40"></div>
              <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(600px_320px_at_20%_30%,rgba(201,169,98,0.18),transparent_70%)]"></div>
            </div>
            <div class="flex h-full flex-col">
              <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-kp-gold/40 bg-kp-gold/10 text-kp-gold transition-colors duration-500 group-hover:bg-kp-gold/15"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="h-5 w-5" aria-hidden="true">{!! $ico($p['icon'] ?? null) !!}</svg></div>
              <h3 class="mt-6 font-serif text-2xl font-normal tracking-tight text-white md:text-3xl">{{ $li($p, 'title') }}</h3>
              <p class="mt-4 max-w-md text-[15px] leading-relaxed text-white/70 md:text-[16px]">{{ $li($p, 'body') }}</p>
              <div class="mt-auto flex items-end justify-between pt-10">
                <span class="inline-flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-kp-gold/85"><span>{{ $c('pil_word') }} {{ $n }}</span><span aria-hidden="true" class="h-px w-6 bg-current opacity-60"></span></span>
                <span aria-hidden="true" class="font-serif text-5xl font-medium leading-none text-white/[0.08] md:text-6xl">{{ $n }}</span>
              </div>
            </div>
          </article>
        </div>
        @else
        <div class="md:col-span-3 lg:col-span-2" {!! $rv('bottom', $i * 60) !!}>
          <article class="kp-br-lift group relative flex h-full flex-col overflow-hidden rounded-2xl border border-white/[0.08] bg-kp-bg p-7 transition-all duration-500 hover:border-kp-gold/25 hover:bg-kp-elevated/40 md:p-8">
            <div aria-hidden="true" class="pointer-events-none absolute inset-x-7 top-0 h-px origin-left scale-x-0 bg-gradient-to-r from-kp-gold/70 via-kp-gold/30 to-transparent transition-transform duration-700 group-hover:scale-x-100"></div>
            <div aria-hidden="true" class="pointer-events-none absolute -right-12 -top-12 h-40 w-40 rounded-full bg-kp-gold/10 opacity-0 blur-3xl transition-opacity duration-700 group-hover:opacity-100"></div>
            <div class="flex items-start justify-between">
              <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-kp-gold/30 bg-kp-gold/10 text-kp-gold transition-colors duration-500 group-hover:bg-kp-gold/15"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="h-5 w-5" aria-hidden="true">{!! $ico($p['icon'] ?? null) !!}</svg></div>
              <span aria-hidden="true" class="font-serif text-2xl font-medium leading-none text-white/[0.12] transition-colors duration-500 group-hover:text-white/20">{{ $n }}</span>
            </div>
            <h3 class="mt-5 font-serif text-xl font-normal tracking-tight text-white md:text-[22px]">{{ $li($p, 'title') }}</h3>
            <p class="mt-3 text-[14px] leading-relaxed text-white/60 md:text-[15px]">{{ $li($p, 'body') }}</p>
            <span class="mt-6 inline-flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-white/35 transition-colors duration-300 group-hover:text-kp-gold/85"><span>{{ $c('pil_word') }} {{ $n }}</span><span aria-hidden="true" class="h-px w-6 bg-current opacity-60"></span></span>
          </article>
        </div>
        @endif
        @endforeach
      </div>
    </div>
  </section>

  {{-- TRAJECTOIRE --}}
  <section aria-labelledby="journey-heading" class="relative isolate overflow-hidden bg-kp-bg">
    {!! cms_edit(route('admin.brand').'#trajectoire', 'Modifier cette section', '1rem') !!}
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(800px_500px_at_50%_0%,rgba(201,169,98,0.10),transparent_60%)]"></div>
    <div class="mx-auto max-w-[1280px] px-5 py-20 sm:px-8 md:py-28 lg:px-12">
      <div {!! $rv() !!}><div class="mx-auto max-w-2xl text-center">
        <p class="{{ $eyebrow }} justify-center"><span aria-hidden="true" class="h-px w-8 bg-kp-gold/60"></span>{{ $c('jr_label') }}<span aria-hidden="true" class="h-px w-8 bg-kp-gold/60"></span></p>
        <h2 id="journey-heading" class="mt-5 {{ $h2 }}">{{ $c('jr_title') }}</h2>
        <p class="mx-auto mt-5 max-w-xl text-[14px] leading-relaxed text-white/55 md:text-[15px]">{{ $c('jr_text') }}</p>
      </div></div>
      <div class="relative mt-14 md:mt-20">
        <div aria-hidden="true" class="pointer-events-none absolute left-0 right-0 top-7 hidden h-px bg-gradient-to-r from-transparent via-kp-gold/40 to-transparent md:block"></div>
        <ol class="grid gap-10 md:grid-cols-3 md:gap-8 lg:gap-10">
          @foreach(collect(content('brand.journey'))->values() as $i => $s)
          <li class="group relative flex h-full flex-col" {!! $rv('bottom', $i * 100) !!}>
            <div class="relative mb-8 flex items-center gap-4">
              <div aria-hidden="true" class="relative flex h-14 w-14 items-center justify-center rounded-full border border-kp-gold/40 bg-kp-bg shadow-[0_0_0_6px_rgba(5,5,5,1)] transition-all duration-500 group-hover:border-kp-gold/80"><span class="absolute inset-1.5 rounded-full bg-kp-gold/10 transition-colors duration-500 group-hover:bg-kp-gold/20"></span><span class="relative font-serif text-lg font-medium text-kp-gold/95">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span></div>
              <span class="text-[10px] font-semibold uppercase tracking-[0.28em] text-white/55">{{ $c('jr_step') }}</span>
            </div>
            <article class="relative flex h-full flex-col rounded-2xl border border-white/[0.08] bg-kp-elevated/40 p-7 backdrop-blur-sm transition-all duration-500 group-hover:-translate-y-0.5 group-hover:border-kp-gold/25 group-hover:bg-kp-elevated/[0.55] md:p-8">
              <p class="font-serif text-2xl font-medium tracking-tight text-kp-gold/95 md:text-3xl">{{ $li($s, 'year') }}</p>
              <h3 class="mt-3 font-serif text-xl font-normal text-white md:text-2xl">{{ $li($s, 'title') }}</h3>
              <p class="mt-3 text-[14px] leading-relaxed text-white/60 md:text-[15px]">{{ $li($s, 'body') }}</p>
              <span aria-hidden="true" class="pointer-events-none absolute inset-x-7 bottom-0 h-px origin-left scale-x-0 bg-gradient-to-r from-kp-gold/70 via-kp-gold/30 to-transparent transition-transform duration-700 group-hover:scale-x-100"></span>
            </article>
          </li>
          @endforeach
        </ol>
      </div>
    </div>
  </section>

  {{-- INSTALLATIONS --}}
  <section aria-labelledby="installations-heading" class="relative isolate overflow-hidden bg-kp-surface">
    {!! cms_edit(route('admin.brand').'#installations', 'Modifier cette section', '1rem') !!}
    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold/55 to-transparent"></div>
    <div class="mx-auto max-w-[1280px] px-5 py-20 sm:px-8 md:py-28 lg:px-12">
      <div {!! $rv() !!}><div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between md:gap-10">
        <div class="max-w-2xl"><p class="{{ $eyebrow }}"><span aria-hidden="true" class="h-px w-8 bg-kp-gold/60"></span>{{ $c('gal_label') }}</p><h2 id="installations-heading" class="mt-4 {{ $h2 }}">{{ $c('gal_title') }}</h2></div>
        <p class="max-w-md text-[14px] leading-relaxed text-white/55 md:text-[15px]">{{ $c('gal_text') }}</p>
      </div></div>
      <div {!! $rv('bottom', 120) !!}><div class="mt-12 grid gap-4 md:mt-16 md:grid-cols-3 md:grid-rows-2 md:gap-5">
        @foreach($gallery as $i => $g)
        @php($big = $i === 0)
        <figure class="group relative overflow-hidden rounded-2xl border border-white/10 bg-kp-elevated/40 {{ $big ? 'md:col-span-2 md:row-span-2' : '' }}">
          <div class="relative w-full {{ $big ? 'aspect-[16/10] md:aspect-auto md:h-full md:min-h-[480px]' : 'aspect-[4/3] md:aspect-auto md:h-full md:min-h-[230px]' }}">
            <img src="{{ cms_asset($g['image']) }}" alt="{{ $li($g, 'alt') }}" loading="eager" decoding="async" class="absolute inset-0 h-full w-full object-cover transition-transform duration-[1200ms] ease-out {{ $big ? 'group-hover:scale-[1.04]' : 'group-hover:scale-[1.05]' }} motion-reduce:transition-none">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-gradient-to-t {{ $big ? 'from-black/85 via-black/15' : 'from-black/80 via-black/10' }} to-transparent"></div>
            @if($big)<div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-24 bg-gradient-to-b from-black/40 to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>@endif
            <span class="absolute {{ $big ? 'left-6 top-6' : 'left-5 top-5' }} inline-flex items-center gap-2 rounded-full border border-white/15 bg-black/45 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.24em] text-white/85 backdrop-blur-md"><span class="h-1 w-1 rounded-full bg-kp-gold/95"></span>{{ $li($g, 'tag') }}</span>
            @if($big)
            <figcaption class="absolute inset-x-0 bottom-0 p-6 md:p-8"><p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-kp-gold/95">{{ $li($g, 'kicker') }}</p><p class="mt-2 font-serif text-2xl font-medium text-white md:text-3xl">{{ $li($g, 'title') }}</p>@if($li($g, 'body'))<p class="mt-3 max-w-md text-[13px] leading-relaxed text-white/65 md:text-[14px]">{{ $li($g, 'body') }}</p>@endif</figcaption>
            @else
            <figcaption class="absolute inset-x-0 bottom-0 p-5"><p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-kp-gold/95">{{ $li($g, 'kicker') }}</p><p class="mt-1.5 font-serif text-lg font-medium text-white">{{ $li($g, 'title') }}</p></figcaption>
            @endif
          </div>
        </figure>
        @endforeach
      </div></div>
    </div>
  </section>

  {{-- FIN DE PAGE --}}
  <section aria-labelledby="cta-heading" class="relative isolate overflow-hidden bg-kp-bg">
    {!! cms_edit(route('admin.brand').'#fin', 'Modifier cette section', '1rem') !!}
    <div class="absolute inset-0 -z-10"><img src="{{ cms_asset(content('brand.cta_image')) }}" alt="" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover object-[50%_45%] opacity-25"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-b from-kp-bg via-kp-bg/[0.85] to-kp-bg"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(900px_520px_at_50%_50%,rgba(201,169,98,0.18),transparent_60%)]"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold/55 to-transparent"></div>
    <div aria-hidden="true" class="kp-grain pointer-events-none absolute inset-0 -z-10 opacity-[0.22] mix-blend-overlay"></div>
    <div class="mx-auto max-w-[1100px] px-5 py-24 text-center sm:px-8 md:py-36 lg:px-12">
      <div {!! $rv() !!}><div class="mx-auto flex w-full max-w-sm items-center gap-3"><span aria-hidden="true" class="h-px flex-1 bg-gradient-to-r from-transparent via-kp-gold/45 to-kp-gold/20"></span><span aria-hidden="true" class="h-1.5 w-1.5 shrink-0 rounded-full bg-kp-gold/95 shadow-[0_0_14px_rgba(201,169,98,0.45)]"></span><span aria-hidden="true" class="h-px flex-1 bg-gradient-to-l from-transparent via-kp-gold/45 to-kp-gold/20"></span></div></div>
      <div {!! $rv('bottom', 80) !!}><p class="{{ $eyebrow }} mt-7 justify-center">{{ $c('cta_kicker') }}</p></div>
      <div {!! $rv('bottom', 140) !!}><h2 id="cta-heading" class="mt-5 font-serif text-[clamp(2rem,4.8vw,3.7rem)] font-normal leading-[1.06] tracking-tight text-white">{{ $c('cta_title1') }}<br class="hidden sm:block"><span class="bg-gradient-to-r from-kp-gold via-kp-gold/90 to-white/85 bg-clip-text text-transparent"> {{ $c('cta_title2') }}</span></h2></div>
      <div {!! $rv('bottom', 220) !!}><p class="mx-auto mt-6 max-w-2xl text-[15px] leading-relaxed text-white/70 md:text-[16px]">{{ $c('cta_text') }}</p></div>
      <div {!! $rv('bottom', 300) !!}><div class="mt-10 flex flex-wrap items-center justify-center gap-3 sm:gap-4">
        <a href="{{ cms_url(content('brand.cta_models_url')) }}" class="{{ $btnPrimary }}"><span>{{ $c('cta_btn_models') }}</span>{!! $arrow !!}</a>
        <a href="{{ cms_url(content('brand.cta_contact_url')) }}" class="{{ $btnGhost }}">{{ $c('cta_btn_contact') }}</a>
      </div></div>
      <div {!! $rv('bottom', 380) !!}><div class="mt-14 flex flex-col items-center gap-3 text-[10px] uppercase tracking-[0.32em] text-white/35 sm:flex-row sm:justify-center sm:gap-6">
        @foreach(collect(content('brand.cta_badges'))->map($fe)->filter()->values() as $i => $b)@if($i)<span aria-hidden="true" class="hidden h-1 w-1 rounded-full bg-white/25 sm:block"></span>@endif<span>{{ $b }}</span>@endforeach
      </div></div>
    </div>
  </section>
</div>
<script>
(function(){var els=document.querySelectorAll('[data-kp-br-rv]');
if(!('IntersectionObserver' in window)||matchMedia('(prefers-reduced-motion: reduce)').matches){els.forEach(function(e){e.classList.add('is-in')});return}
var io=new IntersectionObserver(function(en){en.forEach(function(x){if(x.isIntersecting){x.target.classList.add('is-in');io.unobserve(x.target)}})},{threshold:0.12,rootMargin:'0px 0px -40px 0px'});
els.forEach(function(e){io.observe(e)});})();
</script>
@endsection
