{{-- Kpandji Sira — port de app/sira/page.tsx + components/kp/Sira.tsx + SiraContactCTA.tsx (+ Reveal.tsx, kp-hero-zoom de globals.css).
     Contenu : Admin › Kpandji Sira (/admin/sira) — clés CMS « sira.* » ; inscriptions : table sira_requests --}}
@extends('layouts.app')
@php($en = app()->getLocale() === 'en')
@php($c = fn ($k) => $en ? (content('sira.'.$k.'_en') ?: content('sira.'.$k)) : content('sira.'.$k))
@php($li = fn ($row, $f) => $en ? (($row[$f.'_en'] ?? '') ?: ($row[$f] ?? '')) : ($row[$f] ?? ''))
@php($tiers = collect(content('sira.tiers'))->filter(fn ($t) => ($t['visible'] ?? '1') !== '0' && trim($t['model'] ?? '') !== '')->values())
@php($hex = fn ($h) => preg_match('/^#([0-9a-f]{6})$/i', trim($h ?? ''), $m) ? array_map('hexdec', str_split($m[1], 2)) : [201, 169, 98])
@php($tierStyle = function ($t) use ($hex) {
  $m = strtoupper(trim($t['metal'] ?? '')); [$r, $g, $b] = $hex($m); $rgb = "$r,$g,$b";
  return match ($m) {
    '#CD7F32' => ['accent' => 'linear-gradient(to bottom,rgba(139,90,43,.4),rgba(205,127,50,.18),transparent)', 'ring' => 'rgba(205,127,50,.4)', 'badge' => '#CD7F32', 'glow' => '0 0 48px -14px rgba(205,127,50,0.5)'],
    '#C0C0C0' => ['accent' => 'linear-gradient(to bottom,rgba(255,255,255,.22),rgba(255,255,255,.08),transparent)', 'ring' => 'rgba(255,255,255,.28)', 'badge' => 'rgba(255,255,255,.8)', 'glow' => '0 0 48px -14px rgba(255,255,255,0.2)'],
    default => ['accent' => "linear-gradient(to bottom,rgba($rgb,.35),rgba($rgb,.14),transparent)", 'ring' => "rgba($rgb,.45)", 'badge' => "rgb($rgb)", 'glow' => "0 0 48px -14px rgba($rgb,0.5)"],
  };
})
@php($metal = fn ($t) => preg_match('/^#[0-9a-f]{6}$/i', trim($t['metal'] ?? '')) ? trim($t['metal']) : '#C9A962')
@php($icons = [
  'shield' => '<path d="M12 2l7 4v6c0 5-3.5 9.5-7 10-3.5-.5-7-5-7-10V6l7-4z" stroke-linejoin="round"/>',
  'doc' => '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 12h8M8 16h5" stroke-linecap="round"/>',
  'umbrella' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke-linejoin="round"/>',
  'card' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M3 11h18M7 15h.01M11 15h.01" stroke-linecap="round"/>',
  'gear' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2" stroke-linecap="round"/>',
  'car' => '<path d="M5 17h14l-1.5-5.5a2 2 0 00-1.9-1.5H8.4a2 2 0 00-1.9 1.5L5 17z" stroke-linejoin="round"/><circle cx="7.5" cy="17.5" r="1.5" fill="currentColor" stroke="none"/><circle cx="16.5" cy="17.5" r="1.5" fill="currentColor" stroke="none"/>',
])
@php($pageMax = 'mx-auto w-full max-w-[1680px]')
@php($pad = 'px-5 sm:px-8 lg:px-12 xl:px-16')
@php($eyebrow = 'inline-flex items-center gap-3 font-sans text-[11px] font-semibold uppercase tracking-[0.28em] text-kp-gold/95')
@php($body = 'font-sans text-[14px] leading-relaxed text-white/55 md:text-[15px]')
@php($bodyLg = 'font-sans text-[15px] leading-relaxed text-white/[0.72] sm:text-[16px] md:text-[17px]')
@php($btnGold = 'group inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-full bg-kp-gold px-7 py-3.5 font-sans text-[11px] font-semibold uppercase tracking-[0.18em] text-black shadow-[0_12px_40px_-12px_rgba(201,169,98,0.55)] transition duration-300 hover:bg-[#d4b56e] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/55 sm:min-h-11 sm:w-auto sm:px-8')
@php($btnGhost = 'inline-flex min-h-12 w-full items-center justify-center rounded-full border border-white/15 bg-white/5 px-7 py-3.5 font-sans text-[11px] font-semibold uppercase tracking-[0.18em] text-white/85 backdrop-blur-sm transition hover:border-white/25 hover:bg-white/10 sm:min-h-11 sm:w-auto sm:px-8')
@php($input = 'w-full min-h-14 rounded-xl border border-white/[0.11] bg-black/40 px-5 py-4 font-sans text-[17px] leading-normal text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.04)] placeholder:text-white/35 transition-[border-color,box-shadow] duration-200 focus:border-kp-gold/45 focus:outline-none focus:shadow-[inset_0_1px_0_rgba(255,255,255,0.05),0_0_0_1px_rgba(201,169,98,0.12)] sm:min-h-16 sm:text-[18px]')
@php($label = 'mb-2.5 block text-left text-[12px] font-semibold uppercase tracking-[0.2em] text-white/65')
@php($arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-x-0.5" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>')
@section('seo_title', content('sira.seo_title'))
@section('meta_description', content('sira.seo_description'))
@push('meta')
<link rel="canonical" href="{{ url('/sira') }}">
<meta property="og:title" content="{{ content('sira.og_title') }}">
<meta property="og:description" content="{{ content('sira.og_description') }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="fr_FR">
<meta property="og:url" content="{{ url('/sira') }}">
<link rel="preload" as="image" href="{{ cms_asset(content('sira.hero_image')) }}" fetchpriority="high">
@endpush
@section('content')
<style>
.kp-si-rv{opacity:0;transform:translateY(40px);transition:opacity .75s cubic-bezier(.22,1,.36,1),transform .75s cubic-bezier(.22,1,.36,1)}
.kp-si-rv.is-in{opacity:1;transform:none}
@keyframes kp-cinematic-zoom{from{transform:scale(1) translate3d(0,0,0)}to{transform:scale(1.08) translate3d(0,-1.5%,0)}}
.kp-hero-zoom{animation:kp-cinematic-zoom 22s ease-in-out infinite alternate}
.kp-si-hero-img{object-position:52% 38%}@media(min-width:640px){.kp-si-hero-img{object-position:50% 40%}}
@media (prefers-reduced-motion:reduce){.kp-si-rv{opacity:1!important;transform:none!important;transition:none!important}.kp-hero-zoom{animation:none!important}}
</style>
<div class="min-h-screen bg-kp-bg text-white" x-data="kpSira()" @keydown.escape.window="open && close()">
<section id="sira" class="relative overflow-hidden bg-kp-bg" aria-labelledby="sira-title">
  {{-- Hero --}}
  <div class="relative isolate flex min-h-[min(88svh,720px)] items-end overflow-hidden sm:min-h-[min(80svh,760px)]">
    {!! cms_edit(route('admin.sira').'#hero', 'Modifier cette section', '6.5rem') !!}
    <div class="absolute inset-0 -z-10"><img src="{{ cms_asset(content('sira.hero_image')) }}" alt="" fetchpriority="high" decoding="async" class="kp-si-hero-img kp-hero-zoom absolute inset-0 h-full w-full object-cover"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-b from-black/45 via-black/55 to-kp-bg"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(780px_420px_at_18%_20%,rgba(201,169,98,0.18),transparent_58%)]"></div>
    <div aria-hidden="true" class="kp-grain pointer-events-none absolute inset-0 -z-10 opacity-[0.22] mix-blend-overlay"></div>
    <div class="relative z-10 w-full {{ $pageMax }} {{ $pad }} pb-12 pt-28 sm:pb-16 sm:pt-32 md:pb-20">
      <div class="kp-si-rv" data-kp-si-rv>
        <button type="button" onclick="if(history.length>1){history.back()}else{location.href='{{ url('/') }}'}" class="group inline-flex min-h-10 items-center gap-2 rounded-full border border-white/15 bg-black/35 px-4 py-2 font-sans text-[11px] font-semibold uppercase tracking-[0.18em] text-white/80 backdrop-blur-md transition hover:border-white/25 hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/55">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5 transition-transform duration-300 group-hover:-translate-x-0.5" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          <span>{{ $c('back') }}</span>
        </button>
      </div>
      <p class="kp-si-rv {{ $eyebrow }} mt-6 sm:mt-7" data-kp-si-rv style="transition-delay:40ms"><span aria-hidden="true" class="h-px w-8 bg-kp-gold/80 sm:w-12"></span>{{ $c('hero_eyebrow') }}</p>
      <h1 id="sira-title" class="kp-si-rv mt-4 max-w-[11ch] font-serif text-[clamp(2.75rem,12vw,5.5rem)] font-medium leading-[0.98] tracking-[-0.03em] text-white sm:mt-5 sm:max-w-none" data-kp-si-rv style="transition-delay:80ms">
        <span class="block">{{ content('sira.hero_brand') }}</span>
        <span class="block bg-gradient-to-r from-[#CD7F32] via-kp-gold to-[#E8D5A3] bg-clip-text text-transparent">{{ content('sira.hero_name') }}</span>
      </h1>
      <p class="kp-si-rv mt-5 max-w-md sm:mt-6 {{ $bodyLg }}" data-kp-si-rv style="transition-delay:140ms;text-wrap:pretty">{{ $c('hero_text') }}</p>
      <div class="kp-si-rv mt-8 flex flex-col gap-3 sm:mt-9 sm:flex-row sm:flex-wrap sm:items-center" data-kp-si-rv style="transition-delay:200ms">
        <button type="button" @click="openModal()" class="{{ $btnGold }}"><span>{{ $c('cta') }}</span>{!! $arrow !!}</button>
        <a href="#niveaux" class="{{ $btnGhost }}">{{ $c('cta_tiers') }}</a>
      </div>
    </div>
  </div>

  {{-- En bref --}}
  <div class="relative {{ $pageMax }} {{ $pad }} py-12 sm:py-16 md:py-20">
    {!! cms_edit(route('admin.sira').'#bref', 'Modifier cette section', '1rem') !!}
    <div class="kp-si-rv mx-auto max-w-2xl text-center" data-kp-si-rv>
      <p class="{{ $eyebrow }} justify-center"><span aria-hidden="true" class="h-px w-6 bg-kp-gold/60"></span>{{ $c('brief_eyebrow') }}<span aria-hidden="true" class="h-px w-6 bg-kp-gold/60"></span></p>
      <h2 class="mt-4 font-serif text-[clamp(1.65rem,4vw,2.5rem)] font-medium leading-[1.1] tracking-tight text-white">{{ $c('brief_title') }}</h2>
    </div>
    <div class="mt-10 grid gap-4 sm:mt-12 sm:grid-cols-3 sm:gap-5 lg:gap-6">
      @foreach(collect(content('sira.brief'))->values() as $i => $it)
      <div class="kp-si-rv" data-kp-si-rv style="transition-delay:{{ $i * 70 }}ms"><article class="h-full border-t border-kp-gold/35 pt-5 sm:pt-6">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-kp-gold">{{ $li($it, 'q') }}</p>
        <p class="mt-3 {{ $bodyLg }}" style="text-wrap:pretty">{{ $li($it, 'a') }}</p>
      </article></div>
      @endforeach
    </div>
  </div>

  {{-- Comment ça marche --}}
  <div class="relative border-y border-white/[0.08] bg-[radial-gradient(900px_320px_at_50%_0%,rgba(201,169,98,0.08),transparent_60%)]">
    {!! cms_edit(route('admin.sira').'#parcours', 'Modifier cette section', '1rem') !!}
    <div class="{{ $pageMax }} {{ $pad }} py-12 sm:py-16">
      <div class="kp-si-rv max-w-xl" data-kp-si-rv>
        <p class="{{ $eyebrow }}"><span aria-hidden="true" class="h-px w-8 bg-kp-gold/80"></span>{{ $c('how_eyebrow') }}</p>
        <h2 class="mt-3 font-serif text-[clamp(1.65rem,4vw,2.5rem)] font-medium leading-[1.1] text-white">{{ $c('how_title') }}</h2>
      </div>
      @php($steps = collect(content('sira.steps'))->values())
      <ol class="mt-10 grid gap-6 sm:mt-12 sm:grid-cols-3 sm:gap-8">
        @foreach($steps as $i => $st)
        <li class="kp-si-rv relative" data-kp-si-rv style="transition-delay:{{ $i * 80 }}ms">
          @if($i < $steps->count() - 1)<span aria-hidden="true" class="absolute left-[2.25rem] top-5 hidden h-px w-[calc(100%-1rem)] bg-gradient-to-r from-kp-gold/40 to-transparent sm:block"></span>@endif
          <span class="font-serif text-3xl text-kp-gold/40 sm:text-4xl">{{ $st['n'] ?? '' }}</span>
          <h3 class="mt-3 font-serif text-xl text-white sm:text-[1.35rem]">{{ $li($st, 'title') }}</h3>
          <p class="mt-2 {{ $body }}">{{ $li($st, 'body') }}</p>
        </li>
        @endforeach
      </ol>
    </div>
  </div>

  {{-- Les 3 niveaux --}}
  <div id="niveaux" class="relative {{ $pageMax }} {{ $pad }} scroll-mt-24 py-12 sm:py-16 md:py-20">
    {!! cms_edit(route('admin.sira').'#niveaux', 'Modifier cette section', '1rem') !!}
    <div class="kp-si-rv flex flex-col gap-4 md:flex-row md:items-end md:justify-between md:gap-10" data-kp-si-rv>
      <div class="max-w-xl">
        <p class="{{ $eyebrow }}"><span aria-hidden="true" class="h-px w-8 bg-kp-gold/80"></span>{{ $c('tiers_eyebrow') }}</p>
        <h2 class="mt-3 font-serif text-[clamp(1.65rem,4vw,2.75rem)] font-medium leading-[1.08] text-white">{{ $c('tiers_title') }}</h2>
      </div>
      <p class="max-w-sm {{ $body }} md:text-right" style="text-wrap:pretty">{{ $c('tiers_text') }}</p>
    </div>
    <div aria-hidden="true" class="relative mx-auto mb-10 mt-10 hidden max-w-3xl xl:block">
      <div class="pointer-events-none absolute left-[10%] right-[10%] top-[13px] h-px bg-white/10"></div>
      <div class="pointer-events-none absolute left-[10%] right-[10%] top-[13px] h-px opacity-70" style="background:linear-gradient(to right,{{ $tiers->map(fn ($t) => $metal($t))->implode(',') ?: '#CD7F32,#C0C0C0,#C9A962' }})"></div>
      <ol class="relative flex justify-between">
        @foreach($tiers as $t)
        <li class="flex w-28 flex-col items-center">
          <span class="relative flex h-7 w-7 items-center justify-center">
            <span class="absolute inset-0 rounded-full bg-kp-bg" style="border:1px solid {{ $metal($t) }}59"></span>
            <span class="absolute inset-[5px] rounded-full" style="border:1px solid {{ $metal($t) }}40"></span>
            <span class="relative h-2 w-2 rounded-full" style="background-color:{{ $metal($t) }};box-shadow:0 0 14px -2px {{ $metal($t) }}"></span>
          </span>
          <p class="mt-3 text-[10px] font-semibold uppercase tracking-[0.22em]" style="color:{{ $metal($t) }}">{{ $li($t, 'name') }}</p>
          <p class="mt-1 text-[9px] font-medium uppercase tracking-[0.16em] text-white/30">≥ {{ $t['min'] ?? '' }} {{ $c('veh_short') }}</p>
        </li>
        @endforeach
      </ol>
    </div>
    <div class="mt-8 grid gap-4 sm:mt-10 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3 lg:gap-6 xl:mt-0">
      @foreach($tiers as $i => $t)
      @php($s = $tierStyle($t))
      <div class="kp-si-rv" data-kp-si-rv style="transition-delay:{{ $i * 70 }}ms">
        <article class="group relative flex h-full flex-col overflow-hidden rounded-2xl border bg-kp-elevated/25 transition-all duration-500 hover:bg-kp-elevated/40 motion-safe:hover:-translate-y-1" style="border-color:{{ $s['ring'] }};box-shadow:{{ $s['glow'] }}">
          <div aria-hidden="true" class="pointer-events-none absolute inset-0" style="background:{{ $s['accent'] }}"></div>
          <div class="absolute right-0 top-0 z-10 px-3.5 py-1.5 text-[9px] font-bold uppercase tracking-[0.22em] text-black sm:px-4 sm:py-2" style="background-color:{{ $metal($t) }}">{{ $li($t, 'name') }}</div>
          <div class="relative flex flex-1 flex-col p-4 sm:p-5 md:p-6">
            <p class="pr-16 text-[10px] font-semibold uppercase tracking-[0.24em]" style="color:{{ $s['badge'] }}">{{ $li($t, 'full') }}</p>
            <h3 class="mt-1 font-serif text-[1.65rem] text-white sm:text-2xl">{{ $t['model'] }}</h3>
            <p class="mt-2 {{ $body }}">{{ $li($t, 'blurb') }}</p>
            <div class="relative mt-4 aspect-[16/10] overflow-hidden rounded-xl border border-white/[0.08] bg-black/30 sm:mt-5">
              @if(filled($t['image'] ?? null))<img src="{{ cms_asset($t['image']) }}" alt="{{ strtr($c('model_alt'), ['{model}' => $t['model'], '{tier}' => $li($t, 'full')]) }}" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover object-center transition duration-700 motion-safe:group-hover:scale-[1.04]">@endif
              <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/55 via-transparent to-transparent"></div>
              <div class="absolute bottom-2.5 left-2.5 rounded-full border border-white/15 bg-black/50 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/80 backdrop-blur-sm sm:bottom-3 sm:left-3 sm:px-3">≥ {{ $t['min'] ?? '' }} {{ $c('veh_short') }}</div>
            </div>
            <div class="mt-4 flex items-center justify-between border-t border-white/[0.08] pt-3.5 sm:mt-5 sm:pt-4">
              <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-white/40">{{ $c('minimum') }}</p>
              <p class="font-serif text-lg text-white">{{ $t['min'] ?? '' }}<span class="ml-1 text-sm text-white/45">{{ $c('vehicles') }}</span></p>
            </div>
            @if(filled($t['brochure'] ?? null))
            <a href="{{ cms_asset($t['brochure']) }}" download="{{ $t['brochure_name'] ?: basename($t['brochure']) }}" class="mt-auto inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-full border border-white/[0.12] bg-white/[0.04] px-5 py-2.5 font-sans text-[10px] font-semibold uppercase tracking-[0.18em] text-white/75 transition duration-300 hover:border-white/25 hover:bg-white/[0.08] hover:text-white sm:mt-5">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5 shrink-0" aria-hidden="true"><path d="M12 3v12M7 11l5 5 5-5" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 19h14" stroke-linecap="round"/></svg>
              <span>{{ $c('brochure_btn') }}</span>
            </a>
            @endif
          </div>
        </article>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Inclus --}}
  <div class="relative border-t border-white/[0.08]">
    {!! cms_edit(route('admin.sira').'#inclus', 'Modifier cette section', '1rem') !!}
    <div class="{{ $pageMax }} {{ $pad }} py-12 sm:py-16 md:py-20">
      <div class="kp-si-rv max-w-xl" data-kp-si-rv>
        <p class="{{ $eyebrow }}"><span aria-hidden="true" class="h-px w-8 bg-kp-gold/80"></span>{{ $c('inc_eyebrow') }}</p>
        <h2 class="mt-3 font-serif text-[clamp(1.65rem,4vw,2.5rem)] font-medium leading-[1.1] text-white">{{ $c('inc_title') }}</h2>
      </div>
      <div class="mt-10 grid gap-3 sm:mt-12 sm:grid-cols-2 sm:gap-4 lg:grid-cols-3">
        @foreach(collect(content('sira.included'))->values() as $i => $it)
        <div class="kp-si-rv" data-kp-si-rv style="transition-delay:{{ ($i % 3) * 50 }}ms"><article class="flex h-full gap-3.5 rounded-2xl border border-white/[0.07] bg-kp-elevated/25 p-4 backdrop-blur-md sm:gap-4 sm:p-5">
          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-kp-gold/25 bg-kp-gold/10 text-kp-gold sm:h-11 sm:w-11"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-[18px] w-[18px] sm:h-5 sm:w-5" aria-hidden="true">{!! $icons[$it['icon'] ?? ''] ?? $icons['shield'] !!}</svg></span>
          <div class="min-w-0">
            <h3 class="font-sans text-[14px] font-semibold leading-snug text-white/90 sm:text-[15px]">{{ $li($it, 'title') }}</h3>
            <p class="mt-1.5 text-[13px] leading-relaxed text-white/50 sm:text-[14px]">{{ $li($it, 'body') }}</p>
          </div>
        </article></div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- CTA final --}}
  <div class="relative border-t border-white/[0.08] bg-[radial-gradient(900px_400px_at_50%_0%,rgba(201,169,98,0.10),transparent_65%)]">
    {!! cms_edit(route('admin.sira').'#fin', 'Modifier cette section', '1rem') !!}
    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold/45 to-transparent"></div>
    <div class="{{ $pageMax }} {{ $pad }} py-14 sm:py-20 md:py-24">
      <div class="kp-si-rv mx-auto max-w-2xl text-center" data-kp-si-rv>
        <h2 class="font-serif text-[clamp(1.75rem,4.2vw,2.75rem)] font-medium leading-[1.1] tracking-tight text-white">{{ $c('final_title') }}</h2>
        <p class="mx-auto mt-4 max-w-lg {{ $bodyLg }}" style="text-wrap:pretty">{{ $c('final_text') }}</p>
        <div class="mt-8 flex flex-col items-stretch gap-3 sm:mt-10 sm:flex-row sm:flex-wrap sm:items-center sm:justify-center sm:gap-4">
          <button type="button" @click="openModal()" class="{{ $btnGold }}"><span>{{ $c('cta') }}</span>{!! $arrow !!}</button>
          <a href="{{ cms_url(content('sira.advisor_url')) }}" class="{{ $btnGhost }}">{{ $c('advisor') }}</a>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Formulaire « M'inscrire au projet » (SiraContactCTA.tsx) --}}
