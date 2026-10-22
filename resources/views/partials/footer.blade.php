{{-- Port Blade de KpFooter (kpandji2026 final/components/kp/KpFooter.tsx) — textes : lib/i18n/messages/footer.ts (fr) ; éditables dans Admin › Contenu › Pied de page --}}
@php
  $panel = 'kpf-panel relative flex flex-col rounded-2xl border border-white/[0.06] bg-kp-elevated/25 p-7 backdrop-blur-sm transition-colors duration-300 hover:border-white/[0.09] hover:bg-kp-elevated/35 md:p-8';
  $flink = 'group -mx-1 inline-flex w-full items-center gap-2.5 rounded-lg px-1 py-2 text-sm text-white/60 transition-all duration-300 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/50 focus-visible:ring-offset-2 focus-visible:ring-offset-kp-elevated';
  $cline = 'group flex items-start gap-2.5 text-sm text-white/50 transition-colors duration-300 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/50 focus-visible:ring-offset-2 focus-visible:ring-offset-kp-elevated';
  $iwrap = 'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-kp-gold/20 bg-kp-gold/[0.08] text-kp-gold shadow-[inset_0_1px_0_rgba(255,255,255,0.06)] transition-colors duration-300 group-hover:border-kp-gold/35 group-hover:bg-kp-gold/[0.12]';
  $label = fn ($t) => '<div class="flex items-center gap-3"><span class="h-px w-10 shrink-0 bg-gradient-to-r from-kp-gold/80 to-kp-gold/20" aria-hidden="true"></span><p class="font-sans text-[10px] font-semibold uppercase tracking-[0.32em] text-white/45">'.e($t).'</p></div>';
  $tel = fn ($d) => preg_replace('/[^\d+]/', '', (string) $d);
  $email = content('footer.email');
