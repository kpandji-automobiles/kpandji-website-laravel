{{-- Fiche modèle /modeles/{slug} — passe design 2 (inspirée de ModeleDetailContent.tsx de kpandji2026 final).
     Données uniquement issues des tables cars / modeles (aucune donnée inventée) ; textes : Contenu du site › « Fiche modèle » (modelp.*). --}}
@extends('layouts.app')
@php($en = app()->getLocale() === 'en')
@php($t = fn ($k) => $en ? (content('modelp.'.$k.'_en') ?: content('modelp.'.$k)) : content('modelp.'.$k))
@php($nm = fn ($s) => str_replace(':name', $car->name, (string) $s))
@php($desc = trim((string) (($en && $m?->description_en) ? $m->description_en : $car->description)))
@php($heroImg = $car->hero_image ?: $car->image)
@php($versions = collect($car->versions ?? [])->filter(fn ($v) => filled($v['version'] ?? null))->values())
@php($vPrices = $versions->pluck('promo_price')->filter()->map(fn ($x) => (int) $x))
@php($kPrice = $car->promo_price ? (int) $car->promo_price : ($vPrices->isNotEmpty() ? $vPrices->min() : ((int) $car->price ?: null)))
@php($isFrom = ! $car->promo_price && $vPrices->unique()->count() > 1)
@php($brochure = $car->brochure ?: ($m?->brochure && file_exists(public_path($m->brochure)) ? $m->brochure : null))
@php($wa = 'https://wa.me/'.preg_replace('/\D/', '', content('modelp.wa_number')).'?text='.rawurlencode($nm($t('wa_message'))))
@php($specs = collect($car->specs ?? [])->map(fn ($rows) => collect($rows)->filter(fn ($v) => filled($v)))->filter(fn ($rows) => $rows->isNotEmpty()))
@php($flat = $specs->flatMap(fn ($rows) => $rows->map(fn ($v, $k) => ['k' => $k, 'v' => $v])->values()))
@php($kf = collect(['/^(moteur|motorisation)$/iu', '/puissance/iu', '/couple/iu', '/(transmission|boîte)/iu', '/vitesse/iu', '/places/iu'])->map(fn ($re) => $flat->first(fn ($r) => preg_match($re, $r['k'])))->filter()->unique('k')->take(4)->values())
@php($highlights = collect($car->highlights ?? [])->filter()->values())
@php($gallery = collect($m?->gallery ?? [])->filter(fn ($g) => filled($g['src'] ?? null) && file_exists(public_path($g['src'])))->map(fn ($g) => ['src' => asset($g['src']), 'alt' => ($g['alt'] ?? '') ?: $car->name, 'tag' => $g['tag'] ?? 'ext']))
@php($extra = collect([$m?->top_image, $m?->bottom_left_image, $m?->bottom_right_image])->filter(fn ($s) => $s && file_exists(public_path($s)))->map(fn ($s) => ['src' => asset($s), 'alt' => $car->name, 'tag' => 'ext']))
@php($gallery = $gallery->concat($extra)->unique('src')->values())
@php($video = $m?->top_video && file_exists(public_path($m->top_video)) ? asset($m->top_video) : null)
@php($fmt = fn ($v) => \App\Models\Car::fcfa($v ? (int) $v : null))
@php($advOn = !in_array(mb_strtolower(trim((string) content('modelp.adv_show'))), ['non', 'no', '0', 'off', 'false'], true))
@php($advItems = collect(content('modelp.adv_items'))->map(fn ($r) => ['icon' => $r['icon'] ?? '', 'txt' => $en ? (($r['en'] ?? '') ?: ($r['fr'] ?? '')) : ($r['fr'] ?? '')])->filter(fn ($r) => filled($r['txt']))->values())
@php($advIco = [
  'shield' => '<path d="M12 3 5 6v5.5c0 4.3 2.9 8.1 7 9.5 4.1-1.4 7-5.2 7-9.5V6l-7-3Z"/><path d="m9 12 2.2 2.2L15.5 10"/>',
  'wrench' => '<path d="M14.7 6.3a4 4 0 0 0 5 5L21 13l-1.4 1.4a5 5 0 0 1-6-.8L7 20.2a1.8 1.8 0 0 1-2.6-2.6L10.8 11a5 5 0 0 1-.8-6L11.4 3.6l1.6 1.3a4 4 0 0 0 1.7 1.4Z"/>',
  'document' => '<rect x="3" y="7" width="18" height="10" rx="2"/><path d="M7 11h4M7 13.5h7"/><circle cx="17" cy="12" r="1.2"/>',
])
@php($showAdv = $advOn && $advItems->isNotEmpty())
@php($showHl = $highlights->isNotEmpty() || mb_strlen($desc) > 200)
@php($showGal = $gallery->count() > 1 || $video)
@php($showWar = filled($car->warranty) || $showAdv)
@php($nav = collect(['apercu' => $t('nav_overview'), 'points-forts' => $showHl ? $t('nav_highlights') : null, 'galerie' => $showGal ? $t('nav_gallery') : null, 'caracteristiques' => $specs->isNotEmpty() ? $t('nav_specs') : null, 'versions' => $versions->isNotEmpty() ? $t('nav_versions') : null, 'garantie' => $showWar ? $t('nav_warranty') : null])->filter())
@php($btnGold = 'kp-md-press inline-flex min-h-[54px] items-center justify-center gap-2.5 rounded-full bg-gradient-to-b from-[#dcc07e] to-kp-gold px-8 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-black shadow-[0_18px_44px_-14px_rgba(201,169,98,0.6),inset_0_1px_0_rgba(255,255,255,0.4)] transition-[transform,filter] duration-300 hover:brightness-110 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/60 focus-visible:ring-offset-2 focus-visible:ring-offset-black')
@php($btnGhost = 'kp-md-press inline-flex min-h-[54px] items-center justify-center gap-2.5 rounded-full border border-white/20 bg-white/[0.04] px-7 py-4 text-[11px] font-semibold uppercase tracking-[0.22em] text-white/90 backdrop-blur-sm transition-colors duration-300 hover:border-kp-gold/60 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/50')
@php($btnLink = 'group inline-flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-kp-gold/90 transition-colors hover:text-[#e7cf8f]')
@php($kick = 'flex items-center gap-3 font-sans text-[10px] font-semibold uppercase tracking-[0.38em] text-kp-gold/85 sm:text-[11px]')
@php($kickLine = '<span class="h-px w-8 bg-kp-gold/70" aria-hidden="true"></span>')
@php($h2 = 'font-serif text-[clamp(2rem,4.4vw,3.4rem)] font-medium leading-[1.02] tracking-[-0.025em] text-kp-accent')
@php($sec = 'relative scroll-mt-[150px] py-20 md:py-28 lg:py-32')
@php($wrap = 'mx-auto max-w-[1480px] px-5 md:px-10')
@php($waIcon = '<svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.8-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.4-.5c.2-.2.2-.3.3-.5.1-.2 0-.4 0-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5 2.5 1 3 .8 3.6.8.6-.1 1.8-.7 2-1.4.2-.7.2-1.3.2-1.4-.1-.2-.3-.3-.6-.4zM12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2c-1.5 0-3-.4-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2z"/></svg>')
@php($arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-x-1" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>')
@php($priceLabel = $isFrom ? $t('price_from') : $t('price_label'))
@section('seo_title', $car->name.content('modelp.seo_suffix'))
@section('meta_description', \Illuminate\Support\Str::limit($desc, 157))
@push('meta')
<link rel="canonical" href="{{ route('cars.show', $car) }}">
<meta property="og:title" content="{{ $car->name.content('modelp.seo_suffix') }}">
<meta property="og:description" content="{{ \Illuminate\Support\Str::limit($desc, 157) }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="fr_FR">
<meta property="og:url" content="{{ route('cars.show', $car) }}">
@if($heroImg)<meta property="og:image" content="{{ asset($heroImg) }}"><meta property="og:image:alt" content="{{ $car->name }}">
<link rel="preload" as="image" href="{{ asset($heroImg) }}" fetchpriority="high">@endif
@endpush
@section('content')
<style>
[data-kp-md-rv]{opacity:0;transform:translateY(34px);transition:opacity .9s cubic-bezier(.22,1,.36,1),transform .9s cubic-bezier(.22,1,.36,1)}
[data-kp-md-rv].is-in{opacity:1;transform:none}
@keyframes kp-md-in{from{opacity:0;transform:translate3d(6%,0,0) scale(.97)}to{opacity:1;transform:none}}
.kp-md-car{animation:kp-md-in 1.4s cubic-bezier(.22,1,.36,1) both}
@keyframes kp-md-glow{0%,100%{opacity:.5}50%{opacity:.85}}
.kp-md-glow{animation:kp-md-glow 8s ease-in-out infinite}
.kp-md-press:hover{transform:translateY(-1px)}.kp-md-press:active{transform:scale(.98)}
.kp-md-tile img{transition:transform 1.1s cubic-bezier(.22,1,.36,1)}.kp-md-tile:hover img{transform:scale(1.05)}
.kp-md-lift{transition:transform .5s cubic-bezier(.22,1,.36,1),border-color .5s}.kp-md-lift:hover{transform:translateY(-4px)}
.kp-md-scroll{scrollbar-width:none}.kp-md-scroll::-webkit-scrollbar{display:none}
@media (prefers-reduced-motion:reduce){[data-kp-md-rv]{opacity:1!important;transform:none!important;transition:none!important}.kp-md-car,.kp-md-glow{animation:none!important}.kp-md-press:hover,.kp-md-press:active,.kp-md-tile:hover img,.kp-md-lift:hover{transform:none!important}html{scroll-behavior:auto!important}}
</style>
<div class="min-h-screen overflow-x-clip bg-[#050505] pb-24 text-kp-accent lg:pb-0" x-data="kpModel(@js($gallery->values()), @js($nav->keys()->values()))" x-init="init()">

  {{-- ===== HERO ===== --}}
  <header id="apercu" class="relative isolate scroll-mt-[150px] overflow-hidden pt-[96px] md:pt-[120px]" aria-labelledby="kp-md-title">
    {!! cms_edit(route('admin.cars.edit', $car), 'Modifier ce modèle', '8.5rem') !!}
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(1200px_620px_at_72%_48%,rgba(201,169,98,0.17),transparent_62%),radial-gradient(900px_500px_at_0%_0%,rgba(255,255,255,0.045),transparent_55%),linear-gradient(180deg,#030303_0%,#0a0a0a_55%,#050505_100%)]"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 opacity-[0.18] bg-[linear-gradient(rgba(255,255,255,0.035)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.035)_1px,transparent_1px)] bg-[length:80px_80px] [mask-image:radial-gradient(ellipse_at_center,black_30%,transparent_75%)]"></div>
    {{-- nom géant en filigrane --}}
    <p aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-[22%] -z-10 select-none overflow-hidden whitespace-nowrap text-center font-serif text-[clamp(5rem,17vw,17rem)] font-medium leading-none tracking-[-0.04em] text-white/[0.025]">{{ $car->name }}</p>
    <div class="{{ $wrap }}">
      <div class="flex items-center justify-between gap-3 pt-3">
        <a href="{{ cms_url(content('modelp.back_url')) }}" class="group inline-flex items-center gap-2 rounded-full border border-white/15 bg-black/40 px-4 py-2 text-[10px] font-semibold uppercase tracking-[0.22em] text-white/80 backdrop-blur-md transition hover:border-kp-gold/50 hover:text-white"><span aria-hidden="true" class="transition-transform group-hover:-translate-x-0.5">←</span>{{ $t('back') }}</a>
        <p class="hidden text-[10px] font-medium uppercase tracking-[0.32em] text-white/40 sm:block">{{ content('modelp.brand') }}</p>
      </div>
      <div class="grid items-center gap-6 pb-12 pt-4 lg:grid-cols-12 lg:gap-8 lg:pb-14 lg:pt-0">
        <div class="relative order-1 lg:order-2 lg:col-span-7 xl:col-span-8">
          <div aria-hidden="true" class="kp-md-glow pointer-events-none absolute bottom-[10%] left-1/2 h-[50%] w-[90%] -translate-x-1/2 rounded-[50%] bg-kp-gold/25 blur-[80px]"></div>
          <div aria-hidden="true" class="pointer-events-none absolute bottom-[8%] left-1/2 h-px w-[88%] -translate-x-1/2 bg-gradient-to-r from-transparent via-kp-gold/60 to-transparent"></div>
          <div aria-hidden="true" class="pointer-events-none absolute bottom-[9%] left-1/2 h-8 w-[70%] -translate-x-1/2 rounded-[50%] bg-black blur-2xl"></div>
          @if($heroImg)
          <img src="{{ asset($heroImg) }}" alt="{{ $car->name }}" loading="eager" fetchpriority="high" decoding="async" class="kp-md-car relative z-10 mx-auto aspect-[16/10] w-full max-w-[1040px] object-contain drop-shadow-[0_50px_60px_rgba(0,0,0,0.8)] lg:scale-[1.08]" data-kp-md-hero>
          @endif
        </div>
        <div class="relative z-10 order-2 lg:order-1 lg:col-span-5 xl:col-span-4" data-kp-md-rv>
          @if($car->category)<p class="{{ $kick }}">{!! $kickLine !!}{{ $car->category }}</p>@endif
          <h1 id="kp-md-title" class="mt-4 break-words font-serif text-[clamp(2.6rem,5vw,4rem)] font-medium leading-[0.95] tracking-[-0.035em] text-white">{{ $car->name }}</h1>
          @if($desc)<p class="mt-5 max-w-[46ch] text-pretty text-[14px] leading-relaxed text-white/60 md:text-[15px]">{{ \Illuminate\Support\Str::limit($desc, 120) }}</p>@endif
          <div class="mt-6 flex flex-wrap items-end gap-x-6 gap-y-2 border-t border-white/10 pt-5" data-kp-md-price>
            <div>
              <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-kp-gold">{{ $priceLabel }}</p>
              <p class="mt-1.5 font-serif text-[clamp(2rem,3.6vw,2.8rem)] font-medium leading-none tracking-[-0.02em] text-white">{{ $kPrice ? $fmt($kPrice) : $t('price_request') }}</p>
            </div>
          </div>
          @if($showAdv)
          <div class="kp-md-adv relative mt-5 overflow-hidden rounded-2xl border border-kp-gold/35 bg-gradient-to-br from-[#17130a] via-black/70 to-black/90 shadow-[0_22px_60px_-28px_rgba(201,169,98,0.55)]" data-kp-md-adv>
            {!! cms_edit('modelp', 'Modifier', '.4rem') !!}
            <span aria-hidden="true" class="pointer-events-none absolute -right-12 -top-12 h-32 w-32 rounded-full bg-kp-gold/15 blur-3xl"></span>
            <span aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold/70 to-transparent"></span>
            <div class="flex items-center gap-3 border-b border-kp-gold/15 bg-gradient-to-r from-kp-gold/[0.14] via-kp-gold/[0.04] to-transparent px-5 py-2">
              <span class="h-1.5 w-1.5 rounded-full bg-kp-gold shadow-[0_0_10px_rgba(201,169,98,0.8)]" aria-hidden="true"></span>
              <p class="bg-gradient-to-r from-[#f1dfa8] via-kp-gold to-[#a8843f] bg-clip-text text-[10px] font-bold uppercase tracking-[0.3em] text-transparent">{{ $t('adv_title') }}</p>
            </div>
            <ul class="relative grid gap-0 px-3 py-2" role="list">
              @foreach($advItems as $a)
              <li class="flex items-center gap-3 rounded-xl px-2 py-1"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-kp-gold/35 bg-kp-gold/10 text-kp-gold"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $advIco[$a['icon']] ?? $advIco['shield'] !!}</svg></span><span class="min-w-0 break-words text-[13px] font-medium leading-snug text-white/90">{{ $a['txt'] }}</span></li>
              @endforeach
            </ul>
          </div>
          @endif
          <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
            <a href="{{ route('essai', ['modele' => $car->slug]) }}" class="{{ $btnGold }}" data-kp-md-test>{{ $t('cta_test') }}</a>
            <a href="{{ $wa }}" target="_blank" rel="noopener" class="{{ $btnGhost }}" data-kp-md-wa>{!! $waIcon !!}{{ $t('cta_wa') }}</a>
          </div>
          @if($brochure)<a href="{{ asset($brochure) }}" target="_blank" rel="noopener" download class="mt-5 {{ $btnLink }}" data-kp-md-pdf><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 4v11m0 0-4-4m4 4 4-4M5 20h14" stroke-linecap="round" stroke-linejoin="round"/></svg>{{ $t('cta_pdf') }}</a>@endif
        </div>
      </div>
    </div>
    {{-- chiffres clés --}}
    @if($kf->isNotEmpty())
    <div class="relative border-y border-white/[0.08] bg-black/50 backdrop-blur-sm">
      <dl class="{{ $wrap }} grid grid-cols-2 lg:grid-cols-{{ $kf->count() }}" aria-label="{{ $t('kf_aria') }}" data-kp-md-kf>
        @foreach($kf as $i => $f)
        <div class="relative px-1 py-6 sm:px-6 md:py-8 {{ $i % 2 ? 'pl-4 sm:pl-6' : '' }} {{ $i > 0 ? 'lg:border-l lg:border-white/[0.08]' : '' }} {{ $i % 2 ? 'border-l border-white/[0.08]' : '' }} {{ $i >= 2 ? 'border-t border-white/[0.08] lg:border-t-0' : '' }}" data-kp-md-rv style="transition-delay:{{ $i * 80 }}ms">
          <dt class="text-[10px] font-semibold uppercase tracking-[0.28em] text-white/40">{{ $f['k'] }}</dt>
          <dd class="mt-2 break-words font-serif text-[clamp(1.15rem,2vw,1.6rem)] leading-tight text-white">{{ $f['v'] }}</dd>
        </div>
        @endforeach
      </dl>
    </div>
    @endif
  </header>

  {{-- ===== SOUS-NAVIGATION ===== --}}
  @if($nav->count() > 1)
  <nav class="sticky top-[56px] z-40 border-b border-white/[0.08] bg-black/80 backdrop-blur-xl sm:top-[64px] md:top-[88px]" aria-label="{{ $t('nav_aria') }}" data-kp-md-subnav>
    <div class="{{ $wrap }} flex items-center gap-6">
      <p class="hidden shrink-0 font-serif text-lg text-white lg:block">{{ $car->name }}</p>
      <ul class="kp-md-scroll -mx-5 flex min-w-0 flex-1 gap-1 overflow-x-auto px-5 md:mx-0 md:px-0 lg:justify-center" role="list">
        @foreach($nav as $id => $lab)
        <li class="shrink-0"><a href="#{{ $id }}" @click.prevent="go('{{ $id }}')" :class="active==='{{ $id }}' ? 'text-white after:scale-x-100' : 'text-white/50 after:scale-x-0'" class="relative block px-3 py-4 text-[10px] font-semibold uppercase tracking-[0.24em] transition-colors after:absolute after:inset-x-3 after:bottom-0 after:h-[2px] after:origin-left after:bg-kp-gold after:transition-transform after:duration-300 hover:text-white" data-kp-md-nav="{{ $id }}">{{ $lab }}</a></li>
        @endforeach
      </ul>
      <a href="{{ route('essai', ['modele' => $car->slug]) }}" class="hidden shrink-0 rounded-full bg-kp-gold px-5 py-2.5 text-[10px] font-bold uppercase tracking-[0.22em] text-black transition hover:brightness-110 lg:inline-flex">{{ $t('cta_test') }}</a>
    </div>
  </nav>
  @endif

  <main class="relative">
    {{-- ===== POINTS FORTS ===== --}}
    @if($showHl)
    <section id="points-forts" class="{{ $sec }}" aria-labelledby="kp-md-hl">
      {!! cms_edit(route('admin.cars.edit', $car), 'Modifier les points forts', '1rem') !!}
      <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(800px_420px_at_10%_20%,rgba(201,169,98,0.08),transparent_60%)]"></div>
      <div class="{{ $wrap }} grid gap-12 lg:grid-cols-12 lg:gap-16">
        <header class="lg:col-span-5" data-kp-md-rv>
          <p class="{{ $kick }}">{!! $kickLine !!}{{ $t('hl_kicker') }}</p>
          <h2 id="kp-md-hl" class="mt-5 {{ $h2 }}">{{ $car->name }}</h2>
          <p class="mt-6 max-w-[48ch] text-pretty text-[15px] leading-7 text-white/50">{{ $t('hl_text') }}</p>
          @if(mb_strlen($desc) > 170)<p class="mt-8 border-l border-kp-gold/40 pl-5 font-serif text-[1.08rem] leading-[1.8] text-kp-accent/85">{{ $desc }}</p>@endif
        </header>
        @if($highlights->isNotEmpty())
        <ol class="grid gap-px self-start overflow-hidden rounded-[28px] border border-white/10 bg-white/[0.07] sm:grid-cols-2 lg:col-span-7" role="list">
          @foreach($highlights as $i => $h)
          <li class="group relative bg-[#0a0a0a] p-7 transition-colors duration-500 hover:bg-[#0f0d08] md:p-9" data-kp-md-rv style="transition-delay:{{ $i * 70 }}ms">
            <span aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px origin-left scale-x-0 bg-gradient-to-r from-kp-gold to-transparent transition-transform duration-700 group-hover:scale-x-100"></span>
            <span class="font-serif text-[2.6rem] leading-none tracking-[-0.06em] text-kp-gold/35" aria-hidden="true">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
            <p class="mt-6 font-serif text-[1.25rem] leading-snug text-white">{{ $h }}</p>
          </li>
          @endforeach
        </ol>
        @endif
      </div>
    </section>
    @endif

    {{-- ===== GALERIE (bento) ===== --}}
    @if($showGal)
    <section id="galerie" class="{{ $sec }} border-t border-white/[0.06] bg-[#080808]" aria-labelledby="kp-md-gal">
      @if($m){!! cms_edit(route('admin.modeles.edit', $m), 'Modifier la galerie', '1rem') !!}@endif
      <div class="{{ $wrap }}">
        <header class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between" data-kp-md-rv>
          <div><p class="{{ $kick }}">{!! $kickLine !!}{{ $t('gal_kicker') }}</p><h2 id="kp-md-gal" class="mt-5 {{ $h2 }}">{{ $t('gal_title') }}</h2>
          <p class="mt-4 max-w-[56ch] text-[15px] leading-relaxed text-white/50">{{ $nm($t('gal_text')) }}</p></div>
          @if($gallery->contains('tag', 'int'))
          <div class="flex shrink-0 gap-1 rounded-full border border-white/10 bg-white/[0.03] p-1" role="group">
            @foreach(['all' => $t('gal_all'), 'ext' => $t('gal_ext'), 'int' => $t('gal_int')] as $k => $lab)
            <button type="button" @click="filter='{{ $k }}'" :aria-pressed="filter==='{{ $k }}'" :class="filter==='{{ $k }}' ? 'bg-kp-gold text-black' : 'text-white/60 hover:text-white'" class="rounded-full px-4 py-2 text-[10px] font-semibold uppercase tracking-[0.2em] transition-colors sm:px-5" data-kp-md-filter="{{ $k }}">{{ $lab }}</button>
            @endforeach
          </div>
          @endif
        </header>
        @if($video)
        <div class="relative mt-12 overflow-hidden rounded-[24px] border border-white/10 bg-black" data-kp-md-rv>
          <video class="aspect-video w-full object-cover md:aspect-[21/9]" src="{{ $video }}" muted loop playsinline controls preload="none" @if($gallery->isNotEmpty()) poster="{{ $gallery->first()['src'] }}" @endif></video>
          <span class="pointer-events-none absolute left-5 top-5 rounded-full border border-white/20 bg-black/50 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.22em] text-white/90 backdrop-blur-md">{{ $t('gal_video') }}</span>
        </div>
        @endif
        @if($gallery->isNotEmpty())
        <div class="mt-4 grid auto-rows-[150px] grid-cols-2 gap-3 sm:auto-rows-[190px] md:mt-5 md:grid-cols-4 md:gap-4 lg:auto-rows-[230px]">
          <template x-for="(g, n) in shown" :key="g.src">
            <button type="button" @click="open(items.indexOf(g))" :class="n===0 ? 'col-span-2 row-span-2' : (n===3 ? 'md:col-span-2' : '')" class="kp-md-tile group relative overflow-hidden rounded-[18px] border border-white/10 bg-black/40 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/60" :aria-label="g.alt+' — '+(items.indexOf(g)+1)+'/'+items.length" data-kp-md-thumb>
              <img :src="g.src" :alt="g.alt" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover">
              <span aria-hidden="true" class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/55 via-transparent to-transparent opacity-60 transition-opacity group-hover:opacity-30"></span>
              <template x-if="n===shown.length-1 && rest>0"><span class="absolute inset-0 flex items-center justify-center bg-black/60 font-serif text-2xl text-white backdrop-blur-[2px]" x-text="@js($t('gal_more')).replace(':n', rest)"></span></template>
            </button>
          </template>
        </div>
        @endif
      </div>
    </section>
    @endif

    {{-- ===== FICHE TECHNIQUE (onglets) ===== --}}
    @if($specs->isNotEmpty())
    <section id="caracteristiques" class="{{ $sec }} border-t border-white/[0.06]" aria-labelledby="kp-md-specs" x-data="{ tab: 0 }">
      {!! cms_edit(route('admin.cars.edit', $car), 'Modifier la fiche technique', '1rem') !!}
      <div class="{{ $wrap }} grid gap-10 lg:grid-cols-12 lg:gap-16">
        <header class="lg:col-span-4" data-kp-md-rv>
          <p class="{{ $kick }}">{!! $kickLine !!}{{ $t('specs_kicker') }}</p>
          <h2 id="kp-md-specs" class="mt-5 {{ $h2 }}">{{ $t('specs_title') }}</h2>
          <div class="kp-md-scroll -mx-5 mt-8 flex gap-2 overflow-x-auto px-5 lg:mx-0 lg:flex-col lg:px-0" role="tablist">
            @foreach($specs->keys() as $i => $group)
            <button type="button" role="tab" :aria-selected="tab==={{ $i }}" @click="tab={{ $i }}" :class="tab==={{ $i }} ? 'border-kp-gold/60 bg-kp-gold/10 text-white' : 'border-white/10 text-white/55 hover:border-white/25 hover:text-white'" class="flex shrink-0 items-center justify-between gap-4 rounded-full border px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.2em] transition-colors lg:rounded-xl lg:py-4" data-kp-md-tab>
              <span>{{ $group }}</span><span class="hidden text-[10px] text-kp-gold/70 lg:inline">{{ str_pad($specs[$group]->count(), 2, '0', STR_PAD_LEFT) }}</span>
            </button>
            @endforeach
          </div>
          @if($brochure)<a href="{{ asset($brochure) }}" target="_blank" rel="noopener" download class="mt-8 {{ $btnLink }}">{{ $t('cta_pdf') }} {!! $arrow !!}</a>@endif
        </header>
        <div class="lg:col-span-8">
          @foreach($specs as $group => $rows)
          <div role="tabpanel" x-show="tab==={{ $loop->index }}" @if($loop->index) style="display:none" @endif x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-2" class="rounded-[28px] border border-white/10 bg-gradient-to-b from-white/[0.04] to-transparent p-2" data-kp-md-panel>
            <p class="px-5 pb-2 pt-5 font-serif text-2xl text-white md:px-7">{{ $group }}</p>
            <dl class="divide-y divide-white/[0.07] px-5 md:px-7">
              @foreach($rows as $k => $v)
              <div class="grid gap-1 py-5 sm:grid-cols-[minmax(0,2fr)_minmax(0,3fr)] sm:gap-6"><dt class="text-[11px] uppercase tracking-[0.2em] text-white/40">{{ $k }}</dt><dd class="break-words text-[15px] text-white/90">{{ $v }}</dd></div>
              @endforeach
            </dl>
          </div>
          @endforeach
        </div>
      </div>
    </section>
    @endif

    {{-- ===== VERSIONS ===== --}}
    @if($versions->isNotEmpty())
    <section id="versions" class="{{ $sec }} border-t border-white/[0.06] bg-[#080808]" aria-labelledby="kp-md-ver">
      {!! cms_edit(route('admin.cars.edit', $car), 'Modifier versions et prix', '1rem') !!}
      <div class="{{ $wrap }}">
        <header data-kp-md-rv><p class="{{ $kick }}">{!! $kickLine !!}{{ $t('versions_kicker') }}</p><h2 id="kp-md-ver" class="mt-5 {{ $h2 }}">{{ $t('versions_title') }}</h2></header>
        <div class="mt-12 grid gap-5 {{ $versions->count() > 1 ? 'md:grid-cols-2 xl:grid-cols-'.min(4, $versions->count()) : 'max-w-xl' }}">
          @foreach($versions as $v)
          <article class="kp-md-lift relative flex flex-col overflow-hidden rounded-[24px] border {{ ($v['promo_price'] ?? null) && (int) $v['promo_price'] === $kPrice ? 'border-kp-gold/45' : 'border-white/10' }} bg-gradient-to-b from-white/[0.05] to-black/40 p-7 hover:border-kp-gold/50" data-kp-md-rv style="transition-delay:{{ $loop->index * 80 }}ms" data-kp-md-version>
            <span aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold/50 to-transparent"></span>
            <p class="font-serif text-[1.4rem] leading-tight text-white">{{ $v['version'] }}</p>
            <dl class="mt-5 space-y-2.5 text-sm">
              @foreach(['motorisation' => 'th_engine', 'boite' => 'th_gearbox', 'transmission' => 'th_drive'] as $f => $lk)
              @if(filled($v[$f] ?? null))<div class="flex justify-between gap-4 border-b border-white/[0.06] pb-2.5"><dt class="text-white/40">{{ $t($lk) }}</dt><dd class="text-right text-white/85">{{ $v[$f] }}</dd></div>@endif
              @endforeach
              @if(!empty($v['maintenance_cost']))<div class="flex justify-between gap-4 border-b border-white/[0.06] pb-2.5"><dt class="text-white/40">{{ $t('th_maint') }}</dt><dd class="text-right text-white/85">{{ $fmt($v['maintenance_cost']) }}</dd></div>@endif
            </dl>
            <div class="mt-auto pt-7">
              <p class="text-[10px] font-semibold uppercase tracking-[0.26em] text-kp-gold">{{ $t('th_price') }}</p>
              <p class="mt-1 font-serif text-[1.9rem] leading-none text-white">{{ $fmt($v['promo_price'] ?? null) }}</p>
              <a href="{{ route('essai', ['modele' => $car->slug]) }}" class="mt-6 {{ $btnLink }}">{{ $t('cta_test') }} {!! $arrow !!}</a>
            </div>
          </article>
          @endforeach
        </div>
      </div>
    </section>
    @endif

    {{-- ===== GARANTIE ===== --}}
    @if($showWar)
    <section id="garantie" class="{{ $sec }} border-t border-white/[0.06]" aria-labelledby="kp-md-war">
      {!! cms_edit('modelp', 'Modifier cette section', '1rem') !!}
      <div class="{{ $wrap }}">
        <div class="relative overflow-hidden rounded-[32px] border border-kp-gold/25 bg-[radial-gradient(900px_400px_at_0%_0%,rgba(201,169,98,0.14),transparent_60%),linear-gradient(180deg,#0e0c07,#060606)] p-8 md:p-14" data-kp-md-rv>
          <span aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold/70 to-transparent"></span>
          <div class="grid gap-10 lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-5">
              <p class="{{ $kick }}">{!! $kickLine !!}{{ $t('warranty_kicker') }}</p>
              <h2 id="kp-md-war" class="mt-5 {{ $h2 }}">{{ $t('warranty_title') }}</h2>
              @if($car->warranty)<p class="mt-6 text-[15px] text-white/60">{{ $t('warranty') }} <span class="text-white">{{ $car->warranty }}</span></p>@endif
            </div>
            @if($showAdv)
            <ul class="grid gap-4 sm:grid-cols-3 lg:col-span-7" role="list">
              @foreach($advItems as $a)
              <li class="rounded-2xl border border-white/10 bg-black/40 p-6"><span class="flex h-12 w-12 items-center justify-center rounded-full border border-kp-gold/40 bg-kp-gold/10 text-kp-gold"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $advIco[$a['icon']] ?? $advIco['shield'] !!}</svg></span><p class="mt-5 font-serif text-lg leading-snug text-white">{{ $a['txt'] }}</p></li>
              @endforeach
            </ul>
            @endif
          </div>
        </div>
      </div>
    </section>
    @endif

    {{-- ===== AUTRES MODÈLES ===== --}}
    @if($others->isNotEmpty())
    <section class="{{ $sec }} border-t border-white/[0.06] bg-[#080808]" aria-labelledby="kp-md-rel">
      {!! cms_edit('modelp', 'Modifier les textes de la fiche', '1rem') !!}
      <div class="{{ $wrap }}">
        <header class="flex items-end justify-between gap-6" data-kp-md-rv><div><p class="{{ $kick }}">{!! $kickLine !!}{{ $t('related_kicker') }}</p><h2 id="kp-md-rel" class="mt-5 {{ $h2 }}">{{ $t('related_title') }}</h2></div>
          <a href="{{ cms_url(content('modelp.back_url')) }}" class="hidden shrink-0 sm:inline-flex {{ $btnLink }}">{{ $t('back') }} {!! $arrow !!}</a></header>
        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          @foreach($others as $o)
          @php($op = $o->promo_price ?: (collect($o->versions ?? [])->pluck('promo_price')->filter()->min() ?: $o->price))
          <a href="{{ route('cars.show', $o) }}" class="kp-md-tile kp-md-lift group relative flex flex-col overflow-hidden rounded-[24px] border border-white/10 bg-[#0b0b0b] hover:border-kp-gold/40 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/60" data-kp-md-rv style="transition-delay:{{ $loop->index * 80 }}ms" data-kp-md-related>
            <div class="relative aspect-[16/10] overflow-hidden bg-[radial-gradient(70%_60%_at_50%_70%,rgba(201,169,98,0.2),transparent_70%)]">
              <span aria-hidden="true" class="absolute bottom-[14%] left-1/2 h-px w-3/4 -translate-x-1/2 bg-gradient-to-r from-transparent via-kp-gold/40 to-transparent"></span>
              @if($o->image)<img src="{{ asset($o->image) }}" alt="{{ $o->name }}" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-contain p-6">@endif
            </div>
            <div class="flex flex-1 flex-col border-t border-white/[0.07] p-6">
              @if($o->category)<p class="text-[10px] uppercase tracking-[0.28em] text-white/40">{{ $o->category }}</p>@endif
              <p class="mt-1.5 font-serif text-[1.6rem] leading-tight text-white">{{ $o->name }}</p>
              <div class="mt-auto flex items-end justify-between gap-3 pt-6">
                <div><p class="text-[10px] uppercase tracking-[0.22em] text-kp-gold">{{ $t('price_label') }}</p><p class="mt-1 font-serif text-xl text-white">{{ $op ? $fmt($op) : $t('price_request') }}</p></div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-white/15 text-white/70 transition-colors group-hover:border-kp-gold group-hover:bg-kp-gold group-hover:text-black" aria-hidden="true">{!! $arrow !!}</span>
              </div>
            </div>
          </a>
          @endforeach
        </div>
      </div>
    </section>
    @endif

    {{-- ===== FIN DE PAGE ===== --}}
    <section class="relative isolate overflow-hidden border-t border-white/[0.06] py-24 md:py-32" data-kp-md-end>
      {!! cms_edit('modelp', 'Modifier cette section', '1rem') !!}
      <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(900px_460px_at_50%_110%,rgba(201,169,98,0.22),transparent_62%),#040404]"></div>
      @if($heroImg)<img src="{{ asset($heroImg) }}" alt="" aria-hidden="true" loading="lazy" decoding="async" class="pointer-events-none absolute bottom-[-12%] left-1/2 -z-10 w-[min(1100px,140%)] max-w-none -translate-x-1/2 object-contain opacity-[0.12] blur-[1px]">@endif
      <div class="relative mx-auto max-w-3xl px-5 text-center" data-kp-md-rv>
        <p class="justify-center {{ $kick }}">{!! $kickLine !!}{{ content('modelp.brand') }}{!! $kickLine !!}</p>
        <h2 class="mt-6 font-serif text-[clamp(2.4rem,6vw,4.6rem)] font-medium leading-[0.98] tracking-[-0.03em] text-white">{{ $car->name }}</h2>
        <p class="mx-auto mt-5 max-w-[50ch] text-[15px] leading-relaxed text-white/55">{{ $nm($t('end_text')) }}</p>
        <p class="mt-8 text-[10px] font-semibold uppercase tracking-[0.3em] text-kp-gold">{{ $kPrice ? $priceLabel : '' }}</p>
        <p class="mt-2 font-serif text-[clamp(1.8rem,3.4vw,2.6rem)] text-white">{{ $kPrice ? $fmt($kPrice) : $t('price_request') }}</p>
        <div class="mt-10 flex flex-col justify-center gap-3 sm:flex-row">
          <a href="{{ route('essai', ['modele' => $car->slug]) }}" class="{{ $btnGold }}">{{ $t('cta_test') }}</a>
          <a href="{{ $wa }}" target="_blank" rel="noopener" class="{{ $btnGhost }}">{!! $waIcon !!}{{ $t('cta_wa') }}</a>
        </div>
        <p class="mt-6 text-[11px] tracking-[0.14em] text-white/35">WhatsApp · <a href="{{ $wa }}" target="_blank" rel="noopener" class="text-white/60 hover:text-kp-gold">{{ content('modelp.wa_display') }}</a></p>
      </div>
    </section>
  </main>

  {{-- Barre mobile --}}
  <div class="fixed inset-x-0 bottom-0 z-30 border-t border-kp-gold/20 bg-black/85 px-3 pb-[max(.75rem,env(safe-area-inset-bottom))] pt-3 backdrop-blur-xl lg:hidden" data-kp-md-bar>
    <div class="mx-auto flex max-w-[560px] items-center gap-3">
      <div class="min-w-0 flex-1"><p class="truncate text-[9px] font-semibold uppercase tracking-[0.22em] text-kp-gold">{{ $kPrice ? $priceLabel : $car->name }}</p><p class="truncate font-serif text-[15px] text-white">{{ $kPrice ? $fmt($kPrice) : $t('price_request') }}</p></div>
      <a href="{{ $wa }}" target="_blank" rel="noopener" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-white/20 text-white" aria-label="{{ $t('cta_wa') }}">{!! $waIcon !!}</a>
      <a href="{{ route('essai', ['modele' => $car->slug]) }}" class="inline-flex h-11 shrink-0 items-center justify-center rounded-full bg-kp-gold px-4 text-[10px] font-bold uppercase tracking-[0.16em] text-black">{{ $t('cta_test') }}</a>
    </div>
  </div>

  {{-- Visionneuse --}}
  @if($gallery->isNotEmpty())
  <template x-teleport="body">
    <div x-show="lb" x-cloak x-transition.opacity @keydown.escape.window="close()" @keydown.arrow-right.window="lb && step(1)" @keydown.arrow-left.window="lb && step(-1)" @click.self="close()" role="dialog" aria-modal="true" :aria-label="items[i] ? items[i].alt : ''" class="fixed inset-0 z-[200] flex items-center justify-center bg-black/95 p-4 backdrop-blur-sm" data-kp-md-lightbox>
      <button type="button" @click="close()" class="absolute right-4 top-4 z-10 flex h-11 w-11 items-center justify-center rounded-full border border-white/20 bg-black/50 text-lg text-white hover:border-kp-gold" aria-label="{{ $t('lb_close') }}" data-kp-md-close>✕</button>
      <button type="button" @click="step(-1)" class="absolute left-2 top-1/2 z-10 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-black/50 text-2xl text-white hover:border-kp-gold sm:left-6" aria-label="{{ $t('lb_prev') }}">‹</button>
      <img :src="items[i] ? items[i].src : ''" :alt="items[i] ? items[i].alt : ''" class="max-h-[80vh] max-w-full rounded-xl object-contain shadow-2xl" data-kp-md-lbimg>
      <button type="button" @click="step(1)" class="absolute right-2 top-1/2 z-10 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-black/50 text-2xl text-white hover:border-kp-gold sm:right-6" aria-label="{{ $t('lb_next') }}" data-kp-md-next>›</button>
      <div class="absolute bottom-5 left-1/2 flex -translate-x-1/2 items-center gap-4"><span class="h-px w-10 bg-kp-gold/60"></span><p class="text-[11px] tracking-[0.25em] text-white/70" x-text="(i+1)+' / '+items.length" data-kp-md-count></p><span class="h-px w-10 bg-kp-gold/60"></span></div>
    </div>
  </template>
  @endif
