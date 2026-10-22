{{-- ECO KPANDJI — port de app/ecologie/page.tsx + components/kp/EcologyPageContent.tsx (+ Reveal.tsx, kp-hero-zoom / kp-marquee de globals.css).
     Accent émeraude comme dans la source. Contenu : Admin › Eco-Kpandji (/admin/ecologie) — clés CMS « eco.* » --}}
@extends('layouts.app')
@php($en = app()->getLocale() === 'en')
@php($c = fn ($k) => $en ? (content('eco.'.$k.'_en') ?: content('eco.'.$k)) : content('eco.'.$k))
@php($li = fn ($row, $f) => $en ? (($row[$f.'_en'] ?? '') ?: ($row[$f] ?? '')) : ($row[$f] ?? ''))
@php($fe = fn ($row) => $en ? (($row['en'] ?? '') ?: ($row['fr'] ?? '')) : ($row['fr'] ?? ''))
@php($commitments = collect(content('eco.commitments'))->map($fe)->filter()->values())
@php($marquee = collect(content('eco.marquee'))->map($fe)->filter()->values())
@php($gallery = collect(content('eco.gallery'))->filter(fn ($g) => filled($g['image'] ?? null))->values())
@php($btnPrimary = 'kp-eco-press group inline-flex items-center justify-center gap-2 rounded-full bg-white px-7 py-3.5 text-[12px] font-semibold uppercase tracking-[0.2em] text-black transition-all duration-300 hover:bg-white/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400/55')
@php($btnGhost = 'group inline-flex items-center justify-center gap-2 rounded-full border border-white/15 bg-white/5 px-7 py-3.5 text-[12px] font-semibold uppercase tracking-[0.2em] text-white/85 backdrop-blur-sm transition-all duration-300 hover:border-emerald-400/45 hover:bg-white/[0.08] hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400/55')
@php($eyebrow = 'inline-flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.28em] text-emerald-400/95')
@php($pad = 'mx-auto max-w-[1280px] px-5 py-16 sm:px-8 sm:py-20 md:py-28 lg:px-12')
@php($h2 = 'font-serif text-[clamp(1.75rem,4.2vw,3rem)] font-normal leading-[1.1] tracking-tight text-white')
@php($arrowR = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-x-0.5" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>')
@php($rv = fn ($d = 0) => 'data-kp-eco-rv'.($d ? ' style="transition-delay:'.$d.'ms"' : ''))
@section('seo_title', content('eco.seo_title'))
@section('meta_description', content('eco.seo_description'))
@push('meta')
<link rel="canonical" href="{{ url('/ecologie') }}">
<meta property="og:title" content="{{ content('eco.og_title') }}">
<meta property="og:description" content="{{ content('eco.og_description') }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="fr_FR">
<meta property="og:url" content="{{ url('/ecologie') }}">
<link rel="preload" as="image" href="{{ cms_asset(content('eco.hero_image')) }}" fetchpriority="high">
@endpush
@section('content')
<style>
[data-kp-eco-rv]{opacity:0;transform:translateY(40px);transition:opacity .75s cubic-bezier(.22,1,.36,1),transform .75s cubic-bezier(.22,1,.36,1)}
[data-kp-eco-rv].is-in{opacity:1;transform:none}
@keyframes kp-cinematic-zoom{from{transform:scale(1) translate3d(0,0,0)}to{transform:scale(1.08) translate3d(0,-1.5%,0)}}
.kp-hero-zoom{animation:kp-cinematic-zoom 22s ease-in-out infinite alternate}
@keyframes kp-eco-marquee{from{transform:translate3d(0,0,0)}to{transform:translate3d(-50%,0,0)}}
.kp-eco-marquee{animation:kp-eco-marquee 36s linear infinite}
.kp-eco-press:hover{transform:scale(1.02)}.kp-eco-press:active{transform:scale(.98)}
.kp-eco-lift:hover{transform:translateY(-2px)}
@media (prefers-reduced-motion:reduce){[data-kp-eco-rv]{opacity:1!important;transform:none!important;transition:none!important}.kp-hero-zoom,.kp-eco-marquee{animation:none!important}.kp-eco-press:hover,.kp-eco-press:active,.kp-eco-lift:hover{transform:none}}
</style>
<div class="-mt-14 min-h-screen bg-kp-bg text-kp-accent sm:-mt-16 md:-mt-[125px]">
<main>
  {{-- HERO --}}
  <section aria-label="{{ content('eco.hero_aria') }}" class="relative isolate flex min-h-[100svh] items-end overflow-hidden pt-[100px] sm:pt-[110px] md:pt-[132px]">
    {!! cms_edit(route('admin.eco').'#hero', 'Modifier cette section', '8.5rem') !!}
    <div class="absolute inset-0 -z-10 overflow-hidden"><img src="{{ cms_asset(content('eco.hero_image')) }}" alt="{{ $c('hero_alt') }}" fetchpriority="high" decoding="async" class="kp-hero-zoom absolute inset-0 h-full w-full object-cover object-center"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-b from-emerald-950/50 via-black/55 to-black/95"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(900px_520px_at_15%_20%,rgba(52,211,153,0.22),transparent_58%),radial-gradient(800px_600px_at_85%_40%,rgba(201,169,98,0.14),transparent_55%),radial-gradient(900px_700px_at_50%_50%,transparent_50%,rgba(0,0,0,0.82)_100%)]"></div>
    <div aria-hidden="true" class="kp-grain pointer-events-none absolute inset-0 -z-10 opacity-[0.32] mix-blend-overlay"></div>
    <div aria-hidden="true" class="pointer-events-none absolute right-8 top-1/2 hidden -translate-y-1/2 lg:block">
      <div class="flex flex-col items-center gap-4"><span class="text-[10px] font-semibold uppercase tracking-[0.4em] text-emerald-300/70 [writing-mode:vertical-rl]">{{ content('eco.hero_kicker') }}</span><span class="h-24 w-px bg-gradient-to-b from-emerald-400/70 via-emerald-400/25 to-transparent"></span></div>
    </div>
    <div class="relative z-10 mx-auto w-full max-w-[1280px] px-5 pb-8 sm:px-8 sm:pb-32 md:pb-36 lg:px-12">
      <div {!! $rv() !!}><p class="{{ $eyebrow }}"><span aria-hidden="true" class="h-px w-10 bg-emerald-400/80 sm:w-16"></span>{{ content('eco.hero_kicker') }}</p></div>
      <div {!! $rv(120) !!}><h1 class="mt-5 max-w-[16ch] font-serif text-[clamp(2rem,7.5vw,5rem)] font-medium leading-[1.04] tracking-[-0.025em] text-white sm:max-w-[22ch]"><span class="block">{{ $c('hero_title1') }}</span><span class="block bg-gradient-to-r from-white via-emerald-100/90 to-emerald-400/85 bg-clip-text text-transparent">{{ $c('hero_title2') }}</span></h1></div>
      <div {!! $rv(200) !!}><p class="mt-6 max-w-2xl text-[15px] leading-relaxed text-white/[0.72] sm:text-[16px] md:text-[17px]" style="text-wrap:pretty">{{ $c('hero_text') }}</p></div>
      <div {!! $rv(280) !!}><div class="mt-8 flex flex-col gap-3 sm:mt-9 sm:flex-row sm:flex-wrap sm:items-center sm:gap-4">
        <a href="{{ cms_url(content('eco.btn_commit_url')) }}" class="{{ $btnPrimary }} w-full sm:w-auto"><span>{{ $c('btn_commit') }}</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-y-0.5" aria-hidden="true"><path d="M12 5v14M5 12l7 7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
        <a href="{{ cms_url(content('eco.btn_practices_url')) }}" class="{{ $btnGhost }} w-full sm:w-auto">{{ $c('btn_practices') }}</a>
      </div></div>
    </div>
  </section>

  {{-- Cible du bouton « Nos engagements » (absente de la source) : liste des priorités --}}
  <span id="engagements" class="block scroll-mt-28" aria-hidden="true"></span>

  {{-- PRIORITÉS — mobile --}}
  <section aria-label="{{ $c('prio_aria') }}" class="relative border-b border-white/[0.08] bg-kp-surface md:hidden">
    {!! cms_edit(route('admin.eco').'#priorites', 'Modifier cette section', '.5rem') !!}
    <div class="mx-auto max-w-[1280px] px-5 py-8">
      <p class="{{ $eyebrow }}"><span aria-hidden="true" class="h-px w-8 bg-emerald-400/60"></span>{{ $c('prio_label') }}</p>
      <ul class="mt-5 grid gap-3">
        @foreach($commitments as $i => $v)<li class="flex items-start gap-3 rounded-xl border border-white/[0.08] bg-kp-elevated/30 px-4 py-3.5"><span class="mt-0.5 font-serif text-sm font-medium text-emerald-400/90">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span><span class="text-[13px] leading-snug text-white/75">{{ $v }}</span></li>@endforeach
      </ul>
    </div>
  </section>

  {{-- PRATIQUES USINE (bandeau) --}}
  <section id="pratiques" aria-label="{{ $c('mq_aria') }}" class="relative scroll-mt-28 overflow-hidden border-y border-white/[0.08] bg-kp-surface py-6 sm:py-7">
    {!! cms_edit(route('admin.eco').'#pratiques', 'Modifier le bandeau', '.3rem') !!}
    <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-16 bg-gradient-to-r from-kp-surface to-transparent sm:w-24"></div>
    <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-16 bg-gradient-to-l from-kp-surface to-transparent sm:w-24"></div>
    <p class="sr-only">{{ $c('mq_sr') }}</p>
    <div class="kp-eco-marquee flex w-max items-center gap-8 whitespace-nowrap text-white/55 sm:gap-12" aria-hidden="true">
      @for($dup = 0; $dup < 2; $dup++)<div class="flex items-center gap-8 sm:gap-12">@foreach($marquee as $v)<span class="flex max-w-[min(90vw,420px)] items-center gap-6 font-serif text-base font-normal tracking-tight sm:max-w-none sm:gap-8 sm:text-xl md:text-2xl"><span>{{ $v }}</span><span class="h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-400/80"></span></span>@endforeach</div>@endfor
    </div>
  </section>

  {{-- PRIORITÉS — ordinateur --}}
  <section aria-label="{{ $c('prio_aria') }}" class="relative isolate hidden overflow-hidden border-b border-white/[0.08] md:block">
    {!! cms_edit(route('admin.eco').'#priorites', 'Modifier cette section', '1rem') !!}
    <div class="absolute inset-0 -z-10"><img src="{{ cms_asset(content('eco.prio_image')) }}" alt="" loading="eager" decoding="async" class="absolute inset-0 h-full w-full object-cover object-center"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-b from-kp-surface/[0.94] via-kp-surface/[0.58] to-kp-surface/[0.52]"></div>
    <div class="relative z-10 {{ $pad }}">
      <div {!! $rv() !!}><p class="{{ $eyebrow }}"><span aria-hidden="true" class="h-px w-8 bg-emerald-400/60"></span>{{ $c('prio_label') }}</p><h2 class="mt-5 {{ $h2 }}">{{ $c('prio_title') }}</h2></div>
      <ul class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-5">
        @foreach($commitments as $i => $v)
        <li class="kp-eco-lift group flex h-full flex-col rounded-2xl border border-white/[0.08] bg-kp-elevated/25 p-6 transition-all duration-500 hover:border-emerald-400/25 hover:bg-kp-elevated/40" {!! $rv($i * 50) !!}>
          <span class="font-serif text-3xl font-medium leading-none text-emerald-400/25 transition-colors duration-500 group-hover:text-emerald-400/40">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
          <p class="mt-4 text-[20px] leading-relaxed text-white/75">{{ $v }}</p>
        </li>
        @endforeach
      </ul>
    </div>
  </section>

  {{-- LEVIERS --}}
  <section aria-label="{{ $c('lev_aria') }}" class="relative isolate overflow-hidden bg-kp-bg">
    {!! cms_edit(route('admin.eco').'#leviers', 'Modifier cette section', '.5rem') !!}
    <div class="absolute inset-0 -z-10"><img src="{{ cms_asset(content('eco.lev_image')) }}" alt="" loading="eager" decoding="async" class="absolute inset-0 h-full w-full object-cover object-center opacity-30"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-b from-kp-bg/[0.92] via-kp-bg/90 to-kp-bg/[0.92]"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(900px_520px_at_50%_40%,rgba(52,211,153,0.1),transparent_58%)]"></div>
    <div class="relative z-10 mx-auto max-w-[1280px] px-5 py-10 sm:px-8 sm:py-12 lg:px-12">
      <div class="grid gap-3 sm:grid-cols-3 sm:gap-4">
        @foreach(collect(content('eco.levers'))->values() as $i => $p)
        <div {!! $rv($i * 50) !!}>
          <a href="{{ cms_url($p['url'] ?? '#engagements') }}" class="kp-eco-lift group flex h-full flex-col rounded-2xl border border-white/[0.08] bg-kp-elevated/20 p-5 transition-all duration-500 hover:border-emerald-400/25 hover:bg-kp-elevated/[0.35] sm:p-6">
            <span class="text-[15px] font-semibold uppercase tracking-[0.14em] text-emerald-400/90">{{ $li($p, 'tag') }}</span>
            <span class="mt-3 font-serif text-lg leading-snug tracking-tight text-white sm:text-2xl">{{ $li($p, 'title') }}</span>
            <span class="mt-4 inline-flex items-center gap-1.5 text-[15px] font-semibold uppercase tracking-[0.2em] text-white/45 transition-colors group-hover:text-emerald-400/85">{{ $c('lev_more') }}<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3 w-3 transition-transform duration-300 group-hover:translate-x-0.5" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
          </a>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- GALERIE --}}
  <section aria-labelledby="terrain-heading" class="relative isolate overflow-hidden bg-kp-bg">
    {!! cms_edit(route('admin.eco').'#galerie', 'Modifier cette section', '1rem') !!}
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(800px_500px_at_50%_0%,rgba(52,211,153,0.08),transparent_60%)]"></div>
    <div class="{{ $pad }}">
      <div {!! $rv() !!}><div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between lg:gap-12">
        <div><p class="{{ $eyebrow }}"><span aria-hidden="true" class="h-px w-8 bg-emerald-400/60"></span>{{ $c('gal_label') }}</p><h2 id="terrain-heading" class="mt-4 sm:mt-5 {{ $h2 }}">{{ $c('gal_title') }}</h2></div>
        <p class="max-w-md text-[14px] leading-relaxed text-white/55 md:text-[15px] lg:max-w-sm lg:pb-1 lg:text-right">{{ $c('gal_text') }}</p>
      </div></div>
      <div {!! $rv(120) !!}><div class="mt-10 grid grid-cols-1 gap-4 sm:mt-12 sm:grid-cols-2 md:mt-16 lg:grid-cols-3 lg:grid-rows-2 lg:gap-5">
        @foreach($gallery as $i => $g)
        @php($big = $i === 0)
        <figure class="group relative overflow-hidden rounded-2xl border border-white/10 bg-kp-elevated/40 {{ $big ? 'sm:col-span-2 lg:col-span-2 lg:row-span-2' : '' }}">
          <div class="relative w-full {{ $big ? 'aspect-[16/10] sm:aspect-[2/1] lg:aspect-auto lg:h-full lg:min-h-[min(52vh,520px)]' : 'aspect-[16/10] sm:aspect-[4/3] lg:aspect-auto lg:h-full lg:min-h-[240px]' }}">
            <img src="{{ cms_asset($g['image']) }}" alt="{{ $li($g, 'alt') }}" loading="eager" decoding="async" class="absolute inset-0 h-full w-full object-cover transition-transform duration-1000 {{ $big ? 'group-hover:scale-[1.04]' : 'group-hover:scale-[1.05]' }} motion-reduce:transition-none">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-gradient-to-t {{ $big ? 'from-black/85 via-black/15' : 'from-black/80 via-black/10' }} to-transparent"></div>
            <span class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-full border border-white/15 bg-black/45 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.24em] text-white/85 backdrop-blur-md {{ $big ? 'sm:left-6 sm:top-6' : 'sm:left-5 sm:top-5' }}"><span class="h-1 w-1 rounded-full bg-emerald-400/95"></span>{{ $li($g, 'tag') }}</span>
            @if($big)
            <figcaption class="absolute inset-x-0 bottom-0 p-5 sm:p-6 md:p-8">@if($li($g, 'kicker'))<p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-emerald-400/95">{{ $li($g, 'kicker') }}</p>@endif<p class="mt-2 font-serif text-xl font-medium text-white sm:text-2xl md:text-3xl">{{ $li($g, 'title') }}</p></figcaption>
            @else
            <figcaption class="absolute inset-x-0 bottom-0 p-4 sm:p-5"><p class="font-serif text-lg font-medium text-white">{{ $li($g, 'title') }}</p></figcaption>
            @endif
          </div>
        </figure>
        @endforeach
      </div></div>
    </div>
  </section>

  {{-- FIN DE PAGE --}}
  <section aria-labelledby="ecologie-cta" class="relative isolate overflow-hidden bg-kp-bg">
    {!! cms_edit(route('admin.eco').'#fin', 'Modifier cette section', '1rem') !!}
    <div class="absolute inset-0 -z-10"><img src="{{ cms_asset(content('eco.cta_image')) }}" alt="" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover object-center opacity-20"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-b from-kp-bg via-kp-bg/[0.88] to-kp-bg"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(900px_520px_at_50%_50%,rgba(52,211,153,0.14),transparent_60%)]"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-emerald-400/45 to-transparent"></div>
    <div class="mx-auto max-w-[1100px] px-5 py-20 text-center sm:px-8 sm:py-24 md:py-32 lg:px-12">
      <div {!! $rv() !!}><div class="mx-auto flex w-full max-w-sm items-center gap-3"><span aria-hidden="true" class="h-px flex-1 bg-gradient-to-r from-transparent via-emerald-400/45 to-emerald-400/20"></span><span aria-hidden="true" class="h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-400/95 shadow-[0_0_14px_rgba(52,211,153,0.4)]"></span><span aria-hidden="true" class="h-px flex-1 bg-gradient-to-l from-transparent via-emerald-400/45 to-emerald-400/20"></span></div></div>
      <div {!! $rv(80) !!}><p class="{{ $eyebrow }} mt-7 justify-center">{{ $c('cta_kicker') }}</p></div>
      <div {!! $rv(140) !!}><h2 id="ecologie-cta" class="mt-5 font-serif text-[clamp(2rem,4.8vw,3.5rem)] font-normal leading-[1.06] tracking-tight text-white">{{ $c('cta_title1') }}<br class="hidden sm:block"><span class="bg-gradient-to-r from-emerald-300 via-emerald-400/90 to-white/85 bg-clip-text text-transparent"> {{ $c('cta_title2') }}</span></h2></div>
      <div {!! $rv(220) !!}><p class="mx-auto mt-6 max-w-2xl text-[15px] leading-relaxed text-white/70 md:text-[16px]" style="text-wrap:pretty">{{ $c('cta_text') }}</p></div>
      <div {!! $rv(300) !!}><div class="mt-10 flex flex-col items-stretch justify-center gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:gap-4">
        <a href="{{ cms_url(content('eco.cta_btn1_url')) }}" class="{{ $btnPrimary }} w-full sm:w-auto"><span>{{ $c('cta_btn1') }}</span>{!! $arrowR !!}</a>
        <a href="{{ cms_url(content('eco.cta_btn2_url')) }}" class="{{ $btnGhost }} w-full sm:w-auto">{{ $c('cta_btn2') }}</a>
        <a href="{{ cms_url(content('eco.cta_btn3_url')) }}" class="inline-flex w-full items-center justify-center rounded-full border border-white/10 px-6 py-3.5 text-[12px] font-semibold uppercase tracking-[0.2em] text-white/60 transition-all duration-300 hover:border-white/20 hover:text-white/85 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400/55 sm:w-auto">{{ $c('cta_btn3') }}</a>
      </div></div>
      <div {!! $rv(380) !!}><div class="mt-14 flex flex-col items-center gap-3 text-[10px] uppercase tracking-[0.32em] text-white/35 sm:flex-row sm:justify-center sm:gap-6">
        @foreach(collect(content('eco.cta_badges'))->map($fe)->filter()->values() as $i => $b)@if($i)<span aria-hidden="true" class="hidden h-1 w-1 rounded-full bg-emerald-400/40 sm:block"></span>@endif<span>{{ $b }}</span>@endforeach
      </div></div>
    </div>
  </section>
</main>
</div>
<script>
(function(){var els=document.querySelectorAll('[data-kp-eco-rv]');
if(!('IntersectionObserver' in window)||matchMedia('(prefers-reduced-motion: reduce)').matches){els.forEach(function(e){e.classList.add('is-in')});return}
var io=new IntersectionObserver(function(en){en.forEach(function(x){if(x.isIntersecting){x.target.classList.add('is-in');io.unobserve(x.target)}})},{threshold:0.12,rootMargin:'0px 0px -40px 0px'});
els.forEach(function(e){io.observe(e)});})();
</script>
@endsection