@endphp
<footer id="contact" class="kpf relative isolate mt-24 overflow-hidden border-t border-white/[0.07] bg-kp-bg">
  {!! cms_edit('footer', 'Modifier le pied de page', '1rem') !!}
  <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-kp-surface"></div>
  <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 kpf-drift bg-[radial-gradient(1000px_520px_at_8%_-10%,rgba(201,169,98,0.11),transparent_58%),radial-gradient(820px_480px_at_92%_110%,rgba(255,255,255,0.04),transparent_52%)]"></div>
  <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[linear-gradient(rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.03)_1px,transparent_1px)] bg-[length:64px_64px] opacity-[0.35]"></div>
  <div aria-hidden="true" class="kpf-glow2 pointer-events-none absolute -z-10"></div>
  <div aria-hidden="true" class="kpf-gridshine pointer-events-none absolute inset-0 -z-10"></div>
  <div aria-hidden="true" class="kpf-hair absolute inset-x-0 top-0 h-px overflow-hidden bg-gradient-to-r from-transparent via-kp-gold/50 to-transparent"><span class="kpf-sweep"></span></div>

  <div class="mx-auto max-w-[1600px] px-5 pb-12 pt-16 md:px-10 md:pb-16 md:pt-20">
    <div class="grid gap-12 lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1.5fr)] lg:items-start lg:gap-16 xl:gap-20">
      {{-- Colonne marque --}}
      <div data-kp-reveal class="relative min-w-0 lg:row-span-2" style="--rd:0ms">
        <div aria-hidden="true" class="absolute -left-5 bottom-0 top-0 w-px bg-gradient-to-b from-kp-gold/50 via-white/10 to-transparent md:-left-6"></div>
        <div class="kpf-panel relative flex h-full flex-col rounded-2xl border border-white/[0.06] bg-kp-elevated/40 p-8 shadow-[0_24px_80px_-32px_rgba(0,0,0,0.85)] backdrop-blur-md md:p-10">
          <a href="{{ url('/') }}#accueil" class="kpf-logo relative inline-block overflow-hidden rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/50 focus-visible:ring-offset-2 focus-visible:ring-offset-kp-elevated">
            <img src="{{ cms_asset(content('header.logo')) }}" alt="KPANDJI AUTOMOBILES" width="200" height="58" class="h-11 w-auto opacity-95 md:h-[52px]"><span class="kpf-logo-shine" aria-hidden="true"></span>
          </a>
          @if(content('footer.manufacturer'))<p class="mt-3 font-sans text-[10px] font-semibold uppercase tracking-[0.34em] text-kp-gold">@content('footer.manufacturer')</p>@endif
          @if(content('footer.headline'))<p class="kpf-head mt-8 font-serif text-2xl font-medium leading-[1.2] tracking-[-0.02em] text-white md:text-[1.75rem]">@foreach(preg_split('/\s+/u', trim(content('footer.headline')), -1, PREG_SPLIT_NO_EMPTY) as $hw)<span class="kpf-w" style="--w:{{ $loop->index }}">{{ $hw }}</span> @endforeach</p>@endif
          @if(content('footer.blurb'))<p class="mt-5 font-sans text-[15px] leading-relaxed text-kp-muted">@content('footer.blurb')</p>@endif

          @if(content('footer.seat_address'))
          <div class="kpf-item mt-8 flex items-start gap-3 rounded-xl border border-white/[0.06] bg-black/20 px-4 py-3.5" style="--k:9">
            <span class="kpf-pin relative {{ $iwrap }}"><svg class="h-[15px] w-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></span>
            <div class="min-w-0">
              <p class="font-sans text-[10px] font-semibold uppercase tracking-[0.2em] text-kp-gold/75">@content('footer.seat_label')</p>
              <p class="mt-0.5 font-sans text-sm text-white/75">@content('footer.seat_address')</p>
            </div>
          </div>
          @endif

          @if(content('footer.write_cta'))
          <div class="kpf-item mt-auto pt-10" style="--k:11">
            <a href="{{ cms_url(content('footer.write_url')) }}" class="kpf-cta relative overflow-hidden inline-flex w-full items-center justify-center rounded-xl border border-kp-gold/35 bg-gradient-to-br from-kp-gold/15 to-transparent px-8 py-4 font-sans text-[11px] font-semibold uppercase tracking-[0.24em] text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.06)] transition duration-300 hover:border-kp-gold/55 hover:from-kp-gold/25 hover:shadow-[0_0_0_1px_rgba(201,169,98,0.12),0_20px_50px_-24px_rgba(201,169,98,0.35)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/50 sm:w-auto">@content('footer.write_cta')</a>
          </div>
          @endif
        </div>
      </div>

      {{-- Colonne droite — Ressources & Contact --}}
      <div class="grid min-w-0 gap-5 sm:grid-cols-2 lg:col-start-2 lg:row-start-1 lg:items-start">
        <div data-kp-reveal class="{{ $panel }} w-full min-w-0" style="--rd:40ms">
          {!! $label(content('footer.resources')) !!}
          <ul class="mt-8 space-y-1">
            @foreach(collect(content('footer.resource_links'))->filter(fn ($l) => filled($l['label'] ?? null) && filled($l['url'] ?? null)) as $l)
              <li class="kpf-item" style="--k:{{ $loop->index + 1 }}">
                <a href="{{ cms_url($l['url']) }}" class="{{ $flink }}" target="_blank" rel="noopener noreferrer">
                  <svg class="kpf-ico shrink-0 text-kp-gold/75" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
                  <span class="flex min-w-0 flex-1 items-center gap-2"><span class="kpf-ul">{{ $l['label'] }}</span>
                    <svg class="kpf-nudge ml-auto shrink-0 text-white/25 transition-colors group-hover:text-kp-gold/70" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14L21 3"/></svg>
                  </span>
                </a>
              </li>
            @endforeach
          </ul>
          <div class="mt-auto pt-8" aria-hidden="true">
            <div class="h-px bg-gradient-to-r from-kp-gold/30 via-white/[0.08] to-transparent"></div>
            @if(content('footer.resources_hint'))<p class="mt-5 font-sans text-[12px] leading-relaxed text-white/[0.32]">@content('footer.resources_hint')</p>@endif
          </div>
        </div>

        <div data-kp-reveal class="{{ $panel }} w-full min-w-0" style="--rd:80ms">
          {!! $label(content('footer.contact')) !!}
          <address class="mt-8 not-italic">
            <ul class="space-y-5" role="list">
              @foreach(collect(content('footer.phones'))->filter(fn ($p) => filled($p['number'] ?? null)) as $p)
                <li class="kpf-item" style="--k:{{ $loop->index + 1 }}">
                  <a href="tel:{{ $tel($p['number']) }}" class="{{ $cline }}">
                    <svg class="kpf-ico mt-0.5 shrink-0 text-kp-gold/75" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span><span>{{ $p['label'] ?? '' }}</span><span class="text-white/35"> : </span><span class="kpf-ul tabular-nums tracking-wide">{{ $p['number'] }}</span></span>
                  </a>
                </li>
              @endforeach
              @if($email)
              <li class="kpf-item" style="--k:6">
                <a href="mailto:{{ $email }}" class="{{ $cline }}">
                  <svg class="kpf-ico mt-0.5 shrink-0 text-kp-gold/75" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="m22 6-10 7L2 6"/></svg>
                  <span><span>@content('footer.email_label')</span><span class="text-white/35"> : </span><span class="kpf-ul break-all">{{ $email }}</span></span>
                </a>
              </li>
              @endif
            </ul>
          </address>
        </div>
      </div>
    </div>
  </div>

  {{-- Barre du bas --}}
  <div class="kpf-bar relative border-t border-white/[0.06] bg-black/35">
    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/[0.08] to-transparent"></div>
    <div class="mx-auto flex max-w-[1600px] flex-col gap-6 px-5 py-8 md:flex-row md:items-center md:justify-between md:px-10 md:py-9">
      <p class="max-w-xl font-sans text-[12px] leading-relaxed text-white/[0.38]">© {{ date('Y') }} KPANDJI AUTOMOBILES. @content('footer.rights') <a href="{{ route('login') }}" class="ml-2 text-white/15 hover:text-white/50" rel="nofollow">Admin</a></p>
      <a href="{{ request()->routeIs('home') ? '#accueil' : '#' }}" data-kpf-top class="kpf-top group inline-flex items-center gap-2.5 self-start rounded-full border border-white/10 bg-white/[0.03] px-5 py-2.5 font-sans text-[11px] font-semibold uppercase tracking-[0.22em] text-white/55 transition-all duration-300 hover:border-kp-gold/30 hover:bg-kp-gold/10 hover:text-kp-gold md:self-auto">
        <span class="flex h-7 w-7 items-center justify-center rounded-full border border-white/10 bg-white/[0.04] transition-colors group-hover:border-kp-gold/25 group-hover:bg-kp-gold/15">
          <svg class="kpf-arrow text-kp-gold/70" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 15l-6-6-6 6"/></svg>
        </span>
        @content('footer.back_to_top')
      </a>
    </div>
  </div>
