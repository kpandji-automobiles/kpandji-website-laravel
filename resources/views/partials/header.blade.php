{{-- Port Blade/Alpine de KpHeader + KpAnimatedLogo (kpandji2026 final/components/kp/KpHeader.tsx) --}}
@php
  $kpEn = app()->getLocale() === 'en';
  $kpT = fn ($k) => $kpEn ? (content('header.'.$k.'_en') ?: content('header.'.$k)) : content('header.'.$k);
  $kpWorkspace = auth()->check() ? (auth()->user()->workspaceUrl() ?? route('home')) : null;
  $nav = collect(content('header.nav'))->filter(fn ($i) => ($i['visible'] ?? '1') !== '0' && trim($i['label'] ?? '') !== '')
    ->map(fn ($i) => [$i['label'], cms_url($i['url'] ?? ''), cms_active($i['url'] ?? '')])
    ->concat(\App\Models\Page::where('in_nav', true)->where('published', true)->orderBy('nav_order')->get()
      ->map(fn ($p) => [$p->title, $p->url(), request()->is($p->slug)]))
    ->values()->all();
  $utility = collect(content('header.utility'))->filter(fn ($i) => trim($i['label'] ?? '') !== '')->map(fn ($i) => [$i['label'], cms_url($i['url'] ?? '')])->values()->all();
  $searchIndex = \App\Models\Car::visible()->get()
    ->map(fn ($c) => ['label' => $c->name, 'sub' => $c->category, 'href' => route('cars.show', $c), 'k' => strtolower($c->name.' '.$c->category.' '.implode(' ', $c->highlights ?? []))])
    ->concat(collect(array_merge($nav, $utility, [[content('header.cta_label'), cms_url(content('header.cta_url'))], ['Promotion', route('home').'#accueil']]))
      ->map(fn ($i) => ['label' => $i[0], 'sub' => 'Page', 'href' => $i[1], 'k' => strtolower($i[0])]))
    ->values();
