{{-- Opportunités — port de app/opportunities/page.tsx + components/kp/OpportunitiesPageContent.tsx (+ Reveal.tsx, kp-light-sweep de globals.css).
     Contenu : Admin › Opportunités (/admin/opportunites) — clés CMS « opps.* » --}}
@extends('layouts.app')
@php($en = app()->getLocale() === 'en')
@php($c2 = fn ($k) => $en ? (content($k.'_en') ?: content($k)) : content($k))
@php($cards = collect(content('opps.cards'))->filter(fn ($i) => ($i['visible'] ?? '1') !== '0' && trim(($i['name'] ?? '').($i['brand'] ?? '')) !== '')->values())
@php($grad = ['bronze' => 'linear-gradient(to right,#CD7F32,#c9a962,#E8D5A3)', 'blanc' => 'linear-gradient(to right,#ffffff,rgba(254,243,199,.95),#c9a962)'])
@section('seo_title', content('opps.seo_title'))
@section('meta_description', content('opps.seo_description'))
@push('meta')
<link rel="canonical" href="{{ url('/opportunities') }}">
<meta property="og:title" content="{{ content('opps.og_title') }}">
<meta property="og:description" content="{{ content('opps.og_description') }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="fr_FR">
<meta property="og:url" content="{{ url('/opportunities') }}">
@if($cards->first() && filled($cards->first()['image'] ?? null))<link rel="preload" as="image" href="{{ cms_asset($cards->first()['image']) }}" fetchpriority="high">@endif
@endpush
@section('content')
<style>
.kp-op-rv{opacity:0;transform:translateY(40px);transition:opacity .75s cubic-bezier(.22,1,.36,1),transform .75s cubic-bezier(.22,1,.36,1)}
.kp-op-rv.is-in{opacity:1;transform:none}
.kp-op-card{transition:transform .45s cubic-bezier(.34,1.4,.64,1),border-color .5s,box-shadow .5s}
.kp-op-card:hover{transform:translateY(-8px) scale(1.01)}.kp-op-card:active{transform:scale(.99)}
.kp-op-sweep{transform:translateX(-130%) skewX(-12deg)}
.kp-op-card:hover .kp-op-sweep{animation:kp-light-sweep 1.65s ease both}
@keyframes kp-light-sweep{0%{transform:translateX(-130%) skewX(-12deg);opacity:0}22%{opacity:.3}48%{opacity:.16}100%{transform:translateX(130%) skewX(-12deg);opacity:0}}
@media (prefers-reduced-motion:reduce){.kp-op-rv{opacity:1!important;transform:none!important;transition:none!important}.kp-op-card,.kp-op-card:hover,.kp-op-card:active{transform:none!important}.kp-op-card:hover .kp-op-sweep{animation:none}.kp-op-img{transition:none!important}}
</style>
<div class="min-h-screen bg-kp-bg text-white">
  <main class="relative isolate overflow-hidden pt-[110px] md:pt-[132px]">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-kp-surface"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(900px_480px_at_20%_0%,rgba(201,169,98,0.14),transparent_55%),radial-gradient(800px_420px_at_85%_40%,rgba(255,255,255,0.05),transparent_50%)]"></div>
    <div aria-hidden="true" class="kp-grain pointer-events-none absolute inset-0 -z-10 opacity-[0.1] mix-blend-overlay"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold/55 to-transparent"></div>

    <div class="relative z-10 mx-auto max-w-[1400px] px-5 pb-20 pt-8 sm:px-8 sm:pb-24 sm:pt-10 md:px-10 lg:pb-28 lg:pt-12">
      {!! cms_edit(route('admin.opps').'#textes', 'Modifier cette section', '0') !!}
      <header class="kp-op-rv mx-auto max-w-2xl text-center" data-kp-op-rv>
        <span class="mx-auto block h-px w-28 bg-gradient-to-r from-transparent via-kp-gold/90 to-transparent sm:w-40" aria-hidden="true"></span>
        <p class="mt-5 font-sans text-[10px] font-semibold uppercase tracking-[0.32em] text-white/40">{{ $c2('opps.eyebrow') }}</p>
        <h1 class="mt-3 font-serif text-[clamp(2rem,5vw,3.25rem)] font-medium leading-[1.05] tracking-[-0.025em] text-kp-accent">{{ $c2('opps.title') }}</h1>
        <p class="mx-auto mt-4 max-w-xl font-sans text-[14px] leading-relaxed text-white/50 sm:text-[15px]" style="text-wrap:pretty">{{ $c2('opps.intro') }}</p>
      </header>

      <div class="relative mt-12 grid gap-6 sm:mt-14 sm:gap-7 md:mt-16 lg:grid-cols-2 lg:gap-8 xl:gap-10">
        {!! cms_edit(route('admin.opps').'#cartes', 'Modifier les cartes', '-2.6rem') !!}
        @foreach($cards as $i => $it)
        @php($t = fn ($f) => $en ? (($it[$f.'_en'] ?? '') ?: ($it[$f] ?? '')) : ($it[$f] ?? ''))
        <div class="kp-op-rv h-full" data-kp-op-rv style="transition-delay:{{ $i * 90 }}ms">
          <article class="kp-op-card group relative flex h-full flex-col overflow-hidden rounded-2xl border border-white/[0.08] bg-kp-elevated/35 shadow-[0_28px_90px_-40px_rgba(0,0,0,0.95)] backdrop-blur-md hover:border-kp-gold/35 hover:shadow-[0_36px_100px_-36px_rgba(201,169,98,0.28)] md:rounded-3xl">
            <div class="relative aspect-[4/3] w-full shrink-0 overflow-hidden sm:aspect-[16/11]">
              @if(filled($it['image'] ?? null))<img src="{{ cms_asset($it['image']) }}" alt="{{ $t('alt') }}" @if($i === 0) fetchpriority="high" @else loading="lazy" @endif decoding="async" sizes="(max-width: 768px) 100vw, 50vw" class="kp-op-img absolute inset-0 h-full w-full object-cover transition duration-700 ease-out group-hover:scale-[1.05]" style="object-position:{{ preg_match('/^[0-9.]+%\s+[0-9.]+%$/', trim($it['position'] ?? '')) ? trim($it['position']) : '50% 50%' }}">@endif
              <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-gradient-to-t from-kp-elevated via-transparent to-black/25"></div>
              <div aria-hidden="true" class="kp-grain pointer-events-none absolute inset-0 opacity-[0.18] mix-blend-overlay"></div>
              <div aria-hidden="true" class="kp-op-sweep pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-700 group-hover:opacity-100" style="background:linear-gradient(110deg,transparent 0%,rgba(255,255,255,0.12) 45%,transparent 60%)"></div>
              <div class="absolute left-4 top-4 flex items-center gap-2 sm:left-5 sm:top-5">
                <span class="rounded-full border border-white/15 bg-black/40 px-3 py-1 font-sans text-[10px] font-semibold uppercase tracking-[0.22em] text-white/70 backdrop-blur-md">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
              </div>
            </div>
            <div class="relative flex flex-1 flex-col border-t border-white/[0.08] px-5 py-6 sm:px-7 sm:py-8">
              <span aria-hidden="true" class="pointer-events-none absolute -right-12 -top-12 h-36 w-36 rounded-full bg-kp-gold/10 opacity-0 blur-3xl transition-opacity duration-500 group-hover:opacity-100"></span>
              <p class="font-sans text-[10px] font-semibold uppercase tracking-[0.28em] text-kp-gold/90">{{ $t('eyebrow') }}</p>
              <h2 class="mt-3 font-serif text-[clamp(1.85rem,4vw,2.65rem)] font-medium leading-[1.05] tracking-[-0.025em] text-white">
                <span class="block text-white/90">{{ $it['brand'] ?? '' }}</span>
                <span class="block bg-clip-text text-transparent" style="background-image:{{ $grad[$it['gradient'] ?? ''] ?? $grad['blanc'] }};-webkit-background-clip:text">{{ $it['name'] ?? '' }}</span>
              </h2>
              <p class="mt-4 flex-1 font-sans text-[14px] leading-relaxed text-white/55 sm:text-[15px]" style="text-wrap:pretty">{{ $t('description') }}</p>
              <span class="mt-7 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-full bg-kp-gold px-6 py-3 font-sans text-[11px] font-semibold uppercase tracking-[0.18em] text-black shadow-[0_12px_36px_-14px_rgba(201,169,98,0.55)] transition duration-300 group-hover:bg-[#d4b56e] sm:mt-8 sm:text-[12px]">
                {{ $c2('opps.cta') }}
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-x-0.5" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
            </div>
            <a href="{{ cms_url($it['url'] ?? '#') }}" aria-label="{{ $en ? 'See more about' : 'Voir plus sur' }} {{ $it['brand'] ?? '' }} {{ $it['name'] ?? '' }}" class="absolute inset-0 z-20 rounded-2xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/70 md:rounded-3xl"></a>
          </article>
        </div>
        @endforeach
      </div>
    </div>
  </main>
</div>
<script>
(function(){var els=document.querySelectorAll('[data-kp-op-rv]');
if(!('IntersectionObserver' in window)||matchMedia('(prefers-reduced-motion: reduce)').matches){els.forEach(function(e){e.classList.add('is-in')});return}
var io=new IntersectionObserver(function(en){en.forEach(function(x){if(x.isIntersecting){x.target.classList.add('is-in');io.unobserve(x.target)}})},{threshold:0.14});
els.forEach(function(e){io.observe(e)});})();
</script>
@endsection
