{{-- Service après vente — port de app/sav/page.tsx + components/kp/ServiceApresVente.tsx + EcrireSavModal.tsx (+ Reveal.tsx, kp-marquee de globals.css).
     Contenu : Admin › S.A.V. (/admin/sav) — clés CMS « savp.* » ; demandes : table sav_requests --}}
@extends('layouts.app')
@php($en = app()->getLocale() === 'en')
@php($c = fn ($k) => $en ? (content('savp.'.$k.'_en') ?: content('savp.'.$k)) : content('savp.'.$k))
@php($li = fn ($row, $f) => $en ? (($row[$f.'_en'] ?? '') ?: ($row[$f] ?? '')) : ($row[$f] ?? ''))
@php($fe = fn ($row) => $en ? (($row['en'] ?? '') ?: ($row['fr'] ?? '')) : ($row['fr'] ?? ''))
@php($gallery = collect(content('savp.gallery'))->filter(fn ($g) => filled($g['image'] ?? null))->values())
@php($main = $gallery->first())
@php($marquee = collect(content('savp.marquee'))->map($fe)->filter()->values())
@php($tel = cms_url(content('savp.phone_link')))
@php($icons = [
  'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 8v4l2.5 2.5" stroke-linecap="round"/>',
  'shield' => '<path d="M12 2l7 4v6c0 5-3.5 9.5-7 10-3.5-.5-7-5-7-10V6l7-4z" stroke-linejoin="round"/>',
  'wrench' => '<path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" stroke-linejoin="round"/>',
  'check' => '<path d="M9 11l3 3L22 4" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" stroke-linejoin="round"/>',
])
@php($pad = 'mx-auto w-full max-w-[1680px] px-5 py-16 sm:px-8 sm:py-20 md:py-24 lg:px-12 lg:py-28 xl:px-16')
@php($eyebrow = 'inline-flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.28em] text-kp-gold/95')
@php($sectionTitle = 'font-serif text-[clamp(1.75rem,4.2vw,3rem)] font-normal leading-[1.1] tracking-tight text-white')
@php($btnPrimary = 'kp-sv-press inline-flex min-h-11 items-center justify-center rounded-full bg-kp-gold px-8 py-3.5 font-sans text-[11px] font-semibold uppercase tracking-[0.18em] text-black shadow-[0_12px_40px_-12px_rgba(201,169,98,0.55)] transition duration-300 hover:bg-[#d4b56e] hover:shadow-[0_16px_48px_-10px_rgba(201,169,98,0.5)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/55')
@php($btnGhost = 'kp-sv-press inline-flex min-h-11 items-center justify-center rounded-full border border-white/40 bg-white/[0.07] px-8 py-3.5 font-sans text-[11px] font-semibold uppercase tracking-[0.18em] text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.12)] backdrop-blur-md transition duration-300 hover:border-white/55 hover:bg-white/[0.12] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/55')
@php($input = 'w-full rounded-xl border border-white/[0.11] bg-black/40 px-4 py-3.5 font-sans text-[15px] text-kp-accent shadow-[inset_0_1px_0_rgba(255,255,255,0.04)] placeholder:text-white/[0.28] transition-[border-color,box-shadow] duration-200 focus:border-kp-gold/45 focus:outline-none focus:shadow-[inset_0_1px_0_rgba(255,255,255,0.05),0_0_0_1px_rgba(201,169,98,0.12)]')
@php($label = 'mb-2 block text-left text-[10px] font-semibold uppercase tracking-[0.22em] text-white/[0.38]')
@php($intro = function ($lab, $titleHtml, $desc, $id = null, $center = true) use ($eyebrow, $sectionTitle) {
  return '<div class="'.($center ? 'mx-auto max-w-3xl text-center' : 'max-w-2xl').'"><p class="'.$eyebrow.($center ? ' justify-center' : '').'"><span aria-hidden="true" class="h-px w-8 bg-kp-gold/60"></span>'.e($lab).'<span aria-hidden="true" class="h-px w-8 bg-kp-gold/60"></span></p>'
    .'<h2'.($id ? ' id="'.$id.'"' : '').' class="mt-5 '.$sectionTitle.'">'.$titleHtml.'</h2><p class="mx-auto mt-5 max-w-2xl text-[14px] leading-relaxed text-white/55 md:text-[15px]" style="text-wrap:pretty">'.e($desc).'</p></div>';
})
@section('seo_title', content('savp.seo_title'))
@section('meta_description', content('savp.seo_description'))
@push('meta')
<link rel="canonical" href="{{ url('/sav') }}">
<meta property="og:title" content="{{ content('savp.og_title') }}">
<meta property="og:description" content="{{ content('savp.og_description') }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="fr_FR">
<meta property="og:url" content="{{ url('/sav') }}">
<link rel="preload" as="image" href="{{ cms_asset(content('savp.hero_image')) }}" fetchpriority="high">
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
@endpush
@section('content')
<style>
.kp-sv-rv{opacity:0;transform:translateY(40px);transition:opacity .75s cubic-bezier(.22,1,.36,1),transform .75s cubic-bezier(.22,1,.36,1)}
.kp-sv-rv[data-from=left]{transform:translateX(-40px)}.kp-sv-rv[data-from=right]{transform:translateX(40px)}
.kp-sv-rv[data-from=pillar]{transform:translateY(18px) scale(.98);transition-duration:.65s}.kp-sv-rv[data-from=hero]{transform:translateY(22px);transition-duration:.78s}
.kp-sv-rv.is-in{opacity:1;transform:none}
@keyframes kp-marquee{from{transform:translate3d(0,0,0)}to{transform:translate3d(-50%,0,0)}}
.kp-marquee-track{animation:kp-marquee 36s linear infinite}
.kp-sv-press:hover{transform:scale(1.03)}.kp-sv-press:active{transform:scale(.98)}
.kp-sv-select{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='rgba(255,255,255,0.45)'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");background-size:1rem;background-position:right 1rem center;background-repeat:no-repeat}
@media (prefers-reduced-motion:reduce){.kp-sv-rv{opacity:1!important;transform:none!important;transition:none!important}.kp-marquee-track{animation:none!important}.kp-sv-press:hover,.kp-sv-press:active{transform:none}.kp-sv-modal{transition:none!important}}
</style>
<div class="min-h-screen bg-kp-bg" x-data="kpSav()" @keydown.escape.window="open && close()">
<main class="relative overflow-hidden">
  <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(1200px_700px_at_18%_10%,rgba(201,169,98,0.08),transparent_60%),radial-gradient(900px_600px_at_82%_60%,rgba(255,255,255,0.04),transparent_62%)]"></div>
  <div aria-hidden="true" class="kp-grain pointer-events-none absolute inset-0 -z-10 opacity-[0.08] mix-blend-overlay"></div>
  <div class="relative">
    {{-- Hero --}}
    <section class="relative flex min-h-[min(72vh,760px)] flex-col justify-end overflow-hidden ring-1 ring-inset ring-white/10" aria-labelledby="sav-title">
      {!! cms_edit(route('admin.sav').'#hero', 'Modifier cette section', '6.5rem') !!}
      <div class="absolute inset-0 z-0">
        <div class="relative h-full min-h-[min(72vh,760px)] w-full overflow-hidden"><img src="{{ cms_asset(content('savp.hero_image')) }}" alt="" aria-hidden="true" fetchpriority="high" decoding="async" class="absolute inset-0 h-full w-full scale-105 object-cover object-center"></div>
        <div class="absolute inset-0 bg-[radial-gradient(900px_700px_at_80%_60%,transparent_38%,rgba(0,0,0,0.72)_100%),radial-gradient(980px_520px_at_12%_18%,rgba(201,169,98,0.22),transparent_55%),linear-gradient(to_bottom,rgba(0,0,0,0.55),rgba(0,0,0,0.25),rgba(0,0,0,0.95))]" aria-hidden="true"></div>
        <div class="kp-grain absolute inset-0 opacity-[0.18] md:opacity-[0.22]" aria-hidden="true"></div>
      </div>
      <div class="relative z-10 mx-auto w-full max-w-[1680px] px-5 pb-14 pt-28 md:px-10 md:pb-20 md:pt-32 lg:pb-24">
        <div class="grid items-end gap-10 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)] lg:gap-14 xl:gap-20">
          <div class="max-w-2xl">
            <div class="kp-sv-rv flex items-center gap-3" aria-hidden="true" data-kp-sv-rv data-from="hero" style="transition-delay:40ms">
              <span class="block h-px w-12 origin-right bg-gradient-to-r from-transparent to-kp-gold/75 md:w-16"></span><span class="h-1.5 w-1.5 rotate-45 rounded-[2px] bg-kp-gold/90 shadow-[0_0_12px_rgba(201,169,98,0.45)]"></span><span class="block h-px w-12 origin-left bg-gradient-to-l from-transparent to-kp-gold/75 md:w-16"></span>
            </div>
            <p class="kp-sv-rv mt-6 font-sans text-[10px] font-semibold uppercase tracking-[0.34em] text-white/60 md:text-[11px]" data-kp-sv-rv data-from="hero" style="transition-delay:120ms">{{ content('savp.hero_kicker') }}</p>
            <h1 id="sav-title" class="kp-sv-rv mt-3 max-w-[14ch] font-serif text-[clamp(2.35rem,6vw,4rem)] font-medium leading-[1.02] tracking-[-0.025em] text-white [text-shadow:0_2px_40px_rgba(0,0,0,0.35)] md:max-w-[18ch]" data-kp-sv-rv data-from="hero" style="transition-delay:200ms">{{ $c('hero_title') }}</h1>
            <p class="kp-sv-rv mt-5 max-w-lg text-[15px] leading-[1.65] text-white/80 md:text-base md:leading-relaxed" data-kp-sv-rv data-from="hero" style="transition-delay:280ms;text-wrap:pretty">{{ $c('hero_text') }}</p>
            <div class="kp-sv-rv mt-10 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:gap-4" data-kp-sv-rv data-from="hero" style="transition-delay:360ms">
              <a href="{{ $tel }}" class="{{ $btnPrimary }}">{{ $c('btn_call') }}</a>
              <a href="#sav-atelier" class="{{ $btnGhost }}">{{ $c('btn_workshop') }}</a>
            </div>
          </div>
          <aside class="kp-sv-rv rounded-2xl border border-white/[0.12] bg-black/45 p-6 shadow-[0_28px_80px_-32px_rgba(0,0,0,0.9)] ring-1 ring-white/[0.08] backdrop-blur-xl sm:p-7 lg:mb-2" data-kp-sv-rv data-from="hero" style="transition-delay:440ms">
            <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-kp-gold/90">{{ $c('aside_kicker') }}</p>
            <p class="mt-4 font-serif text-2xl tracking-tight text-white md:text-[1.65rem]">{{ $c('aside_title') }}</p>
            <ul class="mt-6 flex flex-col gap-4 border-t border-white/10 pt-6">
              <li><p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-white/40">{{ $c('phone_label') }}</p><a href="{{ $tel }}" class="mt-1.5 inline-block font-sans text-[15px] text-white/90 transition hover:text-kp-gold">{{ content('savp.phone') }}</a></li>
              <li><p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-white/40">{{ $c('email_label') }}</p><a href="{{ cms_url(content('savp.email_link')) }}" class="mt-1.5 inline-block font-sans text-[15px] text-white/90 transition hover:text-kp-gold">{{ content('savp.email') }}</a></li>
            </ul>
            <div class="mt-6 grid grid-cols-2 gap-3">
              @foreach(collect(content('savp.stats')) as $st)
              <div class="rounded-xl border border-white/[0.08] bg-white/[0.04] px-3 py-3 text-center"><p class="font-serif text-xl text-kp-gold md:text-2xl">{{ $st['value'] ?? '' }}</p><p class="mt-1 text-[9px] font-semibold uppercase tracking-[0.2em] text-white/45">{{ $fe($st) }}</p></div>
              @endforeach
            </div>
          </aside>
        </div>
      </div>
    </section>

    {{-- Bandeau --}}
    <div class="relative">
      {!! cms_edit(route('admin.sav').'#bandeau', 'Modifier le bandeau', '.3rem') !!}
      <div class="relative overflow-hidden border-y border-white/[0.08] bg-kp-surface/60 py-4" aria-hidden="true">
        <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-16 bg-gradient-to-r from-kp-surface/90 to-transparent sm:w-24"></div>
        <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-16 bg-gradient-to-l from-kp-surface/90 to-transparent sm:w-24"></div>
        <div class="kp-marquee-track flex w-max gap-10 whitespace-nowrap px-4">
          @foreach($marquee->concat($marquee) as $v)<span class="inline-flex items-center gap-10 text-[10px] font-semibold uppercase tracking-[0.32em] text-white/35">{{ $v }}<span class="h-1 w-1 rounded-full bg-kp-gold/45"></span></span>@endforeach
        </div>
      </div>
    </div>

    {{-- Piliers --}}
    <section class="relative overflow-hidden border-b border-white/[0.08] bg-kp-surface/40" aria-label="{{ $c('pil_aria') }}">
      {!! cms_edit(route('admin.sav').'#piliers', 'Modifier cette section', '1rem') !!}
      <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_85%_55%_at_50%_-8%,rgba(201,169,98,0.07),transparent_58%)]" aria-hidden="true"></div>
      <div class="{{ $pad }}">
        <div class="kp-sv-rv" data-kp-sv-rv>{!! $intro($c('pil_label'), '<span class="text-kp-gold/90">'.e($c('pil_title1')).'</span> <span class="text-white/95">'.e($c('pil_title2')).'</span>', $c('pil_text')) !!}</div>
        <ul class="mt-12 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:mt-14 lg:grid-cols-4">
          @foreach(collect(content('savp.pillars'))->values() as $i => $p)
          <li class="kp-sv-rv group relative flex h-full flex-col overflow-hidden rounded-2xl border border-white/10 bg-kp-elevated/50 p-5 shadow-[0_22px_56px_-32px_rgba(0,0,0,0.92)] ring-1 ring-white/5 backdrop-blur-md hover:border-kp-gold/30 hover:bg-kp-elevated/[0.65] hover:shadow-[0_28px_64px_-28px_rgba(201,169,98,0.14)] sm:p-6" data-kp-sv-rv data-from="pillar" style="transition-delay:{{ 60 + $i * 80 }}ms">
            <div class="pointer-events-none absolute inset-0 opacity-[0.55] transition duration-300 group-hover:opacity-100" aria-hidden="true"><div class="absolute inset-0 bg-[radial-gradient(420px_240px_at_50%_-10%,rgba(201,169,98,0.14),transparent_72%)]"></div></div>
            <div class="relative flex items-start justify-between gap-3">
              <span class="flex h-10 w-10 items-center justify-center rounded-xl border border-kp-gold/25 bg-kp-gold/10 text-kp-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-[18px] w-[18px]" aria-hidden="true">{!! $icons[$p['icon'] ?? ''] ?? $icons['shield'] !!}</svg></span>
              <span class="font-sans text-[10px] font-semibold tabular-nums tracking-[0.22em] text-kp-gold/75 md:text-[11px]">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
            </div>
            <p class="relative mt-4 font-serif text-base font-medium leading-snug tracking-[-0.015em] text-white/95 md:text-[1.05rem]">{{ $li($p, 'title') }}</p>
            <p class="relative mt-2 flex-1 text-[13px] leading-relaxed text-kp-muted" style="text-wrap:pretty">{{ $li($p, 'body') }}</p>
          </li>
          @endforeach
        </ul>
      </div>
    </section>

    {{-- Atelier --}}
    <section id="sav-atelier" class="relative scroll-mt-28 overflow-hidden bg-kp-bg" aria-labelledby="sav-atelier-title">
      {!! cms_edit(route('admin.sav').'#atelier', 'Modifier cette section', '1rem') !!}
      <div class="{{ $pad }}">
        <div class="kp-sv-rv" data-kp-sv-rv>{!! $intro($c('ws_label'), e($c('ws_title')), $c('ws_text'), 'sav-atelier-title') !!}</div>
        <div class="kp-sv-rv mt-12 grid gap-4 md:mt-16 md:grid-cols-3 md:grid-rows-2 md:gap-5" data-kp-sv-rv style="transition-delay:100ms">
          @foreach($gallery as $i => $g)
          @if($i === 0)
          <figure class="group relative overflow-hidden rounded-2xl border border-white/10 bg-kp-elevated/40 md:col-span-2 md:row-span-2">
            <div class="relative aspect-[16/10] w-full md:aspect-auto md:h-full md:min-h-[480px]">
              <img src="{{ cms_asset($g['image']) }}" alt="{{ $li($g, 'alt') }}" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover transition-transform duration-1000 ease-out group-hover:scale-[1.04] motion-reduce:transition-none">
              <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/85 via-black/15 to-transparent"></div>
              <span class="absolute left-5 top-5 inline-flex items-center gap-2 rounded-full border border-white/15 bg-black/45 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.24em] text-white/85 backdrop-blur-md sm:left-6 sm:top-6"><span class="h-1 w-1 rounded-full bg-kp-gold/95"></span>{{ $li($g, 'tag') }}</span>
              <figcaption class="absolute inset-x-0 bottom-0 p-6 md:p-8"><p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-kp-gold/95">{{ $li($g, 'title') }}</p><p class="mt-2 max-w-md text-[13px] leading-relaxed text-white/70 md:text-[14px]">{{ $li($g, 'body') }}</p></figcaption>
            </div>
          </figure>
          @else
          <figure class="group relative overflow-hidden rounded-2xl border border-white/10 bg-kp-elevated/40">
            <div class="relative aspect-[4/3] w-full md:aspect-auto md:h-full md:min-h-[230px]">
              <img src="{{ cms_asset($g['image']) }}" alt="{{ $li($g, 'alt') }}" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover transition-transform duration-1000 ease-out group-hover:scale-[1.05] motion-reduce:transition-none">
              <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
              <span class="absolute left-5 top-5 inline-flex items-center gap-2 rounded-full border border-white/15 bg-black/45 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.24em] text-white/85 backdrop-blur-md"><span class="h-1 w-1 rounded-full bg-kp-gold/95"></span>{{ $li($g, 'tag') }}</span>
              <figcaption class="absolute inset-x-0 bottom-0 p-5"><p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-kp-gold/95">{{ $li($g, 'title') }}</p><p class="mt-1.5 font-serif text-lg font-medium text-white">{{ $li($g, 'body') }}</p></figcaption>
            </div>
          </figure>
          @endif
          @endforeach
        </div>
      </div>
    </section>

    {{-- Prestations --}}
    <section class="relative overflow-hidden border-y border-white/[0.08] bg-kp-surface/35" aria-labelledby="sav-services-title">
      {!! cms_edit(route('admin.sav').'#prestations', 'Modifier cette section', '1rem') !!}
      <div class="{{ $pad }}">
        <div class="grid gap-12 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] lg:items-start lg:gap-16 xl:gap-20">
          <div class="kp-sv-rv" data-kp-sv-rv data-from="left">{!! $intro($c('srv_label'), e($c('srv_title')), $c('srv_text'), 'sav-services-title', false) !!}</div>
          <ul class="grid gap-4 sm:grid-cols-2">
            @foreach(collect(content('savp.services'))->values() as $i => $s)
            <li class="kp-sv-rv group rounded-2xl border border-white/10 bg-kp-elevated/40 p-5 hover:border-kp-gold/25 hover:bg-kp-elevated/[0.55] sm:p-6" data-kp-sv-rv data-from="right" style="transition-delay:{{ 80 + $i * 70 }}ms">
              <div class="flex items-start gap-3"><span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-kp-gold shadow-[0_0_8px_rgba(201,169,98,0.5)]" aria-hidden="true"></span>
                <div><p class="font-serif text-[1.05rem] tracking-tight text-white md:text-lg">{{ $li($s, 'title') }}</p><p class="mt-2 text-[13px] leading-relaxed text-white/55 md:text-[14px]">{{ $li($s, 'body') }}</p></div></div>
            </li>
            @endforeach
          </ul>
        </div>
      </div>
    </section>

    {{-- Parcours --}}
    <section class="relative overflow-hidden bg-kp-bg" aria-labelledby="sav-process-title">
      {!! cms_edit(route('admin.sav').'#parcours', 'Modifier cette section', '1rem') !!}
      <div class="{{ $pad }}">
        <div class="kp-sv-rv" data-kp-sv-rv>{!! $intro($c('proc_label'), e($c('proc_title')), $c('proc_text'), 'sav-process-title') !!}</div>
        <ol class="relative mt-12 grid gap-5 md:mt-16 md:grid-cols-2 xl:grid-cols-4">
          <div class="pointer-events-none absolute left-0 right-0 top-11 hidden h-px bg-gradient-to-r from-transparent via-kp-gold/35 to-transparent xl:block" aria-hidden="true"></div>
          @foreach(collect(content('savp.steps'))->values() as $i => $s)
          <li class="kp-sv-rv relative rounded-2xl border border-white/10 bg-kp-elevated/35 p-6 backdrop-blur-sm sm:p-7" data-kp-sv-rv style="transition-delay:{{ 100 + $i * 80 }}ms">
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-kp-gold/30 bg-kp-gold/10 font-sans text-[11px] font-semibold tracking-[0.12em] text-kp-gold">{{ $s['n'] ?? '' }}</span>
            <p class="mt-5 font-serif text-xl tracking-tight text-white">{{ $li($s, 'title') }}</p>
            <p class="mt-3 text-[13px] leading-relaxed text-white/55 md:text-[14px]">{{ $li($s, 'body') }}</p>
          </li>
          @endforeach
        </ol>
      </div>
    </section>

    {{-- CTA --}}
    <section id="sav-contact" class="relative isolate scroll-mt-28 overflow-hidden bg-kp-bg pb-12 md:pb-16" aria-labelledby="sav-cta-title">
      {!! cms_edit(route('admin.sav').'#fin', 'Modifier cette section', '1rem') !!}
      <div class="absolute inset-0 -z-10"><img src="{{ cms_asset(content('savp.cta_image')) }}" alt="" aria-hidden="true" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover object-center opacity-20"></div>
      <div class="absolute inset-0 -z-10 bg-gradient-to-b from-kp-bg via-kp-bg/[0.92] to-kp-bg"></div>
      <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(900px_520px_at_50%_40%,rgba(201,169,98,0.16),transparent_60%)]"></div>
      <div class="{{ $pad }} !pb-24 sm:!pb-28">
        <div class="kp-sv-rv relative overflow-hidden rounded-3xl border border-kp-gold/20 bg-black/40 p-8 shadow-[0_40px_120px_-48px_rgba(0,0,0,0.95),inset_0_1px_0_rgba(255,255,255,0.06)] ring-1 ring-white/[0.08] backdrop-blur-xl sm:p-12 md:p-16 lg:p-20" data-kp-sv-rv>
          <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(600px_320px_at_50%_0%,rgba(201,169,98,0.12),transparent_70%)]"></div>
          <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold/50 to-transparent"></div>
          <div class="relative text-center">
            <p class="{{ $eyebrow }} justify-center">{{ $c('cta_kicker') }}</p>
            <h2 id="sav-cta-title" class="mt-5 font-serif text-[clamp(1.85rem,4.5vw,3.25rem)] font-normal leading-[1.08] tracking-tight text-white">{{ $c('cta_title1') }}<span class="block bg-gradient-to-r from-amber-200 via-kp-gold to-white/85 bg-clip-text text-transparent">{{ $c('cta_title2') }}</span></h2>
            <p class="mx-auto mt-6 max-w-2xl text-[15px] leading-relaxed text-white/65 md:text-[16px]" style="text-wrap:pretty">{{ $c('cta_text') }}</p>
            <div class="mt-10 flex flex-col items-stretch justify-center gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-center sm:gap-4">
              <a href="{{ $tel }}" class="{{ $btnPrimary }} w-full sm:w-auto">{{ content('savp.phone') }}</a>
              {{-- Sans JavaScript : lien e-mail ; avec JavaScript : ouvre le formulaire (preventDefault seulement si la fenêtre s'ouvre) --}}
              <a href="{{ cms_url(content('savp.email_link')) }}" role="button" aria-haspopup="dialog" data-kp-sav-write @click="if (openModal()) $event.preventDefault()" class="{{ $btnGhost }} w-full sm:w-auto">{{ $c('btn_write') }}</a>
              <a href="{{ cms_url(content('savp.contact_url')) }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-full border border-white/10 px-8 py-3.5 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/60 transition duration-300 hover:border-white/20 hover:text-white/85 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/55 sm:w-auto">{{ $c('btn_contact') }}</a>
            </div>
            <div class="mt-12 flex flex-wrap items-center justify-center gap-x-6 gap-y-3 text-[10px] uppercase tracking-[0.28em] text-white/[0.38]">
              @foreach(collect(content('savp.badges'))->map($fe)->filter()->values() as $i => $b)@if($i)<span aria-hidden="true" class="h-1 w-1 rounded-full bg-kp-gold/40"></span>@endif<span>{{ $b }}</span>@endforeach
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</main>

{{-- EcrireSavModal --}}
<template x-teleport="body">
<div x-show="open" x-cloak x-trap.inert.noscroll="open" class="kp-sv-modal fixed inset-0 z-[80] flex items-end justify-center overflow-y-auto p-0 sm:items-center sm:p-6 md:p-10" role="dialog" aria-modal="true" aria-labelledby="ecrire-sav-title" @click.self="close()"
     x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
  <div class="absolute inset-0 bg-black/[0.82] backdrop-blur-md" aria-hidden="true" @click="close()"></div>
  <div tabindex="-1" class="relative z-10 my-auto w-full max-w-3xl overflow-hidden rounded-t-2xl border border-white/[0.12] bg-[#080808] shadow-[0_32px_100px_rgba(0,0,0,0.65)] outline-none sm:rounded-2xl">
    <div class="flex items-start justify-between gap-4 border-b border-white/10 px-6 py-5 sm:px-10 sm:py-6">
      <div>
        <p class="font-sans text-[10px] font-semibold uppercase tracking-[0.28em] text-kp-gold/85">{{ $c('f_kicker') }}</p>
        <h2 id="ecrire-sav-title" class="mt-1 font-serif text-2xl text-white">{{ $c('btn_write') }}</h2>
        <p class="mt-2 font-sans text-sm text-white/50">{{ $c('f_intro') }}</p>
      </div>
      <button type="button" @click="close()" class="shrink-0 rounded-full p-2 text-white/60 transition hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/55" aria-label="{{ $c('f_close_aria') }}"><span class="block text-2xl leading-none" aria-hidden="true">×</span></button>
    </div>
    <div x-show="success" role="status" class="px-6 py-10 text-center sm:px-10 sm:py-12">
      <p class="font-serif text-2xl text-white">{{ $c('f_ok_title') }}</p>
      <p class="mx-auto mt-4 max-w-sm text-[14px] leading-relaxed text-white/50">{{ $c('f_ok_text') }}</p>
      <button type="button" @click="close()" class="mt-8 rounded-full border border-white/15 px-8 py-3 font-sans text-[11px] font-semibold uppercase tracking-[0.22em] text-white/70 transition hover:border-white/25 hover:text-white">{{ $c('f_close') }}</button>
    </div>
    <form x-show="!success" @submit.prevent="submit()" method="POST" action="{{ route('sav.store') }}" class="space-y-5 px-6 py-6 sm:space-y-6 sm:px-10 sm:py-8" novalidate>
      @csrf
      <div><label for="ecrire-sav-name" class="{{ $label }}">{{ $c('f_name') }} <span class="text-kp-gold/90">*</span></label>
        <input id="ecrire-sav-name" name="name" type="text" autocomplete="name" maxlength="120" x-model="f.name" @input="error=null" placeholder="Jean Kouassi" class="{{ $input }}"></div>
      <div><label for="ecrire-sav-contact" class="{{ $label }}">{{ $c('f_contact') }} <span class="text-kp-gold/90">*</span></label>
        <input id="ecrire-sav-contact" name="contact" type="text" autocomplete="tel" maxlength="160" x-model="f.contact" @input="error=null" placeholder="{{ $c('f_contact_ph') }}" class="{{ $input }}"></div>
      <div><label for="ecrire-sav-modele" class="{{ $label }}">{{ $c('f_model') }} <span class="text-kp-gold/90">*</span></label>
        <select id="ecrire-sav-modele" name="model" x-model="f.model" @change="error=null" class="{{ $input }} kp-sv-select cursor-pointer appearance-none pr-10">
          <option value="" disabled>{{ $c('f_model_ph') }}</option>
          @foreach($models as $m)<option value="{{ $m }}">{{ $m }}</option>@endforeach
          <option value="Autre">{{ $c('f_other') }}</option>
        </select></div>
      <div><label for="ecrire-sav-panne" class="{{ $label }}">{{ $c('f_issue') }} <span class="text-kp-gold/90">*</span></label>
        <textarea id="ecrire-sav-panne" name="issue" rows="3" maxlength="3000" x-model="f.issue" @input="error=null" placeholder="{{ $c('f_issue_ph') }}" class="{{ $input }} min-h-[96px] resize-y"></textarea></div>
      <div><label for="ecrire-sav-localisation" class="{{ $label }}">{{ $c('f_loc') }} <span class="text-kp-gold/90">*</span></label>
        <input id="ecrire-sav-localisation" name="location" type="text" autocomplete="address-level2" maxlength="160" x-model="f.location" @input="error=null" placeholder="{{ $c('f_loc_ph') }}" class="{{ $input }}"></div>
      <p x-show="error" x-text="error" role="alert" class="rounded-xl border border-red-400/25 bg-red-950/35 px-4 py-3 text-sm text-red-200/95"></p>
      <div class="flex flex-col gap-4 border-t border-white/[0.07] pt-6 sm:flex-row sm:items-center sm:justify-between">
        <button type="submit" :disabled="sending" class="w-full rounded-full bg-kp-gold px-8 py-3.5 font-sans text-[11px] font-semibold uppercase tracking-[0.22em] text-black shadow-[0_16px_40px_-12px_rgba(201,169,98,0.45)] transition-colors duration-300 hover:bg-[#d4b56e] disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto" x-text="sending ? @js($c('f_sending')) : @js($c('f_submit'))">{{ $c('f_submit') }}</button>
        <p class="text-center text-[11px] leading-relaxed text-white/30 sm:text-right">{{ $c('f_privacy') }}</p>
      </div>
    </form>
  </div>
</div>
</template>
</div>
<script>
function kpSav(){const blank=()=>({name:'',contact:'',model:'',issue:'',location:''});return{open:false,success:false,sending:false,error:null,f:blank(),
 openModal(){try{this.success=false;this.error=null;this.open=true;return !!document.querySelector('.kp-sv-modal')}catch(e){this.open=false;return false}},
 close(){this.open=false},
 async submit(){this.error=null;const d={};for(const k in this.f)d[k]=String(this.f[k]).trim();
  const M=@js(['name' => $c('e_name'), 'contact' => $c('e_contact'), 'model' => $c('e_model'), 'issue' => $c('e_issue'), 'location' => $c('e_loc'), 'server' => $c('e_server'), 'net' => $c('e_network')]);
  for(const k of ['name','contact','model','issue','location']){if(!d[k]){this.error=M[k];return}}
  this.sending=true;
  try{const r=await fetch(@js(route('sav.store')),{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':@js(csrf_token())},body:JSON.stringify(d)});
   const j=await r.json().catch(()=>null);if(!r.ok){this.error=(j&&j.error)||M.server;return}this.success=true;this.f=blank()}
  catch(e){this.error=M.net}finally{this.sending=false}}}}
(function(){var els=document.querySelectorAll('[data-kp-sv-rv]');
if(!('IntersectionObserver' in window)||matchMedia('(prefers-reduced-motion: reduce)').matches){els.forEach(function(e){e.classList.add('is-in')});return}
var io=new IntersectionObserver(function(en){en.forEach(function(x){if(x.isIntersecting){x.target.classList.add('is-in');io.unobserve(x.target)}})},{threshold:0.15});
els.forEach(function(e){io.observe(e)});})();
</script>
@endsection