@endphp
<div x-data="kpHeader()" x-init="init()" @keydown.escape.window="closeAll()" @scroll.window="scrolled = window.scrollY > 24">
<header class="fixed inset-x-0 top-0 z-50 pt-[env(safe-area-inset-top)] transition-[background,box-shadow,border-color] duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
  :class="solid() ? 'border-b border-white/10 bg-black/80 shadow-[0_20px_60px_rgba(0,0,0,0.45)] backdrop-blur-2xl backdrop-saturate-150' : 'border-b border-transparent bg-gradient-to-b from-black/55 via-black/20 to-transparent'">

  {{-- Bandeau utilitaire (md+) : se replie au scroll --}}
  <div class="hidden md:grid transition-[grid-template-rows] duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]" :class="scrolled && !menuOpen && !searchOpen ? 'grid-rows-[0fr]' : 'grid-rows-[1fr]'">
    <div class="overflow-hidden">
      <div class="mx-auto flex max-w-[1680px] items-center justify-end gap-6 px-10 py-2.5 text-sm font-medium uppercase tracking-widest text-white/45">
        @foreach($utility as $i => [$label, $href])
          @if($i > 0)<span class="h-3 w-px shrink-0 bg-white/15" aria-hidden="true"></span>@endif
          <a href="{{ $href }}" class="shrink-0 whitespace-nowrap transition-colors hover:text-white/80">{{ $label }}</a>
        @endforeach
        <span class="h-3 w-px shrink-0 bg-white/15" aria-hidden="true"></span>
        @include('partials.locale-switch')
      </div>
    </div>
  </div>

  <div class="relative mx-auto flex h-14 max-w-[1680px] items-center gap-2 px-3.5 sm:h-16 sm:gap-3 sm:px-5 md:h-[84px] md:gap-6 md:px-10">
    {{-- KpAnimatedLogo --}}
    <a href="{{ route('home') }}" class="relative z-10 flex min-w-0 shrink items-center transition-transform duration-[450ms] ease-[cubic-bezier(0.22,1,0.36,1)] hover:scale-[1.02] active:scale-[0.995]" aria-label="KPANDJI — accueil">
      <div class="relative isolate shrink-0 h-11 w-[148px] sm:h-12 sm:w-[180px] md:h-[68px] md:w-[min(340px,36vw)] xl:h-[76px] xl:w-[280px] 2xl:w-[300px] min-[1800px]:w-[420px]">
        <div x-show="showLayout" x-cloak
             x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-[250ms]" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-700" x-transition:leave-start="opacity-100 blur-0" x-transition:leave-end="opacity-0 blur-[8px]"
             class="absolute inset-0 z-0 flex items-center justify-start">
          <div class="flex w-full items-center gap-0 sm:gap-1">
            <div class="flex min-h-[52px] min-w-0 flex-1 items-center justify-end pr-2 sm:pr-3 md:min-h-[60px] md:pr-4">
              <span class="kp-slide-l font-serif text-lg font-semibold tracking-[0.02em] text-white/95 sm:text-xl md:text-2xl xl:text-xl 2xl:text-2xl whitespace-nowrap">@content('header.brand')</span>
            </div>
            <div aria-hidden="true" class="kp-line h-10 w-px shrink-0 origin-center bg-gradient-to-b from-transparent via-white/35 to-transparent sm:h-12 md:h-14"></div>
            <div class="flex min-h-[52px] min-w-0 flex-1 flex-col justify-center pl-2 sm:pl-3 md:min-h-[60px] md:pl-4">
              <div class="kp-slide-r">
                <p class="whitespace-nowrap text-[10px] font-medium uppercase leading-snug tracking-[0.18em] text-white/70 sm:text-[11px] md:text-xs xl:text-[10px] xl:tracking-[0.12em] 2xl:text-xs 2xl:tracking-[0.18em]">@content('header.tagline1')</p>
                <p class="mt-1 whitespace-nowrap text-[10px] font-medium uppercase leading-snug tracking-[0.18em] text-white/55 sm:text-[11px] md:text-xs xl:text-[10px] xl:tracking-[0.12em] 2xl:text-xs 2xl:tracking-[0.18em]">@content('header.tagline2')</p>
              </div>
            </div>
          </div>
        </div>
        <div x-show="showLogo"
             x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-700" x-transition:enter-start="opacity-0 scale-[0.88] blur-[14px]" x-transition:enter-end="opacity-100 scale-100 blur-0"
             x-transition:leave="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-[350ms]" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-[0.96]"
             class="absolute inset-0 z-10 flex items-center justify-start">
          <div class="relative overflow-hidden rounded-sm">
            <img src="{{ cms_asset(content('header.logo')) }}" alt="" aria-hidden="true" width="220" height="62" class="relative h-10 w-auto max-w-full object-contain object-left sm:h-11 md:h-14 xl:h-16">
          </div>
        </div>
      </div>
    </a>

    {{-- Nav centrale (xl+) --}}
    <nav class="hidden min-w-0 xl:flex xl:flex-1 xl:items-center xl:justify-center xl:gap-0 min-[1700px]:gap-2" aria-label="Navigation principale">
      @foreach($nav as [$label, $href, $active])
        <a href="{{ $href }}" @if($active) aria-current="page" @endif
           class="whitespace-nowrap px-2 py-2 text-[15px] font-semibold uppercase tracking-[0.1em] transition-colors 2xl:px-3 min-[1700px]:px-4 min-[1700px]:text-lg min-[1700px]:tracking-[0.12em] {{ $active ? 'text-white' : 'text-white/80 hover:text-white' }}">{{ $label }}</a>
      @endforeach
    </nav>

    {{-- Icônes à droite --}}
    <div class="ml-auto flex shrink-0 items-center gap-0.5 sm:gap-1 xl:ml-0">
      <template x-if="scrolled && !menuOpen && !searchOpen"><span class="mr-1 hidden md:inline-flex xl:hidden min-[1700px]:inline-flex">@include('partials.locale-switch')</span></template>
      <a href="{{ cms_url(content('header.cta_url')) }}" class="hidden items-center rounded-full border border-white/15 bg-white/[0.06] px-5 py-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-white/90 backdrop-blur-sm transition hover:border-white/30 hover:bg-white/[0.09] hover:text-white xl:inline-flex">@content('header.cta_label')</a>
      <button type="button" @click="menuOpen=false; openSearch()" class="flex h-11 w-11 items-center justify-center rounded-full text-white/85 transition-colors hover:bg-white/10 hover:text-white active:bg-white/15" aria-label="Ouvrir la recherche">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/></svg>
      </button>
      {{-- Icône utilisateur : invité -> fenêtre de connexion (partials/account-login) ; connecté -> petit menu --}}
      @guest
      <a href="{{ route('login') }}" data-kp-login-open @click="menuOpen=false; searchOpen=false; if (window.kpOpenLogin && window.kpOpenLogin()) $event.preventDefault()" class="flex h-11 w-11 items-center justify-center rounded-full text-white/85 transition-colors hover:bg-white/10 hover:text-white" aria-haspopup="dialog" aria-label="{{ $kpT('login_title') }}" title="{{ $kpT('login_title') }}">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="9" r="3.5"/><path d="M6 19.5c0-3 2.5-5 6-5s6 2 6 5"/></svg>
      </a>
      @else
      <div class="relative" x-data="{ acc: false }" @keydown.escape.window="acc = false" @click.outside="acc = false">
        <button type="button" data-kp-account-btn @click="acc = !acc" :aria-expanded="acc" aria-haspopup="menu" class="relative flex h-11 w-11 items-center justify-center rounded-full text-kp-gold transition-colors hover:bg-white/10" aria-label="{{ auth()->user()->name }}">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="9" r="3.5"/><path d="M6 19.5c0-3 2.5-5 6-5s6 2 6 5"/></svg><span class="absolute right-2 top-2 h-1.5 w-1.5 rounded-full bg-kp-gold shadow-[0_0_8px_rgba(201,169,98,0.8)]" aria-hidden="true"></span>
        </button>
        <div x-show="acc" x-cloak x-transition.opacity.duration.200ms data-kp-account-menu role="menu" class="absolute right-0 top-full z-[60] mt-2 w-64 overflow-hidden rounded-2xl border border-kp-gold/20 bg-[#080808]/95 shadow-[0_24px_70px_-20px_rgba(0,0,0,0.9)] ring-1 ring-white/[0.06] backdrop-blur-xl">
          <div class="border-b border-white/10 px-5 py-4"><p class="truncate font-serif text-lg text-white">{{ auth()->user()->name }}</p><p class="truncate text-[11px] text-white/40">{{ auth()->user()->email }}</p></div>
          <a role="menuitem" href="{{ $kpWorkspace }}" class="flex min-h-11 items-center px-5 text-[11px] font-semibold uppercase tracking-[0.2em] text-white/80 transition hover:bg-white/[0.06] hover:text-kp-gold">{{ $kpT(auth()->user()->menuKey()) }}</a>
          <form method="POST" action="{{ route('account.logout') }}">@csrf<button role="menuitem" type="submit" data-kp-logout class="flex min-h-11 w-full items-center border-t border-white/10 px-5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-white/50 transition hover:bg-white/[0.06] hover:text-white">{{ $kpT('account_logout') }}</button></form>
        </div>
      </div>
      @endguest
      <button type="button" @click="searchOpen=false; menuOpen=!menuOpen" class="relative flex h-11 w-11 flex-col items-center justify-center gap-[5px] rounded-full bg-white text-black transition-colors hover:bg-white/90 active:bg-white/85 xl:hidden"
              :aria-expanded="menuOpen" aria-controls="kp-mobile-menu" :aria-label="menuOpen ? 'Fermer le menu' : 'Ouvrir le menu'">
        <span class="block h-px w-[18px] origin-center bg-current transition-[transform,opacity] duration-300 ease-out" :class="menuOpen && 'translate-y-[3px] rotate-45'"></span>
        <span class="block h-px w-[18px] origin-center bg-current transition-[transform,opacity] duration-300 ease-out" :class="menuOpen && '-translate-y-[3px] -rotate-45'"></span>
      </button>
    </div>
  </div>
  <div class="pointer-events-none h-px bg-gradient-to-r from-transparent via-white/15 to-transparent transition-opacity duration-500" :class="solid() ? 'opacity-100' : 'opacity-0'"></div>
