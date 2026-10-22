{{-- Carte showroom premium — données réelles (admin) uniquement --}}
@php
  $flat = collect($car->specs ?? [])->flatMap(fn ($rows) => is_array($rows) ? $rows : []);
  $v0 = ($car->versions ?? [])[0] ?? [];
  $pick = fn (array $keys) => collect($keys)->map(fn ($k) => $flat[$k] ?? null)->filter()->first();
  $isGear = fn ($t) => (bool) preg_match('/auto|manu|rapport|bv|cvt|vitesse/i', (string) $t);
  $specIcons = array_filter([
    ['engine', $pick(['Moteur', 'Motorisation', 'Moteur électrique']) ?? ($v0['motorisation'] ?? null)],
    ['gear', $pick(['Boîte', 'Boîte de vitesses']) ?? ($v0['boite'] ?? null) ?? ($isGear($flat['Transmission'] ?? '') ? $flat['Transmission'] : null)],
    ['drive', ($v0['transmission'] ?? null) ?? $pick(['Traction']) ?? (! $isGear($flat['Transmission'] ?? '') ? ($flat['Transmission'] ?? null) : null)],
    ['shield', $car->warranty],
  ], fn ($s) => filled($s[1]));
  $endsAt = config('promo.ends_at') ? \Carbon\Carbon::parse(config('promo.ends_at'))->endOfDay() : null;
  $idx = $i ?? 0;
@endphp
<article x-data="kpCard()" x-init="init()" @mousemove="par($event)" @mouseleave="unpar()" :class="inView && 'is-in'" :style="parStyle"
         class="kp-card kp-split group relative grid h-full overflow-hidden rounded-[26px] md:grid-cols-[58fr_42fr]" style="--ci:{{ $idx }}">
  <span class="kp-card-sweep" aria-hidden="true"></span>
  <div class="kp-corner" aria-hidden="true"><span>@if(config('promo.reduction_percent') > 0) -{{ config('promo.reduction_percent') }} % @else @content('card.corner') @endif</span></div>

  {{-- Côté voiture --}}
  <a href="{{ route('cars.show', $car) }}" class="kp-stage relative flex h-[340px] items-center justify-center overflow-hidden px-2 pb-0 pt-4 sm:h-[30rem] md:h-auto md:min-h-[500px] md:px-6 md:py-8 lg:min-h-[560px]" aria-label="{{ $car->name }}">
    <span class="kp-spot" aria-hidden="true"></span>
    <span class="kp-floor" aria-hidden="true"></span>
    <div class="kp-car-slide relative z-[1] h-full w-full">
      <div class="kp-float h-full w-full">
        <img src="{{ asset($car->image) }}" alt="{{ $car->name }}" loading="lazy" class="kp-car-img absolute inset-0 h-full w-full object-contain">
      </div>
    </div>
  </a>

  {{-- Côté texte & prix --}}
  <div class="relative z-10 flex flex-col justify-center px-6 pb-7 pt-4 md:py-10 md:pl-2 md:pr-10">
    <div class="kp-rv flex flex-wrap items-center justify-between gap-2" style="--ri:0">
      <p class="text-[11px] font-semibold uppercase tracking-[0.34em] text-kp-gold/85">{{ $car->category }}</p>
      @if(config('promo.active', true))
        <span class="kp-urgency inline-flex items-center gap-1.5 rounded-full border border-kp-gold/35 bg-kp-gold/10 px-2.5 py-1 text-[9px] font-bold uppercase tracking-[0.18em] text-kp-gold"
          @if($endsAt && $endsAt->isFuture()) x-data="{ t:0, end: {{ $endsAt->timestamp * 1000 }}, tick(){ this.t = Math.max(0, this.end - Date.now()) } }" x-init="tick(); setInterval(() => tick(), 30000)" @endif>
          <span class="relative flex h-1.5 w-1.5"><span class="kp-ping absolute inline-flex h-full w-full rounded-full bg-kp-gold/70"></span><span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-kp-gold"></span></span>
          @content('promo.eyebrow')
          @if($endsAt && $endsAt->isFuture())<span class="tabular-nums text-white/70" x-text="'· ' + Math.floor(t/86400000) + 'j ' + String(Math.floor(t/3600000)%24).padStart(2,'0') + 'h'">· {{ now()->diffInDays($endsAt, false) >= 0 ? (int) now()->diffInDays($endsAt) : 0 }}j</span>@endif
        </span>
        @endif
    </div>
    <h3 class="kp-rv mt-2 font-serif text-4xl font-semibold leading-[1.02] text-white lg:text-5xl" style="--ri:1"><a href="{{ route('cars.show', $car) }}" class="transition-colors hover:text-kp-gold">{{ $car->name }}</a></h3>
    @if($specIcons)
    <ul class="kp-rv mt-5 grid grid-cols-2 gap-2" style="--ri:2">
      @foreach($specIcons as [$ic, $val])
        <li class="kp-spec flex min-w-0 items-center gap-2.5 rounded-xl border border-white/[0.07] bg-white/[0.03] px-3 py-2" style="--si:{{ $loop->index }}">
          <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-kp-gold/10 text-kp-gold">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              @switch($ic)
                @case('engine')<path d="M4 10h2l2-3h6l2 3h2v3h2v-2h1v6h-1v-2h-2v3h-3l-2 2H9l-2-2H4z"/>@break
                @case('gear')<circle cx="6" cy="6" r="2"/><circle cx="12" cy="6" r="2"/><circle cx="18" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="12" cy="18" r="2"/><path d="M6 8v8M12 8v8M18 8v4H6"/>@break
                @case('drive')<circle cx="6" cy="6" r="2.5"/><circle cx="18" cy="6" r="2.5"/><circle cx="6" cy="18" r="2.5"/><circle cx="18" cy="18" r="2.5"/><path d="M8.5 6h7M8.5 18h7M12 6v12"/>@break
                @default<path d="M12 3l7 3v5c0 5-3.5 8.5-7 10-3.5-1.5-7-5-7-10V6z"/><path d="m9 12 2 2 4-4"/>
              @endswitch
            </svg>
          </span>
          <span class="truncate text-[11px] leading-tight text-white/75" title="{{ $val }}">{{ $val }}</span>
        </li>
      @endforeach
    </ul>
    @endif

    <div class="kp-rv -mx-2 mt-6" style="--ri:3">@include('partials.price')</div>
    <div class="kp-rv mt-7 flex flex-wrap items-center gap-x-6 gap-y-3" style="--ri:4">
        <a href="{{ route('essai', ['modele' => $car->slug]) }}" class="kp-cta-pulse relative whitespace-nowrap rounded-full bg-gradient-to-r from-[#e3c77f] via-kp-gold to-[#a8863f] px-7 py-3.5 text-[11px] font-bold uppercase tracking-[0.18em] text-black transition hover:brightness-110">@content('card.cta')</a>
        <a href="{{ route('cars.show', $car) }}" class="group/d inline-flex items-center gap-2 whitespace-nowrap text-[11px] font-semibold uppercase tracking-[0.18em] text-white/70 transition hover:text-kp-gold">{{ trim(str_replace('→', '', content('card.discover'))) }}<span class="inline-block transition-transform duration-300 group-hover/d:translate-x-1.5" aria-hidden="true">→</span></a>
    </div>
  </div>
</article>