</footer>
<style>
/* ===== Animations du pied de page (luxe, discrètes) ===== */
.kpf{--ease:cubic-bezier(.22,1,.36,1)}
.kpf-hair{transform-origin:50% 50%}
.kpf-sweep{position:absolute;top:0;left:0;height:1px;width:22%;background:linear-gradient(90deg,transparent,#fff3cf,transparent);opacity:0}
.kpf-drift{animation:kpf-drift 26s ease-in-out infinite alternate}
@keyframes kpf-drift{to{transform:translate3d(3%,2%,0) scale(1.04)}}
.kpf-glow2{right:-10%;top:-20%;width:620px;height:620px;border-radius:9999px;background:radial-gradient(closest-side,rgba(201,169,98,.07),transparent);animation:kpf-glow2 32s ease-in-out infinite alternate}
@keyframes kpf-glow2{to{transform:translate3d(-18%,30%,0)}}
.kpf-gridshine{background:linear-gradient(rgba(201,169,98,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(201,169,98,.06) 1px,transparent 1px);background-size:64px 64px;-webkit-mask-image:linear-gradient(105deg,transparent 35%,#000 50%,transparent 65%);mask-image:linear-gradient(105deg,transparent 35%,#000 50%,transparent 65%);-webkit-mask-size:300% 100%;mask-size:300% 100%;-webkit-mask-position:100% 0;mask-position:100% 0;animation:kpf-grid 14s linear infinite}
@keyframes kpf-grid{to{-webkit-mask-position:0 0;mask-position:0 0}}
/* panneaux : survol + projecteur */
.kpf-panel{transition:transform .5s var(--ease),border-color .4s,box-shadow .5s,background-color .4s}
.kpf-panel::before{content:"";position:absolute;inset:0;border-radius:inherit;pointer-events:none;opacity:0;transition:opacity .4s;background:radial-gradient(380px circle at var(--mx,50%) var(--my,0%),rgba(201,169,98,.10),transparent 60%)}
.kpf-panel>*{position:relative}
.kpf-panel:hover{transform:translateY(-3px);border-color:rgba(201,169,98,.28)!important;box-shadow:0 0 0 1px rgba(201,169,98,.08),0 26px 70px -34px rgba(201,169,98,.35)}
.kpf-panel:hover::before{opacity:1}
/* logo */
.kpf-logo-shine{position:absolute;inset:-20% auto -20% -60%;width:40%;transform:skewX(-20deg);background:linear-gradient(90deg,transparent,rgba(255,240,200,.45),transparent);opacity:0;pointer-events:none}
/* titre */
.kpf-w{display:inline-block}
/* liens */
.kpf-ico,.kpf-nudge{transition:transform .35s var(--ease),color .3s}
.group:hover .kpf-ico{transform:translateX(3px)}
.group:hover .kpf-nudge{transform:translate(2px,-2px)}
.kpf-ul{background:linear-gradient(90deg,#c9a962,#f3e2b3) no-repeat 0 100%/0 1px;transition:background-size .45s var(--ease);padding-bottom:1px}
.group:hover .kpf-ul,.group:focus-visible .kpf-ul{background-size:100% 1px}
/* pin */
.kpf-pin::after{content:"";position:absolute;inset:-1px;border-radius:.5rem;border:1px solid rgba(201,169,98,.5);animation:kpf-pin 2.8s ease-out infinite;pointer-events:none}
@keyframes kpf-pin{0%{transform:scale(1);opacity:.7}80%,100%{transform:scale(1.45);opacity:0}}
/* CTA */
.kpf-cta::after{content:"";position:absolute;top:0;bottom:0;left:-60%;width:40%;transform:skewX(-20deg);background:linear-gradient(90deg,transparent,rgba(255,240,200,.28),transparent);pointer-events:none;opacity:0}
.kpf-cta:hover::after{opacity:1;animation:kpf-shine 1.6s var(--ease) infinite}
@keyframes kpf-shine{0%{left:-60%}60%,100%{left:130%}}
/* retour en haut */
.kpf-top:hover .kpf-arrow{animation:kpf-bounce .9s var(--ease) infinite}
@keyframes kpf-bounce{0%,100%{transform:translateY(0)}45%{transform:translateY(-4px)}}

/* ===== États d'entrée (uniquement si JS actif et mouvement autorisé) ===== */
.kpf-js .kpf-hair{transform:scaleX(0);transition:transform 1.4s var(--ease)}
.kpf-js.kpf-on .kpf-hair{transform:scaleX(1)}
.kpf-js.kpf-on .kpf-sweep{animation:kpf-travel 2.2s var(--ease) 1.2s 1 both}
@keyframes kpf-travel{0%{transform:translateX(-100%);opacity:0}15%{opacity:1}85%{opacity:1}100%{transform:translateX(560%);opacity:0}}
.kpf-js [data-kp-reveal]{opacity:0;transform:translate3d(0,32px,0);filter:blur(8px);transition:opacity .9s var(--ease) var(--rd,0ms),transform 1s var(--ease) var(--rd,0ms),filter .9s ease var(--rd,0ms)}
.kpf-js [data-kp-reveal].kpf-in{opacity:1;transform:none;filter:none}
.kpf-js .kpf-item{opacity:0;transform:translate3d(0,12px,0);transition:opacity .7s var(--ease),transform .8s var(--ease);transition-delay:calc(var(--rd,0ms) + 260ms + var(--k,0)*70ms)}
.kpf-js .kpf-in .kpf-item{opacity:1;transform:none}
.kpf-js .kpf-w{opacity:0;transform:translate3d(0,.45em,0);filter:blur(4px);transition:opacity .7s var(--ease),transform .8s var(--ease),filter .7s ease;transition-delay:calc(420ms + var(--w)*55ms)}
.kpf-js .kpf-in .kpf-w{opacity:1;transform:none;filter:none}
.kpf-js .kpf-logo img{opacity:0;transform:scale(.94);transition:opacity 1s var(--ease) .15s,transform 1.2s var(--ease) .15s}
.kpf-js .kpf-in .kpf-logo img{opacity:.95;transform:none}
.kpf-js .kpf-in .kpf-logo-shine{animation:kpf-logo 1.6s var(--ease) .9s 1 both}
@keyframes kpf-logo{0%{left:-60%;opacity:1}100%{left:130%;opacity:1}}
.kpf-js .kpf-bar{opacity:0;transition:opacity 1.1s ease .2s}
.kpf-js .kpf-bar.kpf-in{opacity:1}

@media (prefers-reduced-motion:reduce){
  .kpf *,.kpf *::before,.kpf *::after{animation:none!important;transition:none!important}
  .kpf-panel:hover{transform:none}
}
</style>
<script>
// Pied de page : révélations (une fois), projecteur au survol, retour en haut fluide
(() => {
  const f = document.querySelector('footer.kpf'); if (!f) return;
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  f.querySelectorAll('[data-kpf-top]').forEach(a => a.addEventListener('click', e => {
    e.preventDefault(); window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
    if (a.getAttribute('href').startsWith('#') && a.getAttribute('href').length > 1) history.replaceState(null, '', a.getAttribute('href'));
  }));
  if (matchMedia('(hover: hover)').matches) f.querySelectorAll('.kpf-panel').forEach(p => p.addEventListener('pointermove', e => {
    const r = p.getBoundingClientRect(); p.style.setProperty('--mx', (e.clientX - r.left) + 'px'); p.style.setProperty('--my', (e.clientY - r.top) + 'px');
  }, { passive: true }));
  if (reduce || !('IntersectionObserver' in window)) return;
  f.classList.add('kpf-js');
  const els = [...f.querySelectorAll('[data-kp-reveal], .kpf-bar')];
  const io = new IntersectionObserver(es => es.forEach(e => {
    if (!e.isIntersecting) return;
    e.target.classList.add('kpf-in'); io.unobserve(e.target);
  }), { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
  els.forEach(el => io.observe(el));
  const io2 = new IntersectionObserver(es => { if (es[0].isIntersecting) { f.classList.add('kpf-on'); io2.disconnect() } }, { threshold: 0 });
  io2.observe(f);
})();
</script>