</header>

{{-- Recherche plein écran --}}
<div x-cloak class="fixed inset-0 z-[70] flex flex-col bg-black/[0.96] pt-[env(safe-area-inset-top)] pb-[env(safe-area-inset-bottom)] backdrop-blur-2xl transition-[opacity,visibility] duration-500 ease-out"
     :class="searchOpen ? 'visible opacity-100' : 'invisible pointer-events-none opacity-0'" role="dialog" aria-modal="true" aria-label="Recherche">
  <form class="flex items-center justify-between border-b border-white/10 px-4 py-3.5 sm:px-5 sm:py-5 md:px-10" @submit.prevent="go()">
    <label for="kp-search" class="sr-only">Rechercher sur le site</label>
    <div class="flex min-w-0 flex-1 items-center gap-3 sm:gap-4">
      <svg class="shrink-0 text-white/50" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/></svg>
      <input x-ref="search" x-model="q" id="kp-search" type="search" enterkeyhint="search" autocapitalize="off" autocorrect="off" placeholder="@content('header.search_placeholder')" class="w-full min-w-0 bg-transparent text-[16px] text-white outline-none placeholder:text-white/35 sm:text-lg md:text-xl">
    </div>
    <button type="button" @click="searchOpen=false" class="ml-2 shrink-0 rounded-full px-3 py-2.5 text-[11px] font-semibold uppercase tracking-[0.2em] text-white/70 transition hover:text-white sm:ml-4 sm:px-4">@content('header.search_close')</button>
  </form>
  <div class="mx-auto mt-8 w-full max-w-2xl flex-1 overflow-y-auto overscroll-contain px-5 pb-10 text-center sm:mt-16 sm:px-6">
    <template x-if="!q.trim()">
      <div>
        <p class="font-serif text-[1.65rem] leading-tight text-white/90 sm:text-2xl md:text-3xl">@content('header.search_title')</p>
        <p class="mt-3 text-sm leading-relaxed text-white/45">@content('header.search_hint')</p>
      </div>
    </template>
    <div class="mt-8 flex flex-wrap justify-center gap-2.5 sm:mt-10 sm:gap-3">
      @foreach(collect(content('header.search_tags'))->pluck('label')->filter() as $tag)
        <button type="button" @click="q = @js($tag); $refs.search.focus()" class="min-h-11 rounded-full border border-white/15 px-4 py-2.5 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/75 transition hover:border-white/35 hover:bg-white/5 sm:px-5 sm:text-[11px]">{{ $tag }}</button>
      @endforeach
    </div>
    <ul class="mt-10 space-y-2 text-left" x-show="q.trim()">
      <template x-for="r in results()" :key="r.href + r.label">
        <li><a :href="r.href" @click="searchOpen=false" class="flex items-center justify-between rounded-lg border border-white/10 px-5 py-4 text-white transition hover:border-white/30 hover:bg-white/5"><span class="font-serif text-xl" x-text="r.label"></span><span class="text-[10px] uppercase tracking-[0.2em] text-white/40" x-text="r.sub"></span></a></li>
      </template>
      <li x-show="results().length === 0" class="text-center text-sm text-white/45">Aucun résultat pour « <span x-text="q"></span> ».</li>
    </ul>
  </div>
