@php($end = config('promo.ends_at') ? \Carbon\Carbon::parse(config('promo.ends_at'))->endOfDay() : null)
@php($pct = config('promo.reduction_percent'))

<section id="offres" class="relative scroll-mt-24 overflow-hidden border-b border-white/10 py-24 md:py-32">
  <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(900px_400px_at_50%_0%,rgba(201,169,98,0.16),transparent_70%)]"></div>
  {!! cms_edit('promo', 'Modifier cette section', '5.5rem') !!}
  <div class="relative mx-auto max-w-[1400px] px-5 md:px-10">
    <div class="mx-auto max-w-5xl text-center">
      
   

@php($kpW = fn ($s) => preg_split('/\s+/u', trim((string) $s), -1, PREG_SPLIT_NO_EMPTY))
@php($kpI = 0)
@php($kpEb = config('promo.reduction_percent') > 0 ? str_replace(':pct', config('promo.reduction_percent'), content('promo.eyebrow_pct')) : content('promo.title_eyebrow'))
<style>
.kph{position:relative;isolation:isolate}
.kph-glow{position:absolute;left:50%;top:46%;width:min(900px,95vw);height:300px;transform:translate(-50%,-50%);background:radial-gradient(closest-side,rgba(201,169,98,.20),rgba(201,169,98,.06) 55%,transparent);filter:blur(10px);z-index:-1;pointer-events:none}
.kph-eb{font-family:Montserrat,sans-serif;letter-spacing:.42em}
.kph-diamond{width:6px;height:6px;transform:rotate(45deg);background:linear-gradient(135deg,#f3e2b3,#c9a962);box-shadow:0 0 12px rgba(201,169,98,.7)}
.kph-l1{font-size:clamp(3rem,9vw,7.5rem);line-height:.95;letter-spacing:-.025em}
.kph-l2{font-size:clamp(1.65rem,4.6vw,3.6rem);line-height:1.12;letter-spacing:-.01em}
.kph-w{display:inline-block;opacity:0;transform:translateY(.55em);filter:blur(6px);transition:opacity .8s cubic-bezier(.2,.7,.2,1),transform .9s cubic-bezier(.2,.7,.2,1),filter .8s ease;transition-delay:calc(var(--i)*75ms + 120ms)}
.kph.is-in .kph-w{opacity:1;transform:none;filter:none}
.kph-hl{position:relative;display:inline-block;font-style:italic;white-space:nowrap;background:linear-gradient(100deg,#c9a962,#f3e2b3 50%,#c9a962);-webkit-background-clip:text;background-clip:text;color:transparent;padding:0 .08em}
.kph.is-in .kph-line{transform:scaleX(0);transition:transform 1.2s cubic-bezier(.65,0,.35,1) .2s}
.kph.is-in .kph-line{transform:scaleX(1)}
@media (prefers-reduced-motion:reduce){.kph-w,.kph-line{transition:none!important;opacity:1!important;transform:none!important;filter:none!important}}
</style>
<div class="kph flex flex-col items-center" x-data x-init="const el=$el; if(/HeadlessChrome|bot|crawl|spider/i.test(navigator.userAgent)||matchMedia('(prefers-reduced-motion: reduce)').matches||!('IntersectionObserver' in window)){el.classList.add('is-in');return} const io=new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting){el.classList.add('is-in');io.disconnect()}}),{threshold:.25}); io.observe(el)">
  <div class="kph-glow" aria-hidden="true"></div>
  @if(trim((string) $kpEb) !== '')
  <div class="kph-eb flex items-center gap-4 text-[10px] font-semibold uppercase text-kp-gold sm:text-[11px]">
    <span class="kph-line h-px w-10 origin-right bg-gradient-to-r from-transparent to-kp-gold sm:w-24"></span>
    <span class="kph-diamond"></span><span>{{ $kpEb }}</span><span class="kph-diamond"></span>
    <span class="kph-line h-px w-10 origin-left bg-gradient-to-l from-transparent to-kp-gold sm:w-24"></span>
  </div>
  @endif
  <h2 class="mt-6 font-serif">
    <span class="sr-only">{{ trim(content('promo.word1').' '.content('promo.word2').' '.content('promo.word3')) }}</span>
    <span aria-hidden="true" class="kph-l1 block font-semibold">@foreach($kpW(content('promo.word1')) as $w)<span class="kph-w" style="--i:{{ $kpI++ }}"><span class="kp-gold-shimmer bg-clip-text text-transparent">{{ $w }}</span></span> @endforeach</span>
    <span aria-hidden="true" class="kph-l2 mt-3 block font-medium text-white/95 sm:mt-4">@foreach($kpW(content('promo.word2')) as $w)<span class="kph-w" style="--i:{{ $kpI++ }}">{{ $w }}</span> @endforeach @if(trim((string) content('promo.word3')) !== '')<span class="kph-w" style="--i:{{ $kpI++ }}"><span class="kph-hl">{{ content('promo.word3') }}</span></span>@endif</span>
  </h2>
  <div class="mt-8 flex w-full max-w-[16rem] items-center gap-3 sm:max-w-sm" aria-hidden="true">
    <span class="kph-line h-px flex-1 origin-right bg-gradient-to-r from-transparent via-kp-gold/70 to-kp-gold"></span>
    <span class="kph-diamond"></span>
    <span class="kph-line h-px flex-1 origin-left bg-gradient-to-l from-transparent via-kp-gold/70 to-kp-gold"></span>
  </div>
  @if(content('promo.intro'))<p class="mx-auto mt-6 max-w-2xl text-base leading-relaxed text-white/70 sm:text-lg">@content('promo.intro')</p>@endif
</div>

      @if($end && $end->isFuture())
      <div x-data="{ t:0, end: {{ $end->timestamp * 1000 }}, tick(){ this.t = Math.max(0, this.end - Date.now()) } }" x-init="tick(); setInterval(() => tick(), 1000)" class="mt-10 inline-flex items-center gap-2 sm:gap-4">
        @php($cdl = collect(content('countdown.labels'))->pluck('label')->values()->all())
        @foreach([['j', 86400000, null, $cdl[0] ?? 'Jours'], ['h', 3600000, 24, $cdl[1] ?? 'Heures'], ['m', 60000, 60, $cdl[2] ?? 'Min'], ['s', 1000, 60, $cdl[3] ?? 'Sec']] as [$k, $div, $mod, $lbl])
          <div class="min-w-[70px] rounded-xl border border-white/10 bg-white/[0.04] px-3 py-3 sm:min-w-[88px]">
            <p class="font-serif text-3xl font-semibold tabular-nums text-white sm:text-4xl" x-text="String(Math.floor(t / {{ $div }}){{ $mod ? ' % '.$mod : '' }}).padStart(2,'0')">00</p>
            <p class="mt-1 text-[10px] uppercase tracking-[0.25em] text-white/45">{{ $lbl }}</p>
          </div>
        @endforeach
      </div>
      <p class="mt-3 text-xs uppercase tracking-[0.25em] text-white/45">{{ content('promo.until') }} {{ $end->format('d/m/Y') }}</p>
      @endif
    </div>
    @if(cms_is_admin())<div class="relative mx-auto max-w-[1200px]">{!! cms_edit(route('admin.cars.index'), 'Modifier les voitures et prix', '1.5rem') !!}</div>@endif
    <div class="relative mx-auto mt-14 max-w-[1200px] md:mt-20" @mouseenter="paused = true" @mouseleave="paused = false" @touchstart.passive="paused = true; clearTimeout(rt); rt = setTimeout(() => paused = false, 9000)"
      x-init="if (!matchMedia('(prefers-reduced-motion: reduce)').matches) setInterval(() => { if (!paused && !document.hidden) go((cur + 1) % n) }, 6000)"
      x-data="{ paused: false, rt: null, p: 0, cur: 0, n: {{ $cars->count() }}, step(d){ const t=this.$refs.track; const c=t.querySelector('[data-slide]'); t.scrollBy({ left: d * ((c ? c.offsetWidth : 400) + 24), behavior: 'smooth' }) },
      sync(){ const t=this.$refs.track; const m = t.scrollWidth - t.clientWidth; this.p = m > 0 ? t.scrollLeft / m : 0; const c=t.querySelector('[data-slide]'); this.cur = c ? Math.round(t.scrollLeft / (c.offsetWidth + 24)) : 0 },
      go(i){ const t=this.$refs.track; const c=t.querySelectorAll('[data-slide]')[i]; if (c) t.scrollTo({ left: c.offsetLeft - t.firstElementChild.offsetLeft, behavior: 'smooth' }) } }">
      <span x-init="$nextTick(() => sync())" class="hidden"></span>
      <div x-ref="track" @scroll.passive="sync()" class="kp-hide-scrollbar -mx-5 flex snap-x snap-mandatory gap-6 overflow-x-auto scroll-smooth px-5 pb-4 md:-mx-10 md:px-10">
        @foreach($cars as $car)
          <div data-slide class="w-full shrink-0 snap-center">@include('partials.card', ['i' => $loop->index])</div>
        @endforeach
        <div class="w-1 shrink-0" aria-hidden="true"></div>
      </div>
      <div class="mt-6 flex items-center gap-5">
        <div class="flex min-w-0 flex-1 items-center gap-4">
          <div class="relative h-[3px] flex-1 overflow-hidden rounded-full bg-white/10" aria-hidden="true"><div class="kp-progress absolute inset-y-0 left-0 w-full origin-left rounded-full bg-gradient-to-r from-[#a8863f] via-kp-gold to-[#f1dca0]" :style="`transform: scaleX(${Math.max(0.06, p)})`"></div></div>
          <div class="hidden items-center gap-1.5 sm:flex">
            @foreach($cars as $c)<button type="button" @click="go({{ $loop->index }})" class="h-2 rounded-full transition-all duration-500" :class="cur === {{ $loop->index }} ? 'w-6 bg-kp-gold' : 'w-2 bg-white/25 hover:bg-white/50'" aria-label="{{ $c->name }}"></button>@endforeach
          </div>
        </div>
        <div class="hidden shrink-0 gap-3 md:flex">
        <button type="button" @click="step(-1)" aria-label="@content('promo.prev')" class="flex h-12 w-12 items-center justify-center rounded-full bg-kp-gold text-black shadow-[0_8px_30px_rgba(201,169,98,0.4)] transition hover:scale-105 hover:bg-white"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
        <button type="button" @click="step(1)" aria-label="@content('promo.next')" class="flex h-12 w-12 items-center justify-center rounded-full bg-kp-gold text-black shadow-[0_8px_30px_rgba(201,169,98,0.4)] transition hover:scale-105 hover:bg-white"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
      </div>
      </div>
    </div>
    <p class="mt-8 text-center text-xs text-white/35">@content('promo.note')</p>
  </div>
</section>