</div>
<script>
function kpModel(items, ids){return{items:items,i:0,lb:false,filter:'all',active:ids[0]||'',
 get list(){return this.filter==='all'?this.items:this.items.filter(g=>g.tag===this.filter)},
 get shown(){return this.list.slice(0,7)}, get rest(){return Math.max(0,this.list.length-7)},
 open(n){this.i=n;this.lb=true;document.documentElement.style.overflow='hidden'},
 close(){this.lb=false;document.documentElement.style.overflow=''},
 step(d){this.i=(this.i+d+this.items.length)%this.items.length},
 go(id){const el=document.getElementById(id);if(!el)return;this.active=id;el.scrollIntoView({behavior:matchMedia('(prefers-reduced-motion: reduce)').matches?'auto':'smooth',block:'start'});history.replaceState(null,'','#'+id)},
 init(){if(!('IntersectionObserver' in window))return;const io=new IntersectionObserver(es=>{es.forEach(e=>{if(e.isIntersecting)this.active=e.target.id})},{rootMargin:'-45% 0px -50% 0px'});ids.forEach(id=>{const el=document.getElementById(id);if(el)io.observe(el)})}}}
(function(){var els=document.querySelectorAll('[data-kp-md-rv]');
if(!('IntersectionObserver' in window)||matchMedia('(prefers-reduced-motion: reduce)').matches){els.forEach(function(e){e.classList.add('is-in')});return}
var io=new IntersectionObserver(function(en){en.forEach(function(x){if(x.isIntersecting){x.target.classList.add('is-in');io.unobserve(x.target)}})},{threshold:0.06,rootMargin:'0px 0px -5% 0px'});
els.forEach(function(e){io.observe(e)})})();
</script>
@endsection