</div>

{{-- Menu mobile / tablette --}}
<div x-cloak class="fixed inset-0 z-[60] xl:hidden" :class="menuOpen ? 'pointer-events-auto' : 'pointer-events-none'" :aria-hidden="!menuOpen">
  <button type="button" class="absolute inset-0 bg-black/75 backdrop-blur-sm transition-opacity duration-500" :class="menuOpen ? 'opacity-100' : 'opacity-0'" @click="menuOpen=false" aria-label="Fermer le menu" :tabindex="menuOpen ? 0 : -1"></button>
  <div id="kp-mobile-menu" role="dialog" aria-modal="true" aria-label="Navigation principale" :data-open="menuOpen ? 'true' : 'false'"
       class="kp-menu-panel absolute inset-y-0 right-0 flex w-full flex-col bg-[#060606] shadow-[-24px_0_80px_rgba(0,0,0,0.75)] transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] sm:max-w-[440px]"
       :class="menuOpen ? 'translate-x-0' : 'translate-x-full'" style="padding-top:env(safe-area-inset-top);padding-bottom:env(safe-area-inset-bottom)">
    <div class="flex shrink-0 items-center justify-between border-b border-white/10 px-5 py-3.5 sm:px-6 sm:py-5">
      <img src="{{ cms_asset(content('header.logo')) }}" alt="KPANDJI" class="h-8 w-auto opacity-90 sm:h-10">
      <button type="button" @click="menuOpen=false" class="flex h-11 w-11 items-center justify-center rounded-full text-white/70 transition hover:bg-white/10 hover:text-white" aria-label="Fermer"><span class="block text-2xl leading-none" aria-hidden="true">×</span></button>
    </div>
    <div class="flex min-h-0 flex-1 flex-col">
      <nav class="flex-1 overflow-y-auto overscroll-contain px-2 pt-2 sm:pt-3" aria-label="Navigation principale">
        @foreach($nav as $i => [$label, $href, $active])
          <a href="{{ $href }}" @click="menuOpen=false" @if($active) aria-current="page" @endif style="animation-delay: {{ 70 + $i * 40 }}ms"
             class="kp-mobile-link group flex min-h-[3.25rem] items-center justify-between gap-4 border-b border-white/[0.06] px-4 py-4 transition active:bg-white/[0.06] sm:py-5 {{ $active ? 'text-white' : 'text-white/80 hover:text-white' }}">
            <span class="flex min-w-0 items-baseline gap-3.5">
              <span class="shrink-0 text-[10px] tabular-nums tracking-[0.18em] {{ $active ? 'text-kp-gold' : 'text-white/30' }}">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
              <span class="font-serif text-[1.35rem] font-medium leading-none tracking-[0.02em] sm:text-[1.5rem]">{{ $label }}</span>
            </span>
            <span class="h-px shrink-0 transition-[width,background-color] duration-300 {{ $active ? 'w-8 bg-kp-gold' : 'w-5 bg-white/20 group-hover:w-7 group-hover:bg-white/45' }}" aria-hidden="true"></span>
          </a>
        @endforeach
        <div class="mt-5 space-y-1 px-4 pb-4">
          @foreach($utility as [$label, $href])
            <a href="{{ $href }}" @click="menuOpen=false" class="flex min-h-11 items-center text-[11px] font-medium uppercase tracking-[0.18em] text-white/45 transition hover:text-white/80">{{ $label }}</a>
          @endforeach
        </div>
      </nav>
      <div class="shrink-0 border-t border-white/10 bg-[#080808] px-5 pb-4 pt-4 sm:px-6">
        <div class="space-y-2.5">
          @guest
          <a href="{{ route('login') }}" data-kp-login-open-mobile @click="menuOpen=false; if (window.kpOpenLogin && window.kpOpenLogin()) $event.preventDefault()" class="inline-flex w-full min-h-12 items-center justify-center gap-2 rounded-full border border-white/15 px-6 py-3.5 text-[11px] font-semibold uppercase tracking-[0.22em] text-white/90 transition hover:border-white/30 hover:bg-white/5">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="9" r="3.5"/><path d="M6 19.5c0-3 2.5-5 6-5s6 2 6 5"/></svg>
            @content('header.account_label')
          </a>
          @else
          <div class="flex items-center gap-2">
            <a href="{{ $kpWorkspace }}" class="inline-flex min-h-12 min-w-0 flex-1 items-center justify-center gap-2 rounded-full border border-kp-gold/30 px-4 py-3.5 text-[11px] font-semibold uppercase tracking-[0.18em] text-kp-gold transition hover:bg-kp-gold/10"><span class="truncate">{{ auth()->user()->name }} · {{ $kpT(auth()->user()->menuKey()) }}</span></a>
            <form method="POST" action="{{ route('account.logout') }}">@csrf<button type="submit" class="inline-flex min-h-12 items-center rounded-full border border-white/15 px-4 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/60 transition hover:text-white">{{ $kpT('account_logout') }}</button></form>
          </div>
          @endguest
          <a href="{{ cms_url(content('header.cta_url')) }}" @click="menuOpen=false" class="inline-flex w-full min-h-12 items-center justify-center rounded-full bg-white px-6 py-3.5 text-[11px] font-semibold uppercase tracking-[0.22em] text-black transition hover:bg-white/95">@content('header.cta_label')</a>
        </div>
        <div class="mt-4 flex items-center justify-between gap-3">
          @include('partials.locale-switch')
          <p class="truncate text-[9px] uppercase tracking-[0.2em] text-white/30">@content('header.mobile_tagline')</p>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<div class="h-14 sm:h-16 md:h-[125px]" aria-hidden="true"></div>
