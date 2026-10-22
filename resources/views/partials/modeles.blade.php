{{-- Port Blade de components/kp/Modeles.tsx (kpandji2026 final) — données : Admin › Modèles (accueil) ; titre : Admin › Contenu › Modèles --}}
@php($kpModeles = \App\Models\Modele::visible()->get())
@php($kpEn = app()->getLocale() === 'en')
@php($kpTr = fn ($fr, $en) => $kpEn ? $en : $fr)
@if($kpModeles->isNotEmpty())
<section id="modeles" aria-label="{{ $kpTr('Modèles', 'Models') }}" class="relative w-full scroll-mt-28 border-t border-white/[0.06] bg-kp-bg">
  {!! cms_edit(route('admin.modeles.index'), 'Modifier cette section', '5.5rem') !!}
  <div class="mx-auto max-w-[1600px] px-5 pt-14 md:px-10 md:pt-[4.5rem]">
    <div data-kpm-reveal>
      <header class="mx-auto max-w-3xl text-center">
        <span class="mx-auto block h-px w-28 bg-gradient-to-r from-transparent via-kp-gold/90 to-transparent md:w-56" aria-hidden="true"></span>
        <h2 class="mt-5 font-serif text-[clamp(2.1rem,5.4vw,3.35rem)] font-medium leading-[1.04] tracking-[-0.03em] text-kp-accent">{{ $kpEn ? (content('modeles.title_en') ?: content('modeles.title')) : content('modeles.title') }}</h2>
      </header>
    </div>
  </div>

  <div class="mt-10">
    @foreach($kpModeles as $index => $item)
      @php($flipped = $index % 2 === 1)
      @php($eager = $index < 3)
      @php($top = '<div class="grid grid-cols-1 gap-4 md:h-[360px] md:grid-cols-3 lg:h-[420px]">'
        .'<div class="aspect-video md:col-span-1 md:aspect-auto md:h-full">'.view('partials.modeles-tile', ['src' => $item->top_image, 'alt' => $item->name.' — '.$kpTr('visuel principal', 'main visual'), 'eager' => $eager])->render().'</div>'
        .'<div class="aspect-video md:col-span-2 md:aspect-auto md:h-full">'.view('partials.modeles-tile', ['video' => $item->top_video, 'alt' => $item->name])->render().'</div></div>')
      @php($bottom = '<div class="grid grid-cols-1 gap-4 md:grid-cols-2'.($flipped ? '' : ' mt-3.5').'">'
        .'<div class="aspect-[16/10]">'.view('partials.modeles-tile', ['src' => $item->bottom_left_image, 'alt' => $item->name.' — '.$kpTr('intérieur', 'interior')])->render().'</div>'
        .'<div class="aspect-[16/10]">'.view('partials.modeles-tile', ['src' => $item->bottom_right_image, 'alt' => $item->name.' — '.$kpTr('extérieur', 'exterior'), 'eager' => $loop->last])->render().'</div></div>')
      <section aria-label="{{ $kpTr('Modèle '.$item->name, $item->name.' model') }}" class="relative w-full scroll-mt-28 border-t border-white/[0.06] bg-kp-bg py-10 md:py-14">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(900px_360px_at_20%_20%,rgba(201,169,98,0.10),transparent_60%),radial-gradient(900px_360px_at_80%_70%,rgba(255,255,255,0.05),transparent_58%)]"></div>
        <div aria-hidden="true" class="kp-grain pointer-events-none absolute inset-0 opacity-[0.08]"></div>
        <div class="relative mx-auto w-full max-w-[1600px] px-5 md:px-10">
          <div class="grid w-full grid-cols-1 items-stretch gap-6 lg:grid-cols-5 lg:gap-7">
            <div data-kpm-reveal class="lg:col-span-4 {{ $flipped ? 'lg:order-2' : 'lg:order-1' }}">
              <div class="grid h-full gap-4">{!! $flipped ? $bottom.$top : $top.$bottom !!}</div>
            </div>
            <aside class="lg:col-span-1 {{ $flipped ? 'lg:order-1' : 'lg:order-2' }}">
              <div class="relative lg:sticky lg:top-28" data-kpm-reveal style="--kpm-d:120ms">
                <div class="relative flex flex-col justify-center overflow-hidden rounded-2xl border border-white/10 bg-kp-elevated/60 p-6 shadow-[0_28px_90px_-38px_rgba(0,0,0,0.95)] ring-1 ring-white/5 backdrop-blur">
                  <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(700px_300px_at_30%_18%,rgba(201,169,98,0.16),transparent_60%),linear-gradient(to_bottom,rgba(255,255,255,0.06),transparent_55%)]"></div>
                  <div aria-hidden="true" class="kp-grain pointer-events-none absolute inset-0 opacity-[0.14]"></div>
                  <div class="relative">
                    <div class="flex items-center justify-between gap-3">
                      <span class="inline-flex items-center rounded-full border border-white/[0.14] bg-black/25 px-3 py-1 font-sans text-[10px] font-semibold uppercase tracking-[0.28em] text-white/85 backdrop-blur">{{ $kpTr('Modèle', 'Model') }} {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                      <span aria-hidden="true" class="h-px flex-1 bg-gradient-to-r from-transparent via-kp-gold/60 to-transparent"></span>
                    </div>
                    <h3 class="mt-4 font-serif text-3xl font-medium leading-none tracking-[-0.02em] text-kp-accent">{{ $item->name }}</h3>
                    <p class="kp-clamp-3 mt-4 text-sm leading-relaxed text-kp-muted">{{ $item->tr('description') }}</p>
                    @php($chips = array_slice($item->characteristics ?? [], 0, 4))
                    @if($chips)
                    <div class="mt-5 flex flex-wrap gap-2">
                      @foreach($chips as $c)<span class="inline-flex items-center rounded-full border border-white/[0.12] bg-white/5 px-3 py-1 font-sans text-[10px] font-semibold uppercase tracking-[0.18em] text-kp-accent/90 backdrop-blur">{{ $kpEn ? (($c['en'] ?? '') ?: ($c['fr'] ?? '')) : ($c['fr'] ?? '') }}</span>@endforeach
                    </div>
                    @endif
                    <a href="{{ $item->link() }}" class="group relative mt-6 inline-flex w-full items-center justify-center overflow-hidden rounded-full border border-kp-gold/45 bg-white/95 px-6 py-3 font-sans text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-950 shadow-[0_10px_26px_-14px_rgba(0,0,0,0.65)] transition duration-300 ease-out hover:scale-[1.02] hover:border-kp-gold/70 hover:bg-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/60 focus-visible:ring-offset-2 focus-visible:ring-offset-kp-bg active:scale-[0.98]">
                      <span aria-hidden="true" class="pointer-events-none absolute inset-y-0 left-0 w-[40%] -translate-x-[130%] bg-gradient-to-r from-white/0 via-white/40 to-white/0 opacity-0 transition duration-700 ease-out group-hover:translate-x-[260%] group-hover:opacity-100"></span>
                      <span class="relative">{{ $kpTr('Découvrir', 'Discover') }}</span>
                    </a>
                  </div>
                </div>
              </div>
            </aside>
          </div>
        </div>
      </section>
    @endforeach
  </div>
</section>
<script>
(() => {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  // LazyAutoplayVideo : charge et lance la vidéo à l'approche du viewport (rootMargin 160px)
  document.querySelectorAll('#modeles video[data-src]').forEach(v => {
    const go = () => { v.src = v.dataset.src; v.preload = 'metadata'; if (!reduce) { v.autoplay = true; v.play().catch(() => {}) } };
    if (!('IntersectionObserver' in window)) return go();
    const io = new IntersectionObserver(([e]) => { if (e && e.isIntersecting) { go(); io.disconnect() } }, { rootMargin: '160px 0px' });
    io.observe(v);
  });
  // Reveal (components/kp/Reveal.tsx) : fondu + montée 40px + flou → net, une seule fois
  const els = document.querySelectorAll('#modeles [data-kpm-reveal]');
  if (reduce || !('IntersectionObserver' in window)) return;
  els.forEach(el => el.classList.add('kpm-wait'));
  const io = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('kpm-in'); io.unobserve(e.target) } }), { threshold: 0.14 });
  els.forEach(el => io.observe(el));
})();
</script>
@endif