<template x-teleport="body">
<div x-show="open" x-cloak class="fixed inset-0 z-[200] flex items-stretch justify-center sm:items-center sm:p-6 md:p-8" role="dialog" aria-modal="true" aria-labelledby="sira-contact-title" @click.self="close()">
  <div class="absolute inset-0 bg-black/[0.88] backdrop-blur-md" aria-hidden="true" @click="close()"></div>
  <div x-ref="panel" tabindex="-1" class="relative z-10 flex h-[100dvh] w-full max-w-5xl flex-col overflow-hidden border-white/[0.12] bg-[#080808] shadow-[0_32px_100px_rgba(0,0,0,0.65)] outline-none sm:h-auto sm:max-h-[min(92dvh,880px)] sm:min-h-[min(82dvh,720px)] sm:rounded-2xl sm:border">
    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold/50 to-transparent"></div>
    <div aria-hidden="true" class="pointer-events-none absolute -top-24 left-1/2 h-48 w-[70%] -translate-x-1/2 rounded-full bg-kp-gold/10 blur-3xl"></div>
    <div class="relative flex shrink-0 items-start justify-between gap-4 border-b border-white/10 px-6 py-6 sm:px-10 sm:py-8 lg:px-14">
      <div>
        <p class="font-sans text-[12px] font-semibold uppercase tracking-[0.28em] text-kp-gold">{{ content('sira.f_kicker') }}</p>
        <h2 id="sira-contact-title" class="mt-2 font-serif text-[1.85rem] leading-tight text-white sm:text-[2.35rem]">{{ $c('cta') }}</h2>
        <p class="mt-3 max-w-xl font-sans text-[15px] leading-relaxed text-white/70 sm:text-[17px]">{{ $c('f_intro') }}</p>
      </div>
      <button type="button" @click="close()" class="shrink-0 rounded-full p-2.5 text-white/70 transition hover:bg-white/10 hover:text-white" aria-label="{{ $c('f_close_aria') }}"><span class="block text-3xl leading-none" aria-hidden="true">×</span></button>
    </div>
    <div x-show="success" role="status" class="relative flex flex-1 flex-col items-center justify-center px-6 py-14 text-center sm:px-10 sm:py-16 lg:px-14">
      <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-kp-gold/35 bg-kp-gold/10 text-kp-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-7 w-7" aria-hidden="true"><path d="M5 12l5 5L20 7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
      <p class="mt-6 font-serif text-[1.85rem] text-white sm:text-[2.2rem]">{{ $c('f_ok_title') }}</p>
      <p class="mx-auto mt-4 max-w-md text-[16px] leading-relaxed text-white/65 sm:text-[18px]">{{ $c('f_ok_text') }}</p>
      <button type="button" @click="close()" class="mt-10 rounded-full border border-white/15 px-10 py-3.5 font-sans text-[12px] font-semibold uppercase tracking-[0.22em] text-white/75 transition hover:border-white/25 hover:text-white">{{ $c('f_close') }}</button>
    </div>
    <form x-show="!success" @submit.prevent="submit()" method="POST" action="{{ route('sira.store') }}" class="relative flex min-h-0 flex-1 flex-col overflow-y-auto px-6 py-7 sm:px-10 sm:py-9 lg:px-14" novalidate>
      @csrf
      <div class="flex flex-1 flex-col justify-center gap-7 sm:gap-8">
        <div><label for="sira-contact-name" class="{{ $label }}">{{ $c('f_name') }} <span class="text-kp-gold">*</span></label>
          <input id="sira-contact-name" name="name" type="text" autocomplete="name" maxlength="120" x-model="f.name" @input="error=null" placeholder="Jean Kouassi" class="{{ $input }}"></div>
        <div class="grid gap-7 md:grid-cols-2 md:gap-6">
          <div><label for="sira-contact-country" class="{{ $label }}">{{ $c('f_country') }} <span class="text-kp-gold">*</span></label>
            <input id="sira-contact-country" name="country" type="text" autocomplete="country-name" maxlength="120" x-model="f.country" @input="error=null" placeholder="{{ $c('f_country_ph') }}" class="{{ $input }}"></div>
          <div><label for="sira-contact-city" class="{{ $label }}">{{ $c('f_city') }} <span class="text-kp-gold">*</span></label>
            <input id="sira-contact-city" name="city" type="text" autocomplete="address-level2" maxlength="120" x-model="f.city" @input="error=null" placeholder="{{ $c('f_city_ph') }}" class="{{ $input }}"></div>
        </div>
        <div class="grid gap-7 md:grid-cols-2 md:gap-6">
          <div><label for="sira-contact-phone" class="{{ $label }}">{{ $c('f_phone') }} <span class="text-kp-gold">*</span></label>
            <input id="sira-contact-phone" name="phone" type="tel" autocomplete="tel" maxlength="40" x-model="f.phone" @input="error=null" placeholder="+225 …" class="{{ $input }}"></div>
          <div><label for="sira-contact-email" class="{{ $label }}">{{ $c('f_email') }} <span class="text-kp-gold">*</span></label>
            <input id="sira-contact-email" name="email" type="email" autocomplete="email" inputmode="email" maxlength="160" x-model="f.email" @input="error=null" placeholder="vous@exemple.com" class="{{ $input }}"></div>
        </div>
        <p x-show="error" x-text="error" role="alert" class="rounded-xl border border-red-400/25 bg-red-950/35 px-5 py-4 text-[15px] text-red-200/95"></p>
      </div>
      <div class="mt-8 flex flex-col gap-4 border-t border-white/[0.07] pt-7 sm:mt-10 sm:flex-row sm:items-center sm:justify-between sm:pt-8">
        <button type="submit" :disabled="sending" class="min-h-14 w-full rounded-full bg-kp-gold px-10 py-4 font-sans text-[12px] font-semibold uppercase tracking-[0.22em] text-black shadow-[0_16px_40px_-12px_rgba(201,169,98,0.45)] transition-colors duration-300 hover:bg-[#d4b56e] disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto" x-text="sending ? @js($c('f_sending')) : @js($c('f_submit'))">{{ $c('f_submit') }}</button>
        <p class="text-center text-[13px] leading-relaxed text-white/40 sm:text-right">{{ $c('f_privacy') }}</p>
      </div>
    </form>
  </div>