@guest @include('partials.account-login', ['kpT' => $kpT]) @endguest

<script>
function kpHeader() {
  return {
    scrolled: window.scrollY > 24, menuOpen: false, searchOpen: false, q: '',
    showLayout: false, showLogo: true, seqTimer: null, index: @json($searchIndex),
    solid() { return this.scrolled || this.menuOpen || this.searchOpen },
    closeAll() { this.menuOpen = false; this.searchOpen = false },
    openSearch() { this.searchOpen = true; setTimeout(() => this.$refs.search.focus(), 50) },
    results() {
      const t = this.q.trim().toLowerCase(); if (!t) return [];
      return this.index.filter(r => r.k.includes(t) || r.label.toLowerCase().includes(t)).slice(0, 8);
    },
    go() { const r = this.results(); if (r.length) window.location = r[0].href },
    // KpAnimatedLogo : séquence « mise en page » -> logo (desktop, sans reduced-motion), relancée toutes les 3 min
    runSequence() {
      clearTimeout(this.seqTimer);
      this.showLogo = false; this.showLayout = true;
      this.seqTimer = setTimeout(() => { this.showLayout = false; this.showLogo = true }, 550 + 180 + 850 + 550);
    },
    init() {
      const compact = matchMedia('(max-width: 767px)').matches;
      const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
      if (!compact && !reduce) { this.runSequence(); setInterval(() => this.runSequence(), 180000) }
      this.$watch('menuOpen', v => document.documentElement.style.overflow = (v || this.searchOpen) ? 'hidden' : '');
      this.$watch('searchOpen', v => document.documentElement.style.overflow = (v || this.menuOpen) ? 'hidden' : '');
    },
  }
}
</script>
