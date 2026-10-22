{{-- Kpandji Privilège — port de app/privilege/page.tsx + components/kp/PrestigePrivilege.tsx + PrestigeContactCTA.tsx + PrivilegeFloatingCTA.tsx (+ Reveal.tsx, kp-hero-zoom / kp-scroll-dot de globals.css).
     Contenu : Admin › Kpandji Privilège (/admin/privilege) — clés CMS « priv.* » ; demandes : table privilege_requests --}}
@extends('layouts.app')
@php($en = app()->getLocale() === 'en')
@php($c = fn ($k) => $en ? (content('priv.'.$k.'_en') ?: content('priv.'.$k)) : content('priv.'.$k))
@php($li = fn ($row, $f) => $en ? (($row[$f.'_en'] ?? '') ?: ($row[$f] ?? '')) : ($row[$f] ?? ''))
@php($pt = fn ($row) => $en ? (($row['en'] ?? '') ?: ($row['fr'] ?? '')) : ($row['fr'] ?? ''))
@php($waPath = 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.881 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z')
@php($paths = collect(content('priv.paths'))->values())
@php($points = [collect(content('priv.points1'))->map($pt)->filter()->values(), collect(content('priv.points2'))->map($pt)->filter()->values()])
@php($steps = collect(content('priv.steps'))->values())
@php($pad = 'mx-auto w-full max-w-[1680px] px-5 sm:px-8 lg:px-12 xl:px-16')
@php($eyebrow = 'inline-flex items-center gap-3 font-sans text-[11px] font-semibold uppercase tracking-[0.26em] text-kp-gold/95 sm:text-[12px]')
@php($body = 'font-sans text-[15px] leading-relaxed text-white/60 sm:text-[16px] md:text-[17px]')
@php($bodyLg = 'font-sans text-[16px] leading-relaxed text-white/75 sm:text-[18px] md:text-[19px]')
@php($btnGold = 'group inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-full bg-kp-gold px-7 py-3.5 font-sans text-[12px] font-semibold uppercase tracking-[0.16em] text-black shadow-[0_12px_40px_-12px_rgba(201,169,98,0.55)] transition duration-300 hover:bg-[#d4b56e] active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/55 sm:w-auto sm:px-8 sm:text-[13px]')
@php($btnPrimary = 'group inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-full bg-white px-7 py-3.5 font-sans text-[12px] font-semibold uppercase tracking-[0.16em] text-black transition duration-300 hover:bg-white/90 active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/55 sm:w-auto sm:px-8 sm:text-[13px]')
@php($input = 'w-full min-h-[3.25rem] rounded-xl border border-white/[0.11] bg-black/40 px-4 py-3.5 font-sans text-[15px] text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.04)] placeholder:text-white/30 transition-[border-color,box-shadow] duration-200 focus:border-kp-gold/45 focus:outline-none focus:shadow-[inset_0_1px_0_rgba(255,255,255,0.05),0_0_0_1px_rgba(201,169,98,0.12)] sm:min-h-14 sm:text-[16px]')
@php($label = 'mb-2 block text-left text-[10px] font-semibold uppercase tracking-[0.22em] text-white/45 sm:text-[11px]')
@php($arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-x-0.5" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>')
@php($ctas = '<button type="button" @click="openModal(\'advisor\')" class="'.$btnGold.'"><span>'.e($c('a_label')).'</span>'.$arrow.'</button><button type="button" @click="openModal(\'contact\')" class="'.$btnPrimary.'"><span>'.e($c('c_label')).'</span>'.$arrow.'</button>')
@php($modes = [
  'advisor' => ['title' => $c('a_title'), 'sub' => $c('a_sub'), 'submit' => $c('a_submit'), 'okTitle' => $c('a_ok_title'), 'okText' => $c('a_ok_text')],
  'contact' => ['title' => $c('c_title'), 'sub' => $c('c_sub'), 'submit' => $c('c_submit'), 'okTitle' => $c('c_ok_title'), 'okText' => $c('c_ok_text')],
])
@section('seo_title', content('priv.seo_title'))
@section('meta_description', content('priv.seo_description'))
@push('meta')
<link rel="canonical" href="{{ url('/privilege') }}">
<meta property="og:title" content="{{ content('priv.og_title') }}">
<meta property="og:description" content="{{ content('priv.og_description') }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="fr_FR">
<meta property="og:url" content="{{ url('/privilege') }}">
<link rel="preload" as="image" href="{{ cms_asset(content('priv.hero_image')) }}" fetchpriority="high">
{{-- Plugin focus (x-trap) : doit être chargé avant Alpine (head) ; QR code généré côté navigateur --}}
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
@endpush
@section('content')
<style>
.kp-pv-rv{opacity:0;transform:translateY(40px);transition:opacity .75s cubic-bezier(.22,1,.36,1),transform .75s cubic-bezier(.22,1,.36,1)}
.kp-pv-rv[data-from=left]{transform:translateX(-40px)}.kp-pv-rv[data-from=right]{transform:translateX(40px)}
.kp-pv-rv.is-in{opacity:1;transform:none}
@keyframes kp-cinematic-zoom{from{transform:scale(1) translate3d(0,0,0)}to{transform:scale(1.08) translate3d(0,-1.5%,0)}}
.kp-hero-zoom{animation:kp-cinematic-zoom 22s ease-in-out infinite alternate}
@keyframes kp-scroll-bounce{0%,100%{transform:translateY(0);opacity:.85}50%{transform:translateY(10px);opacity:.35}}
.kp-scroll-dot{animation:kp-scroll-bounce 1.8s ease-in-out infinite}
.kp-pv-hero-img{object-position:58% 38%}@media(min-width:640px){.kp-pv-hero-img{object-position:50% 40%}}
@media (prefers-reduced-motion:reduce){.kp-wa-modal{transition:none!important}.kp-pv-rv{opacity:1!important;transform:none!important;transition:none!important}.kp-hero-zoom,.kp-scroll-dot{animation:none!important}.kp-pv-wa{transition:none!important}}
</style>
<div class="min-h-screen bg-kp-bg font-sans text-kp-accent" x-data="kpPriv()" @keydown.escape.window="if (waOpen) { waOpen = false } else if (open) { close() }">
  {{-- PrivilegeFloatingCTA --}}
  <a href="{{ cms_url(content('priv.whatsapp')) }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $c('wa_aria') }}" aria-haspopup="dialog" @click="if (openWa()) $event.preventDefault()" x-data="{ v: false }" x-init="const f = () => v = window.scrollY > window.innerHeight; f(); window.addEventListener('scroll', f, { passive: true })"
     :class="v ? 'translate-y-0 opacity-100' : 'pointer-events-none translate-y-4 opacity-0'"
     class="kp-pv-wa fixed bottom-6 right-5 z-50 flex items-center gap-2.5 rounded-full border border-emerald-500/30 bg-emerald-950/90 px-4 py-3 font-sans text-white shadow-[0_16px_48px_-12px_rgba(16,185,129,0.45)] backdrop-blur-md transition-all duration-500 hover:scale-[1.03] hover:border-emerald-400/50 sm:bottom-8 sm:right-8 sm:px-5">
    <svg viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-emerald-400" aria-hidden="true"><path d="{{ $waPath }}"/></svg>
    <span class="text-[10px] font-semibold uppercase tracking-[0.2em] sm:text-[11px]">{{ $c('wa_label') }}</span>
  </a>

  <main>
    {{-- 1. HERO --}}
    <section aria-label="Kpandji Privilège" class="relative isolate flex min-h-[100svh] items-end overflow-hidden sm:min-h-[min(88svh,820px)]">
      {!! cms_edit(route('admin.privilege').'#hero', 'Modifier cette section', '6.5rem') !!}
      <div class="absolute inset-0 -z-10"><img src="{{ cms_asset(content('priv.hero_image')) }}" alt="{{ $c('hero_alt') }}" fetchpriority="high" decoding="async" class="kp-pv-hero-img kp-hero-zoom absolute inset-0 h-full w-full object-cover"></div>
      <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-b from-black/55 via-black/40 to-kp-bg"></div>
      <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(700px_420px_at_20%_20%,rgba(201,169,98,0.2),transparent_58%)]"></div>
      <div aria-hidden="true" class="kp-grain pointer-events-none absolute inset-0 -z-10 opacity-[0.26] mix-blend-overlay"></div>
      <div class="relative z-10 w-full {{ $pad }} pb-[max(3.5rem,env(safe-area-inset-bottom))] pt-28 sm:pb-16 sm:pt-32 md:pb-20">
        <div class="kp-pv-rv" data-kp-pv-rv>
          <button type="button" onclick="if(history.length>1){history.back()}else{location.href='{{ url('/') }}'}" class="group inline-flex min-h-10 items-center gap-2 rounded-full border border-white/15 bg-black/35 px-4 py-2 font-sans text-[11px] font-semibold uppercase tracking-[0.18em] text-white/80 backdrop-blur-md transition hover:border-white/25 hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/55">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5 transition-transform duration-300 group-hover:-translate-x-0.5" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>{{ $c('back') }}</span>
          </button>
        </div>
        <p class="kp-pv-rv {{ $eyebrow }} mt-6 sm:mt-7" data-kp-pv-rv style="transition-delay:40ms"><span aria-hidden="true" class="h-px w-7 bg-kp-gold/80 sm:w-12"></span>{{ $c('hero_eyebrow') }}</p>
        <h1 class="kp-pv-rv mt-4 max-w-[10ch] font-serif text-[clamp(2.85rem,13vw,6rem)] font-medium leading-[0.96] tracking-[-0.03em] text-white sm:mt-5 sm:max-w-none" data-kp-pv-rv style="transition-delay:80ms">
          <span class="block">{{ content('priv.hero_brand') }}</span><span class="block text-kp-gold">{{ content('priv.hero_name') }}</span>
        </h1>
        <p class="kp-pv-rv mt-5 max-w-[22rem] sm:mt-6 sm:max-w-lg {{ $bodyLg }}" data-kp-pv-rv style="transition-delay:140ms;text-wrap:pretty">{{ $c('hero_text') }}</p>
        <div class="kp-pv-rv" data-kp-pv-rv style="transition-delay:200ms">
          <div class="mt-8 flex flex-col gap-3 sm:mt-9 sm:flex-row sm:flex-wrap sm:items-center">
            <div class="mt-8 flex flex-col items-stretch gap-3 sm:mt-9 sm:flex-row sm:items-center sm:justify-center">{!! $ctas !!}</div>
          </div>
        </div>
        <div class="kp-pv-rv" data-kp-pv-rv style="transition-delay:280ms">
          <div class="mt-10 hidden items-center gap-3 sm:mt-12 sm:flex">
            <span aria-hidden="true" class="flex h-9 w-5 items-start justify-center rounded-full border border-white/20"><span class="kp-scroll-dot mt-1.5 h-1 w-1 rounded-full bg-kp-gold/90"></span></span>
            <span class="text-[10px] font-semibold uppercase tracking-[0.32em] text-white/45">{{ $c('scroll') }}</span>
          </div>
        </div>
      </div>
    </section>

    {{-- 2. DEUX CHEMINS --}}
    <section id="chemins" aria-labelledby="chemins-heading" class="relative scroll-mt-24 border-t border-white/[0.08] bg-kp-bg">
      {!! cms_edit(route('admin.privilege').'#chemins', 'Modifier cette section', '1rem') !!}
      <div class="{{ $pad }} py-12 sm:py-16 md:py-20 lg:py-24">
        <div class="kp-pv-rv" data-kp-pv-rv>
          <div class="relative mx-auto max-w-3xl text-center">
            <div aria-hidden="true" class="pointer-events-none absolute -inset-x-6 -top-8 bottom-0 mx-auto max-w-md bg-[radial-gradient(ellipse_at_center,rgba(201,169,98,0.14),transparent_72%)] sm:-inset-x-12 sm:max-w-xl"></div>
            <p class="{{ $eyebrow }} relative justify-center"><span aria-hidden="true" class="h-px w-8 bg-gradient-to-r from-transparent to-kp-gold/70 sm:w-12"></span>{{ $c('paths_eyebrow') }}<span aria-hidden="true" class="h-px w-8 bg-gradient-to-l from-transparent to-kp-gold/70 sm:w-12"></span></p>
            <h2 id="chemins-heading" class="relative mt-5 font-serif text-[clamp(2rem,5.5vw,3.5rem)] font-medium leading-[1.02] tracking-[-0.02em] text-white"><span class="block">{{ $c('paths_title1') }}</span><span class="mt-1 block text-kp-gold">{{ $c('paths_title2') }}</span></h2>
            <div aria-hidden="true" class="relative mx-auto mt-7 flex max-w-xs items-center gap-3 sm:mt-8 sm:max-w-sm sm:gap-4">
              <span class="h-px flex-1 bg-gradient-to-r from-transparent via-kp-gold/35 to-kp-gold/55"></span><span class="h-1.5 w-1.5 rotate-45 border border-kp-gold/50 bg-kp-gold/20"></span><span class="h-px flex-1 bg-gradient-to-l from-transparent via-kp-gold/35 to-kp-gold/55"></span>
            </div>
            <p class="relative mx-auto mt-6 max-w-lg sm:mt-7 {{ $bodyLg }}" style="text-wrap:pretty">{{ $c('paths_text') }}</p>
            <div class="relative mt-8 flex flex-col items-center justify-center gap-2.5 sm:mt-9 sm:flex-row sm:gap-0">
              @foreach($paths as $i => $p)
              @if($i > 0)<span aria-hidden="true" class="hidden h-3 w-px bg-white/[0.12] sm:block"></span>@endif
              <p class="font-sans text-[11px] font-semibold uppercase tracking-[0.24em] text-white/50 sm:px-5"><span class="text-kp-gold/80">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span><span aria-hidden="true" class="mx-2.5 text-white/20">·</span>{{ $li($p, 'title') }}</p>
              @endforeach
            </div>
          </div>
        </div>
        <div class="mt-10 grid items-stretch gap-5 sm:mt-12 sm:gap-6 lg:mt-14 lg:grid-cols-2 lg:gap-5">
          @foreach($paths as $i => $p)
          <div class="kp-pv-rv h-full" data-kp-pv-rv data-from="{{ $i % 2 ? 'right' : 'left' }}" style="transition-delay:{{ $i * 80 }}ms">
            <article class="group flex h-full flex-col overflow-hidden border border-white/[0.08] bg-kp-elevated/40">
              <div class="relative aspect-[16/10] w-full shrink-0 overflow-hidden sm:aspect-[16/9] lg:aspect-[16/10]">
                @if(filled($p['image'] ?? null))<img src="{{ cms_asset($p['image']) }}" alt="" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover transition-transform duration-1000 group-hover:scale-[1.04] motion-reduce:transition-none">@endif
                <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-t from-kp-bg/90 via-transparent to-transparent"></div>
              </div>
              <div class="relative flex flex-1 flex-col px-5 pb-7 pt-5 sm:px-7 sm:pb-8 sm:pt-6 lg:px-8 lg:pb-9 lg:pt-7">
                <p class="text-[11px] font-semibold uppercase tracking-[0.26em] text-kp-gold sm:text-[12px]">{{ $li($p, 'option') }}</p>
                <h3 class="mt-2 min-h-[2.2em] font-serif text-[clamp(1.75rem,4vw,2.5rem)] font-medium leading-[1.08] tracking-tight text-white">{{ $li($p, 'title') }}</h3>
                <p class="mt-3 min-h-[4.5em] {{ $body }}">{{ $li($p, 'body') }}</p>
                <div class="mt-5 flex items-end gap-4 border-t border-white/[0.12] pt-5 sm:gap-5">
                  <div><p class="font-serif text-[clamp(2.5rem,7vw,3.25rem)] font-medium leading-none text-kp-gold">{{ $p['highlight'] ?? '' }}</p><p class="mt-1.5 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/55 sm:text-[12px]">{{ $li($p, 'highlight_label') }}</p></div>
                  <div class="mb-1 h-10 w-px bg-white/15 sm:h-11" aria-hidden="true"></div>
                  <div><p class="font-serif text-[clamp(1.5rem,4vw,2rem)] font-medium leading-none text-white/70">{{ $p['secondary'] ?? '' }}</p><p class="mt-1.5 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/40 sm:text-[12px]">{{ $li($p, 'secondary_label') }}</p></div>
                </div>
                <ul class="mt-5 min-h-[6.5em] space-y-2.5 text-[14px] leading-relaxed text-white/70 sm:text-[15px]">
                  @foreach($points[$i] ?? [] as $pnt)<li class="flex gap-2.5"><span aria-hidden="true" class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-kp-gold"></span>{{ $pnt }}</li>@endforeach
                </ul>
              </div>
            </article>
          </div>
          @endforeach
        </div>
      </div>
    </section>

    {{-- 3. PARCOURS --}}
    <section id="parcours" aria-labelledby="parcours-heading" class="relative scroll-mt-24 border-t border-white/[0.08] bg-kp-surface">
      {!! cms_edit(route('admin.privilege').'#parcours', 'Modifier cette section', '1rem') !!}
      <div class="{{ $pad }} py-12 sm:py-16 md:py-20 lg:py-24">
        <div class="kp-pv-rv mx-auto max-w-2xl text-center" data-kp-pv-rv>
          <p class="{{ $eyebrow }} justify-center"><span aria-hidden="true" class="h-px w-6 bg-kp-gold/60 sm:w-8"></span>{{ $c('steps_eyebrow') }}<span aria-hidden="true" class="h-px w-6 bg-kp-gold/60 sm:w-8"></span></p>
          <h2 id="parcours-heading" class="mt-4 font-serif text-[clamp(1.85rem,5vw,3.25rem)] font-medium leading-[1.05] tracking-tight text-white">{{ $c('steps_title') }}</h2>
        </div>
        <ol class="relative mt-10 space-y-0 sm:mt-12 lg:hidden">
          <span aria-hidden="true" class="absolute bottom-2 left-[15px] top-2 w-px bg-gradient-to-b from-kp-gold/50 via-kp-gold/20 to-transparent"></span>
          @foreach($steps as $i => $s)
          <li class="kp-pv-rv relative grid grid-cols-[32px_1fr] gap-4 py-5 first:pt-0 last:pb-0 sm:gap-5 sm:py-6" data-kp-pv-rv style="transition-delay:{{ $i * 50 }}ms">
            <span class="relative z-10 mt-1 flex h-8 w-8 items-center justify-center rounded-full border border-kp-gold/40 bg-kp-surface font-sans text-[11px] font-semibold text-kp-gold">{{ $s['n'] ?? '' }}</span>
            <div><h3 class="font-serif text-xl tracking-tight text-white sm:text-2xl">{{ $li($s, 'title') }}</h3><p class="mt-2 {{ $body }}">{{ $li($s, 'body') }}</p></div>
          </li>
          @endforeach
        </ol>
        <ol class="mt-14 hidden gap-8 lg:grid lg:grid-cols-4">
          @foreach($steps as $i => $s)
          <li class="kp-pv-rv relative" data-kp-pv-rv style="transition-delay:{{ $i * 70 }}ms">
            @if($i < $steps->count() - 1)<span aria-hidden="true" class="pointer-events-none absolute left-[calc(100%+0.5rem)] top-6 h-px w-[calc(100%-1rem)] bg-gradient-to-r from-kp-gold/45 to-transparent"></span>@endif
            <p class="font-serif text-5xl font-medium leading-none text-kp-gold/30 xl:text-6xl">{{ $s['n'] ?? '' }}</p>
            <h3 class="mt-4 font-serif text-2xl tracking-tight text-white xl:text-3xl">{{ $li($s, 'title') }}</h3>
            <p class="mt-3 {{ $body }}">{{ $li($s, 'body') }}</p>
          </li>
          @endforeach
        </ol>
      </div>
    </section>

    {{-- 4. CONTACT --}}
    <section id="contact" aria-labelledby="contact-heading" class="relative scroll-mt-24 border-t border-white/[0.08] bg-kp-bg pb-[max(5rem,env(safe-area-inset-bottom))] sm:pb-0">
      {!! cms_edit(route('admin.privilege').'#contact', 'Modifier cette section', '1rem') !!}
      <div class="{{ $pad }} py-12 sm:py-16 md:py-20 lg:py-24">
        <div class="kp-pv-rv mx-auto max-w-2xl text-center" data-kp-pv-rv>
          <p class="{{ $eyebrow }} justify-center"><span aria-hidden="true" class="h-px w-6 bg-kp-gold/60 sm:w-8"></span>{{ $c('contact_eyebrow') }}<span aria-hidden="true" class="h-px w-6 bg-kp-gold/60 sm:w-8"></span></p>
          <h2 id="contact-heading" class="mt-4 font-serif text-[clamp(1.85rem,5vw,3.25rem)] font-medium leading-[1.08] tracking-tight text-white">{{ $c('contact_title') }}</h2>
          <p class="mx-auto mt-4 max-w-md sm:mt-5 {{ $bodyLg }}">{{ $c('contact_text') }}</p>
          <div class="mt-8 flex flex-col items-stretch gap-3 sm:mt-9 sm:flex-row sm:items-center sm:justify-center">{!! $ctas !!}</div>
          <div class="mt-10 flex flex-col items-center gap-3 border-t border-white/10 pt-8 sm:mt-12 sm:flex-row sm:justify-center sm:gap-8 sm:pt-10">
            <a href="{{ cms_url(content('priv.phone_link')) }}" class="inline-flex min-h-11 items-center text-[15px] text-white/60 transition hover:text-kp-gold sm:text-[16px]">{{ content('priv.phone') }}</a>
            <span aria-hidden="true" class="hidden h-4 w-px bg-white/15 sm:block"></span>
            <a href="{{ cms_url(content('priv.email_link')) }}" class="inline-flex min-h-11 items-center text-[15px] text-white/60 transition hover:text-kp-gold sm:text-[16px]">{{ content('priv.email') }}</a>
          </div>
          <p class="mt-6 font-serif text-[15px] italic text-white/40 sm:text-base">{{ $c('tagline') }}</p>
        </div>
      </div>
    </section>
  </main>


  {{-- Fenêtre WhatsApp (wa.me ne peut pas être affiché en iframe) --}}
  @php($wa = cms_url(content('priv.whatsapp')))
  <template x-teleport="body">
  <div x-show="waOpen" x-cloak x-trap.inert.noscroll="waOpen" class="kp-wa-modal fixed inset-0 z-[210] flex items-end justify-center p-0 sm:items-center sm:p-6" role="dialog" aria-modal="true" aria-labelledby="kp-wa-title" @click.self="waOpen = false"
       x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="absolute inset-0 bg-black/85 backdrop-blur-md" aria-hidden="true" @click="waOpen = false"></div>
    <div class="relative z-10 w-full max-w-md overflow-hidden rounded-t-2xl border border-white/[0.12] bg-[#080808] shadow-[0_32px_100px_rgba(0,0,0,0.65)] sm:rounded-2xl">
      <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold/50 to-transparent"></div>
      <div aria-hidden="true" class="pointer-events-none absolute -top-20 left-1/2 h-40 w-[70%] -translate-x-1/2 rounded-full bg-kp-gold/10 blur-3xl"></div>
      <button type="button" @click="waOpen = false" class="absolute right-3 top-3 z-10 rounded-full p-2 text-white/60 transition hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/55" aria-label="{{ $c('f_close_aria') }}"><span class="block text-2xl leading-none" aria-hidden="true">×</span></button>
      <div class="relative px-6 pb-7 pt-8 text-center sm:px-9 sm:pb-9 sm:pt-10">
        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-kp-gold/35 bg-kp-gold/10 text-kp-gold" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor" class="h-7 w-7"><path d="{{ $waPath }}"/></svg></span>
        <p class="mt-5 font-sans text-[10px] font-semibold uppercase tracking-[0.28em] text-kp-gold/90 sm:text-[11px]">{{ content('priv.f_kicker') }}</p>
        <h2 id="kp-wa-title" class="mt-1.5 font-serif text-[1.65rem] leading-tight text-white sm:text-[2rem]">{{ $c('wa_title') }}</h2>
        <p class="mt-3 font-sans text-[18px] tracking-[0.06em] text-white/80">{{ content('priv.wa_number') }}</p>
        <a href="{{ $wa }}" target="_blank" rel="noopener noreferrer" data-kp-wa-open class="group mt-6 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-full bg-kp-gold px-7 py-3.5 font-sans text-[12px] font-semibold uppercase tracking-[0.16em] text-black shadow-[0_12px_40px_-12px_rgba(201,169,98,0.55)] transition duration-300 hover:bg-[#d4b56e] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/55 focus-visible:ring-offset-2 focus-visible:ring-offset-[#080808]">
          <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true"><path d="{{ $waPath }}"/></svg><span>{{ $c('wa_open') }}</span>
        </a>
        <div class="mt-7 hidden border-t border-white/[0.08] pt-6 sm:block">
          <div data-kp-wa-qr class="mx-auto h-[168px] w-[168px] rounded-xl bg-white p-2.5 [&_svg]:h-full [&_svg]:w-full" role="img" aria-label="QR code {{ $wa }}"></div>
          <p class="mt-3 text-[13px] leading-relaxed text-white/45">{{ $c('wa_qr') }}</p>
        </div>
      </div>
    </div>
  </div>
  </template>
  {{-- PrestigeContactCTA (modale partagée par les deux boutons) --}}
  <template x-teleport="body">
  <div x-show="open" x-cloak class="fixed inset-0 z-[200] flex items-end justify-center overflow-y-auto p-0 sm:items-center sm:p-6 md:p-10" role="dialog" aria-modal="true" aria-labelledby="prestige-contact-title" @click.self="close()">
    <div class="absolute inset-0 bg-black/85 backdrop-blur-md" aria-hidden="true" @click="close()"></div>
    <div x-ref="panel" tabindex="-1" class="relative z-10 my-auto w-full max-w-3xl overflow-hidden rounded-t-2xl border border-white/[0.12] bg-[#080808] shadow-[0_32px_100px_rgba(0,0,0,0.65)] outline-none sm:rounded-2xl lg:max-w-4xl">
      <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold/50 to-transparent"></div>
      <div aria-hidden="true" class="pointer-events-none absolute -top-20 left-1/2 h-40 w-[65%] -translate-x-1/2 rounded-full bg-kp-gold/10 blur-3xl"></div>
      <div class="relative flex items-start justify-between gap-4 border-b border-white/10 px-6 py-5 sm:px-10 sm:py-7 lg:px-12">
        <div>
          <p class="font-sans text-[10px] font-semibold uppercase tracking-[0.28em] text-kp-gold/90 sm:text-[11px]">{{ content('priv.f_kicker') }}</p>
          <h2 id="prestige-contact-title" class="mt-1.5 font-serif text-[1.65rem] leading-tight text-white sm:text-[2rem]" x-text="t.title"></h2>
          <p class="mt-2 max-w-lg font-sans text-[14px] leading-relaxed text-white/55 sm:text-[15px]" x-text="t.sub"></p>
        </div>
        <button type="button" @click="close()" class="shrink-0 rounded-full p-2 text-white/60 transition hover:bg-white/10 hover:text-white" aria-label="{{ $c('f_close_aria') }}"><span class="block text-2xl leading-none" aria-hidden="true">×</span></button>
      </div>
      <div x-show="success" role="status" class="relative px-6 py-12 text-center sm:px-10 sm:py-14 lg:px-12">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-kp-gold/35 bg-kp-gold/10 text-kp-gold sm:h-16 sm:w-16"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6 sm:h-7 sm:w-7" aria-hidden="true"><path d="M5 12l5 5L20 7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        <p class="mt-6 font-serif text-[1.65rem] text-white sm:text-[2rem]" x-text="t.okTitle"></p>
        <p class="mx-auto mt-4 max-w-sm text-[14px] leading-relaxed text-white/55 sm:text-[15px]" x-text="t.okText"></p>
        <button type="button" @click="close()" class="mt-8 rounded-full border border-white/15 px-8 py-3 font-sans text-[11px] font-semibold uppercase tracking-[0.22em] text-white/70 transition hover:border-white/25 hover:text-white">{{ $c('f_close') }}</button>
      </div>
      <form x-show="!success" @submit.prevent="submit()" method="POST" action="{{ route('privilege.store') }}" class="relative space-y-5 px-6 py-6 sm:space-y-6 sm:px-10 sm:py-8 lg:px-12" novalidate>
        @csrf
        <div><label for="prestige-contact-name" class="{{ $label }}">{{ $c('f_name') }} <span class="text-kp-gold/90">*</span></label>
          <input id="prestige-contact-name" name="name" type="text" autocomplete="name" maxlength="120" x-model="f.name" @input="error=null" placeholder="Jean Kouassi" class="{{ $input }}"></div>
        <div class="grid gap-5 md:grid-cols-2">
          <div><label for="prestige-contact-country" class="{{ $label }}">{{ $c('f_country') }} <span class="text-kp-gold/90">*</span></label>
            <input id="prestige-contact-country" name="country" type="text" autocomplete="country-name" maxlength="120" x-model="f.country" @input="error=null" placeholder="{{ $c('f_country_ph') }}" class="{{ $input }}"></div>
          <div><label for="prestige-contact-city" class="{{ $label }}">{{ $c('f_city') }} <span class="text-kp-gold/90">*</span></label>
            <input id="prestige-contact-city" name="city" type="text" autocomplete="address-level2" maxlength="120" x-model="f.city" @input="error=null" placeholder="{{ $c('f_city_ph') }}" class="{{ $input }}"></div>
        </div>
        <div class="grid gap-5 md:grid-cols-2">
          <div><label for="prestige-contact-phone" class="{{ $label }}">{{ $c('f_phone') }} <span class="text-kp-gold/90">*</span></label>
            <input id="prestige-contact-phone" name="phone" type="tel" autocomplete="tel" maxlength="40" x-model="f.phone" @input="error=null" placeholder="+33 …" class="{{ $input }}"></div>
          <div><label for="prestige-contact-email" class="{{ $label }}">{{ $c('f_email') }} <span class="text-kp-gold/90">*</span></label>
            <input id="prestige-contact-email" name="email" type="email" autocomplete="email" inputmode="email" maxlength="160" x-model="f.email" @input="error=null" placeholder="vous@exemple.com" class="{{ $input }}"></div>
        </div>
        <p x-show="error" x-text="error" role="alert" class="rounded-xl border border-red-400/25 bg-red-950/35 px-4 py-3 text-sm text-red-200/95"></p>
        <div class="flex flex-col gap-4 border-t border-white/[0.07] pt-6 sm:flex-row sm:items-center sm:justify-between">
          <button type="submit" :disabled="sending" class="w-full rounded-full bg-kp-gold px-8 py-3.5 font-sans text-[11px] font-semibold uppercase tracking-[0.22em] text-black shadow-[0_16px_40px_-12px_rgba(201,169,98,0.45)] transition-colors duration-300 hover:bg-[#d4b56e] disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto" x-text="sending ? @js($c('f_sending')) : t.submit"></button>
          <p class="text-center text-[11px] leading-relaxed text-white/30 sm:text-right">{{ $c('f_privacy') }}</p>
        </div>
      </form>
    </div>
  </div>
  </template>
</div>
<script>
function kpPriv(){const T=@js($modes);return{waOpen:false,
 openWa(){try{this.waOpen=true;this.$nextTick(()=>{try{const el=document.querySelector('[data-kp-wa-qr]');if(el&&!el.dataset.done&&window.qrcode){const q=qrcode(0,'M');q.addData(@js(cms_url(content('priv.whatsapp'))));q.make();el.innerHTML=q.createSvgTag({cellSize:4,margin:0,scalable:true});el.dataset.done=1}}catch(e){console.warn('QR code indisponible',e)}});return !!document.querySelector('.kp-wa-modal')}catch(e){this.waOpen=false;return false}},
 open:false,success:false,sending:false,error:null,mode:'contact',t:T.contact,f:{name:@js(auth()->user()?->name ?? ''),country:'',city:'',phone:@js(auth()->user()?->phone ?? ''),email:@js(auth()->user()?->email ?? '')},_ov:'',
 openModal(m){this.mode=m;this.t=T[m]||T.contact;this.f={name:@js(auth()->user()?->name ?? ''),country:'',city:'',phone:@js(auth()->user()?->phone ?? ''),email:@js(auth()->user()?->email ?? '')};this.error=null;this.success=false;this.open=true;this._ov=document.body.style.overflow;document.body.style.overflow='hidden';setTimeout(()=>this.$refs.panel&&this.$refs.panel.focus(),50)},
 close(){this.open=false;document.body.style.overflow=this._ov},
 async submit(){this.error=null;const d={};for(const k in this.f)d[k]=String(this.f[k]).trim();
  const M=@js(['name' => $c('e_name'), 'country' => $c('e_country'), 'city' => $c('e_city'), 'phone' => $c('e_phone'), 'email' => $c('e_email'), 'bad' => $c('e_email_bad'), 'server' => $c('e_server'), 'net' => $c('e_network')]);
  for(const k of ['name','country','city','phone','email']){if(!d[k]){this.error=M[k];return}}
  if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(d.email)){this.error=M.bad;return}
  d.source=this.mode;this.sending=true;
  try{const r=await fetch(@js(route('privilege.store')),{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':@js(csrf_token())},body:JSON.stringify(d)});
   const j=await r.json().catch(()=>null);if(!r.ok){this.error=(j&&j.error)||M.server;return}this.success=true}
  catch(e){this.error=M.net}finally{this.sending=false}}}}
(function(){var els=document.querySelectorAll('[data-kp-pv-rv]');
if(!('IntersectionObserver' in window)||matchMedia('(prefers-reduced-motion: reduce)').matches){els.forEach(function(e){e.classList.add('is-in')});return}
var io=new IntersectionObserver(function(en){en.forEach(function(x){if(x.isIntersecting){x.target.classList.add('is-in');io.unobserve(x.target)}})},{threshold:0.14});
els.forEach(function(e){io.observe(e)});})();
</script>
@endsection
