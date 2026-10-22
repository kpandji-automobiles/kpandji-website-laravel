{{-- Port Blade/Alpine de KpHero (kpandji2026 final/components/kp/KpHero.tsx) --}}
@php
  $pillars = collect(content('hero.pillars'))->map(fn ($p) => [$p['code'] ?? '', $p['label'] ?? '', $p['detail'] ?? ''])->all();
  $marquee = collect(content('hero.marquee'))->pluck('label')->filter()->values()->all();
@endphp
<section id="accueil" class="relative -mt-14 sm:-mt-16 md:-mt-[125px] flex min-h-svh scroll-mt-28 flex-col overflow-hidden"
  x-data="kpHero()" x-init="init()" x-ref="wrap">
  {!! cms_edit('hero', 'Modifier cette section', '150px') !!}

  {{-- Vidéo de fond + overlays cinématographiques --}}
  <div class="absolute inset-0 z-0">
    <div class="absolute inset-0 origin-center will-change-transform"
         :style="`transform: scale(${1 + progress * 0.06}) translate3d(0, ${progress * 48}px, 0); transition: transform 0.1s linear`">
      <video class="h-full w-full object-cover object-[center_35%] sm:object-center" autoplay muted loop playsinline preload="metadata">
        <source src="{{ cms_asset(content('hero.video')) }}" type="video/mp4">
      </video>
    </div>
    <div class="pointer-events-none absolute inset-0 h-full w-full" :style="`opacity: ${0.35 + progress * 0.35}`">
      <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/25 to-black/95 sm:from-black/55 sm:via-black/20"></div>
      <div class="absolute inset-0 bg-[radial-gradient(1200px_520px_at_22%_18%,rgba(201,169,98,0.20),transparent_62%)]"></div>
      <div class="absolute inset-0 bg-[radial-gradient(900px_500px_at_85%_80%,rgba(201,169,98,0.10),transparent_60%)]"></div>
      <div class="absolute inset-0 bg-[radial-gradient(1200px_800px_at_50%_50%,transparent_55%,rgba(0,0,0,0.78)_100%)]"></div>
      <div class="kp-grain absolute inset-0 opacity-[0.28] md:opacity-[0.33]"></div>
    </div>
  </div>

  {{-- Texte promo commercial — slide-up en cascade --}}
  @php($promoEnd = config('promo.ends_at') ? \Carbon\Carbon::parse(config('promo.ends_at'))->endOfDay() : null)
  @php($pct = (float) config('promo.reduction_percent'))
  <div class="pointer-events-none absolute inset-y-0 left-0 z-[5] w-full bg-gradient-to-r from-black/80 via-black/45 to-transparent md:w-3/4"></div>
  <div class="relative z-10 mt-[4.75rem] flex flex-1 items-end sm:mt-20 md:mt-0">
    <div class="mx-auto w-full max-w-[1600px] px-4 pb-6 pt-10 sm:px-5 sm:pb-10 sm:pt-24 md:px-10 md:pb-12 md:pt-36 [text-shadow:0_2px_24px_rgba(0,0,0,0.55)]"
         :class="copyPhase === 'visible' ? 'kp-copy-visible' : 'kp-copy-hidden'">
      {{-- Découpage serveur des textes CMS pour l'animation (mots / lettres) ; texte complet lisible via aria-label --}}
      @php($kpWords = fn ($t) => preg_split('/\s+/u', trim((string) $t), -1, PREG_SPLIT_NO_EMPTY))
      @php($t1 = $kpWords(content('hero.title1')))
      @php($t2 = $kpWords(content('hero.title2')))
      @php($sub = $kpWords(content('hero.subline')))
      @php($d = 0.2)
      <div class="flex flex-wrap items-center gap-3">
        <span class="kp-pop inline-flex" style="--d:.05s"><span class="kp-badge-glow inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-[#e3c77f] via-kp-gold to-[#a8863f] px-4 py-1.5 text-[10px] font-bold uppercase tracking-[0.26em] text-black shadow-[0_0_40px_rgba(201,169,98,0.5)] [text-shadow:none] sm:text-[11px]">
          <span class="size-1.5 animate-pulse rounded-full bg-black"></span>@content('hero.badge')
        </span></span>
        @if(content('hero.eyebrow'))<span class="kp-up text-[10px] font-semibold uppercase tracking-[0.3em] text-white/70 sm:text-[11px]" style="--d:.25s">@content('hero.eyebrow')</span>@endif
      </div>

      <h1 aria-label="{{ trim(content('hero.title1').' '.content('hero.title2')) }}" class="mt-5 max-w-[14ch] font-serif text-[clamp(2.6rem,10vw,6.5rem)] font-semibold leading-[0.98] tracking-[-0.025em] text-white sm:max-w-none">
        <span class="block" aria-hidden="true">@foreach($t1 as $w)<span class="kp-wm"><span class="kp-w" style="--d:{{ round($d += 0.08, 3) }}s">{{ $w }}</span></span>@if(! $loop->last) @endif @endforeach</span>
        @php($d += 0.12)
        <span class="kp-flip block italic [text-shadow:none]" aria-hidden="true">@foreach($t2 as $w)<span class="inline-block whitespace-nowrap">@foreach(mb_str_split($w) as $ch)<span class="kp-ch" style="--d:{{ round($d += 0.035, 3) }}s">{{ $ch }}</span>@endforeach</span>@if(! $loop->last) @endif @endforeach</span>
      </h1>
      @php($d += 0.1)
      <span class="kp-rule mt-5 block h-px w-32 sm:w-48" style="--d:{{ round($d, 3) }}s" aria-hidden="true"></span>

      @if($sub)<p class="mt-5 max-w-2xl text-base leading-relaxed text-white/80 sm:text-lg" aria-label="{{ content('hero.subline') }}"><span aria-hidden="true">@foreach($sub as $w)<span class="kp-bw" style="--d:{{ round($d += 0.03, 3) }}s">{{ $w }}</span>@if(! $loop->last) @endif @endforeach</span></p>@endif
      @php($d = max($d, 1.0))

      <div class="mt-6 flex flex-col gap-5 md:mt-8 lg:flex-row lg:items-center lg:gap-8">
        <div class="kp-card-in kp-draw relative flex w-full max-w-md flex-col rounded-2xl border border-kp-gold/30 bg-black/40 px-5 py-4 shadow-[0_20px_60px_-20px_rgba(201,169,98,0.5)] backdrop-blur-md sm:w-auto sm:px-6" style="--d:{{ round($d += 0.05, 3) }}s" data-kp-hero-offer>
          {!! cms_edit('hero', 'Modifier', '-.9rem') !!}
          <div class="flex items-center gap-5">
          <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-white/55">@content('hero.offer_label')</p>
            @if($pct > 0)
              <p class="font-serif text-3xl font-semibold text-kp-gold sm:text-4xl">{{ str_replace(':pct', rtrim(rtrim(number_format($pct, 1, ',', ''), '0'), ','), content('hero.offer_pct')) }}</p>
            @elseif(filled(content('hero.offer_text')))
              <p class="font-serif text-2xl font-semibold leading-tight text-kp-gold sm:text-3xl">@content('hero.offer_text')</p>
            @endif
            @if($promoEnd && $promoEnd->isFuture())
              <p class="mt-1 text-[11px] uppercase tracking-[0.2em] text-white/60">{{ content('hero.offer_until') }} {{ $promoEnd->format('d/m/Y') }}</p>
            @endif
          </div>
          @if($promoEnd && $promoEnd->isFuture())
          <div x-data="{ t:0, end: {{ $promoEnd->timestamp * 1000 }}, tick(){ this.t = Math.max(0, this.end - Date.now()) } }" x-init="tick(); setInterval(() => tick(), 1000)" class="flex gap-2 border-l border-white/15 pl-5 [text-shadow:none]">
            @foreach([[86400000, null, 'j'], [3600000, 24, 'h'], [60000, 60, 'min']] as [$div, $mod, $lbl])
              <div class="text-center"><p class="font-serif text-2xl tabular-nums text-white" x-text="String(Math.floor(t / {{ $div }}){{ $mod ? ' % '.$mod : '' }}).padStart(2,'0')">00</p><p class="text-[9px] uppercase tracking-[0.2em] text-white/45">{{ $lbl }}</p></div>
            @endforeach
          </div>
          @endif
          </div>
          @php($kpOfferEn = app()->getLocale() === 'en')
          @php($kpOffer = collect(content('hero.offer_items'))->map(fn ($r) => ['icon' => $r['icon'] ?? '', 'txt' => $kpOfferEn ? (($r['en'] ?? '') ?: ($r['fr'] ?? '')) : ($r['fr'] ?? '')])->filter(fn ($r) => filled($r['txt']))->values())
          @if($kpOffer->isNotEmpty())
          @php($kpIco = ['shield' => '<path d="M12 3 5 6v5.5c0 4.3 2.9 8.1 7 9.5 4.1-1.4 7-5.2 7-9.5V6l-7-3Z"/><path d="m9 12 2.2 2.2L15.5 10"/>', 'wrench' => '<path d="M14.7 6.3a4 4 0 0 0 5 5L21 13l-1.4 1.4a5 5 0 0 1-6-.8L7 20.2a1.8 1.8 0 0 1-2.6-2.6L10.8 11a5 5 0 0 1-.8-6L11.4 3.6l1.6 1.3a4 4 0 0 0 1.7 1.4Z"/>', 'document' => '<rect x="3" y="7" width="18" height="10" rx="2"/><path d="M7 11h4M7 13.5h7"/><circle cx="17" cy="12" r="1.2"/>'])
          <ul class="mt-3 grid gap-1.5 border-t border-kp-gold/20 pt-3 [text-shadow:none]" role="list" data-kp-hero-offer-items>
            @foreach($kpOffer as $a)
            <li class="flex items-center gap-2.5"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-kp-gold/40 bg-kp-gold/10 text-kp-gold"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $kpIco[$a['icon']] ?? $kpIco['shield'] !!}</svg></span><span class="min-w-0 text-[12px] font-medium leading-snug text-white/90 sm:text-[13px]">{{ $a['txt'] }}</span></li>
            @endforeach
          </ul>
          @endif
        </div>
        <ul class="flex flex-wrap gap-2 [text-shadow:none]">
          @foreach(collect(content('hero.chips'))->pluck('label')->filter() as $chip)
            <li class="kp-up inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/[0.06] px-3.5 py-1.5 text-[10px] font-semibold uppercase tracking-[0.16em] text-white/85 backdrop-blur-sm sm:text-[11px]" style="--d:{{ round($d += 0.08, 3) }}s"><span class="text-kp-gold">✓</span>{{ $chip }}</li>
          @endforeach
        </ul>
      </div>

      <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4 md:mt-9">
        <div class="kp-up flex flex-col" style="--d:{{ round($d += 0.1, 3) }}s"><a href="{{ cms_url(content('hero.cta1_url')) }}" x-data="kpMagnet()" @mousemove="move($event)" @mouseleave="reset()" :style="s" class="kp-mag group inline-flex items-center justify-center gap-3 rounded-full bg-kp-gold px-8 py-4 text-[11px] font-bold uppercase tracking-[0.22em] text-black shadow-[0_10px_40px_rgba(201,169,98,0.4)] hover:bg-[#dcc07f] hover:shadow-[0_14px_50px_rgba(201,169,98,0.6)] [text-shadow:none] md:text-[12px]">
          @content('hero.cta1_label')
          <svg aria-hidden="true" viewBox="0 0 24 24" class="kp-nudge h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 5 7 7-7 7"/></svg>
        </a></div>
        <div class="kp-up flex flex-col" style="--d:{{ round($d += 0.1, 3) }}s"><a href="{{ cms_url(content('hero.cta2_url')) }}" x-data="kpMagnet()" @mousemove="move($event)" @mouseleave="reset()" :style="s" class="kp-mag inline-flex items-center justify-center rounded-full border border-white/50 bg-white/5 px-8 py-4 text-[11px] font-semibold uppercase tracking-[0.22em] text-white backdrop-blur-sm hover:border-kp-gold hover:text-kp-gold md:text-[12px]">@content('hero.cta2_label')</a></div>
      </div>
      <p class="kp-up mt-4 text-[10px] tracking-wide text-white/45 sm:text-[11px]" style="--d:{{ round($d += 0.1, 3) }}s">@content('hero.legal')</p>
    </div>
  </div>

  
   

    @if(content('hero.show_pillars') === '1')
    <div class="relative z-10 mx-auto grid w-full max-w-[1600px] grid-cols-2 gap-px px-4 pb-4 sm:px-5 md:grid-cols-4 md:px-10">
      @foreach($pillars as [$code, $label, $detail])
        <div class="border-t border-white/15 py-3 pr-3"><p class="text-[10px] tracking-[0.3em] text-kp-gold">{{ $code }}</p><p class="mt-1 text-[12px] font-semibold uppercase tracking-[0.14em] text-white">{{ $label }}</p><p class="mt-1 hidden text-[11px] text-white/55 sm:block">{{ $detail }}</p></div>
      @endforeach
    </div>
    @endif
    {{-- Marquee --}}
    <div class="relative overflow-hidden border-t border-white/10 transition-opacity duration-800" :class="pillarsShown ? 'opacity-100' : 'opacity-0'" style="transition: opacity .8s cubic-bezier(.22,1,.36,1)">
      <div class="pointer-events-none">
        <div class="kp-marquee-track flex w-max items-center gap-10 py-2.5 will-change-transform sm:gap-12 sm:py-3 md:gap-16 md:py-4">
          @foreach(array_merge($marquee, $marquee) as $label)
            <span class="flex items-center gap-10 text-[10px] uppercase tracking-[0.4em] text-white sm:gap-12 sm:text-[11px] sm:tracking-[0.5em] md:text-[12px]">
              <span>{{ $label }}</span><span class="text-kp-gold">◆</span>
            </span>
          @endforeach
        </div>
      </div>
      <div class="pointer-events-none absolute inset-y-0 left-0 w-8 bg-gradient-to-r from-black/80 to-transparent sm:w-12 md:w-24"></div>
      <div class="pointer-events-none absolute inset-y-0 right-0 w-8 bg-gradient-to-l from-black/80 to-transparent sm:w-12 md:w-24"></div>
    </div>
  </div>

  {{-- Indicateur Scroll (desktop) --}}
  <div class="pointer-events-none absolute right-6 top-1/2 z-10 hidden -translate-y-1/2 lg:block">
    <div class="flex flex-col items-center gap-3">
      <span class="text-[10px] uppercase tracking-[0.4em] text-white/45 [writing-mode:vertical-rl]">Scroll</span>
      <div class="relative h-24 w-px overflow-hidden bg-white/15">
        <div class="absolute left-0 top-0 w-px bg-kp-gold" :style="`height: ${Math.max(8, Math.round(progress * 100))}%; transition: height 0.15s linear`"></div>
      </div>
      <span class="kp-scroll-dot block h-1.5 w-1.5 rounded-full bg-white/80"></span>
    </div>
  </div>
</section>
<script>
function kpMagnet() {
  return { s: '', move(e) { if (matchMedia('(hover: none)').matches) return; const r = e.currentTarget.getBoundingClientRect();
      this.s = `transform: translate3d(${(e.clientX - r.left - r.width / 2) * 0.18}px, ${(e.clientY - r.top - r.height / 2) * 0.28}px, 0)` },
    reset() { this.s = '' } }
}
function kpHero() {
  const HOLD_MS = 6500, EXIT_MS = 950, RESTART_GAP_MS = 280;
  return {
    progress: 0, watching: true, copyPhase: 'hidden', pillarsShown: false, reduce: false, timer: null, cancelled: false,
    onScroll() {
      const el = this.$refs.wrap; if (!el) return;
      const rect = el.getBoundingClientRect();
      this.progress = Math.min(1, Math.max(0, -rect.top / ((rect.height || 1) * 0.65)));
    },
    async loop() {
      this.copyPhase = 'visible'; this.pillarsShown = true;
      await this.wait(HOLD_MS); if (this.cancelled || !this.watching) return;
      this.copyPhase = 'hidden';
      await this.wait(EXIT_MS + RESTART_GAP_MS); if (this.cancelled || !this.watching) return;
      this.loop();
    },
    wait(ms) { return new Promise(r => { this.timer = setTimeout(r, ms) }) },
    startCycle() {
      clearTimeout(this.timer); this.cancelled = false;
      if (this.reduce) { this.copyPhase = 'visible'; this.pillarsShown = true; return }
      if (!this.watching) return;
      this.loop();
    },
    init() {
      this.reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
      this.onScroll();
      window.addEventListener('scroll', () => this.onScroll(), { passive: true });
      const io = new IntersectionObserver(([e]) => {
        this.watching = e.isIntersecting && e.intersectionRatio >= 0.4;
        if (this.watching) this.startCycle();
        else { this.cancelled = true; clearTimeout(this.timer) }
      }, { threshold: [0.25, 0.4, 0.55, 0.7] });
      io.observe(this.$refs.wrap);
      this.startCycle();
    },
  }
}
</script>