</div>
</template>
</div>
<script>
function kpSira(){return{open:false,success:false,sending:false,error:null,f:{name:@js(auth()->user()?->name ?? ''),country:'',city:'',phone:@js(auth()->user()?->phone ?? ''),email:@js(auth()->user()?->email ?? '')},_ov:'',
 openModal(){this.f={name:@js(auth()->user()?->name ?? ''),country:'',city:'',phone:@js(auth()->user()?->phone ?? ''),email:@js(auth()->user()?->email ?? '')};this.error=null;this.success=false;this.open=true;this._ov=document.body.style.overflow;document.body.style.overflow='hidden';setTimeout(()=>this.$refs.panel&&this.$refs.panel.focus(),50)},
 close(){this.open=false;document.body.style.overflow=this._ov},
 async submit(){this.error=null;const t={};for(const k in this.f)t[k]=String(this.f[k]).trim();
  const M=@js(['name' => $c('e_name'), 'country' => $c('e_country'), 'city' => $c('e_city'), 'phone' => $c('e_phone'), 'email' => $c('e_email'), 'bad' => $c('e_email_bad'), 'server' => $c('e_server'), 'net' => $c('e_network')]);
  for(const k of ['name','country','city','phone','email']){if(!t[k]){this.error=M[k];return}}
  if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(t.email)){this.error=M.bad;return}
  this.sending=true;
  try{const r=await fetch(@js(route('sira.store')),{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':@js(csrf_token())},body:JSON.stringify(t)});
   const d=await r.json().catch(()=>null);if(!r.ok){this.error=(d&&d.error)||M.server;return}this.success=true}
  catch(e){this.error=M.net}finally{this.sending=false}}}}
(function(){var els=document.querySelectorAll('[data-kp-si-rv]');
if(!('IntersectionObserver' in window)||matchMedia('(prefers-reduced-motion: reduce)').matches){els.forEach(function(e){e.classList.add('is-in')});return}
var io=new IntersectionObserver(function(en){en.forEach(function(x){if(x.isIntersecting){x.target.classList.add('is-in');io.unobserve(x.target)}})},{threshold:0.14});
els.forEach(function(e){io.observe(e)});})();
</script>
@endsection
