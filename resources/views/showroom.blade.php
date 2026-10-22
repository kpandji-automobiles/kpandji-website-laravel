{{-- Showroom virtuel — port de app/showroom/page.tsx + components/kp/VirtualShowroom.tsx + ShowroomSkeleton.tsx (kpandji2026 final).
     Modèles, galeries, points forts : Admin › Modèles (accueil) ; textes : Admin › Contenu › Showroom --}}
@extends('layouts.app')
@php($en = app()->getLocale() === 'en')
@php($tr = fn ($fr, $enT) => $en ? $enT : $fr)
@php($c2 = fn ($k) => $en ? (content($k.'_en') ?: content($k)) : content($k))
@php($pick = fn ($m, $f) => $en ? ($m->{$f.'_en'} ?: $m->{$f.'_fr'}) : $m->{$f.'_fr'})
@php($srModels = $models->map(fn ($m) => [
  'id' => $m->key, 'dbid' => $m->id, 'name' => $m->name,
  'badge' => $pick($m, 'badge'), 'tagline' => $pick($m, 'tagline'), 'description' => $pick($m, 'sr_description'),
  'brochure' => $m->brochure ? cms_asset($m->brochure) : null, 'brochureName' => $m->brochure ? basename($m->brochure) : 'fiche-technique.pdf',
  'highlights' => collect($m->highlights ?? [])->map(fn ($h) => $en ? (($h['en'] ?? '') ?: ($h['fr'] ?? '')) : ($h['fr'] ?? ''))->filter()->values(),
  'gallery' => collect($m->gallery ?? [])->filter(fn ($g) => filled($g['src'] ?? null))->map(fn ($g) => ['src' => cms_asset($g['src']), 'alt' => $g['alt'] ?? '', 'tag' => $g['tag'] ?? ''])->values(),
])->values())
@php($savUrl = cms_url(content('showroomv.sav_url')))
@section('seo_title', content('showroomv.seo_title'))
@section('meta_description', content('showroomv.seo_description'))
@push('meta')
<link rel="canonical" href="{{ url('/showroom') }}">
<meta property="og:title" content="{{ content('showroomv.seo_title') }}">
<meta property="og:description" content="{{ content('showroomv.og_description') }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="{{ $en ? 'en_US' : 'fr_FR' }}">
<meta property="og:url" content="{{ url('/showroom') }}">
@if($srModels->first() && count($srModels->first()['gallery']))<link rel="preload" as="image" href="{{ $srModels->first()['gallery'][0]['src'] }}" fetchpriority="high">@endif
@endpush
@php($btn = 'inline-flex min-h-11 w-full items-center justify-center rounded-full border border-kp-gold/45 bg-white/95 px-6 py-3 font-sans text-[11px] font-semibold uppercase tracking-[0.18em] text-zinc-950 shadow-[0_10px_26px_-14px_rgba(0,0,0,0.65)] transition duration-300 ease-out hover:scale-[1.02] hover:border-kp-gold/70 hover:bg-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/60 focus-visible:ring-offset-2 focus-visible:ring-offset-kp-bg active:scale-[0.98] sm:w-auto sm:tracking-[0.2em]')
@section('content')
<style>
@keyframes kp-sr-fade-up{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:none}}
.kp-sr-up{opacity:0;animation:kp-sr-fade-up .9s cubic-bezier(.22,1,.36,1) forwards}
.kp-sr-d1{animation-delay:.1s}.kp-sr-d2{animation-delay:.2s}.kp-sr-d3{animation-delay:.3s}
.kp-sr-panel-enter{animation:kp-sr-panel .45s cubic-bezier(.22,1,.36,1)}
@keyframes kp-sr-panel{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.kp-sr-underline{transition:left .45s cubic-bezier(.22,1,.36,1),width .45s cubic-bezier(.22,1,.36,1)}
@media (prefers-reduced-motion:reduce){.kp-sr-up,.kp-sr-panel-enter{animation:none!important;opacity:1!important;transform:none!important}.kp-sr-underline,.kp-sr-img{transition:none!important}}
</style>
<div class="min-h-screen overflow-x-hidden bg-kp-bg text-white">
  <div class="relative w-full overflow-hidden">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(1100px_700px_at_14%_8%,rgba(201,169,98,0.10),transparent_60%),radial-gradient(900px_600px_at_86%_58%,rgba(255,255,255,0.05),transparent_62%),linear-gradient(to_bottom,rgba(0,0,0,0.10),transparent_30%,rgba(0,0,0,0.35))]"></div>

    {{-- ShowroomSkeleton : visible jusqu'à l'initialisation (et sans JavaScript) --}}
    <section data-sr-skeleton aria-busy="true" aria-label="{{ $c2('showroomv.loading') }}" class="relative overflow-hidden pb-6 pt-[96px] sm:pt-[110px] md:pb-10 md:pt-[132px]">
      <div class="relative mx-auto w-full max-w-[1440px] px-4 sm:px-6 lg:px-10">
        <header class="mx-auto max-w-3xl text-center">
          <span class="mx-auto block h-px w-24 bg-gradient-to-r from-transparent via-kp-gold/90 to-transparent sm:w-40 md:w-52" aria-hidden="true"></span>
          <p class="mt-5 font-sans text-[10px] font-semibold uppercase tracking-[0.32em] text-kp-muted sm:mt-6 sm:text-[11px] sm:tracking-[0.38em]">{{ $c2('showroomv.sk_kicker') }}</p>
          <h1 class="mt-4 font-serif text-[clamp(1.85rem,6.5vw,3.4rem)] font-medium leading-[1.08] tracking-[-0.02em] text-kp-accent sm:mt-5">{{ $c2('showroomv.title') }}</h1>
          <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-white/50 sm:text-[15px]">{{ $c2('showroomv.sk_intro') }}</p>
        </header>
        <div class="mt-8 h-24 animate-pulse rounded-2xl border border-white/10 bg-white/5 sm:mt-10"></div>
        <div class="mt-6 grid gap-6 sm:mt-8 lg:grid-cols-12 lg:gap-8">
          <div class="order-2 lg:order-1 lg:col-span-8">
            <div class="relative aspect-[4/3] w-full overflow-hidden rounded-2xl border border-white/10 bg-black/30 sm:aspect-video sm:rounded-3xl">
              <img src="{{ cms_asset(content('showroomv.skeleton_image')) }}" alt="" fetchpriority="high" class="absolute inset-0 h-full w-full object-cover object-center opacity-90">
              <div class="absolute inset-0 animate-pulse bg-black/25" aria-hidden="true"></div>
            </div>
            <div class="mt-4 flex gap-2.5 overflow-hidden">@for($i = 0; $i < 4; $i++)<div class="h-[72px] w-[108px] shrink-0 animate-pulse rounded-xl bg-white/[0.08] sm:h-[80px] sm:w-[128px]"></div>@endfor</div>
          </div>
          <div class="order-1 lg:order-2 lg:col-span-4"><div class="h-[320px] animate-pulse rounded-2xl border border-white/10 bg-white/5 sm:h-[360px]"></div></div>
        </div>
      </div>
    </section>

    {{-- VirtualShowroom --}}
    <section x-data="kpShowroom(@js($srModels))" x-init="init()" x-cloak x-ref="root" aria-labelledby="kp-sr-title" class="relative overflow-hidden pb-6 pt-[96px] sm:pt-[110px] md:pb-10 md:pt-[132px]">
      <div aria-hidden="true" class="fixed left-0 right-0 top-0 z-[90] h-px origin-left bg-gradient-to-r from-kp-gold/0 via-kp-gold/65 to-kp-gold/0 opacity-60" :style="`transform: scaleX(${progress})`"></div>
      <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(850px_420px_at_22%_14%,rgba(201,169,98,0.14),transparent_60%),radial-gradient(900px_520px_at_80%_70%,rgba(255,255,255,0.05),transparent_60%)]"></div>
      <div aria-hidden="true" class="kp-grain pointer-events-none absolute inset-0 opacity-[0.10]"></div>
      <div aria-hidden="true" class="pointer-events-none absolute -left-24 top-28 h-[460px] w-[460px] rounded-full bg-kp-gold/[0.08] blur-3xl"></div>
      <div aria-hidden="true" class="pointer-events-none absolute -right-28 top-[420px] h-[520px] w-[520px] rounded-full bg-white/[0.06] blur-3xl"></div>

      <div class="relative mx-auto w-full max-w-[1440px] px-4 sm:px-6 lg:px-10">
        {!! cms_edit(route('admin.showroom').'#textes', 'Modifier les textes de la page', '0') !!}
        <header class="kp-sr-up mx-auto max-w-3xl text-center">
          <span class="mx-auto block h-px w-24 bg-gradient-to-r from-transparent via-kp-gold/90 to-transparent sm:w-40 md:w-52" aria-hidden="true"></span>
          <h1 id="kp-sr-title" class="mt-4 font-serif text-[clamp(1.85rem,6.5vw,3.4rem)] font-medium leading-[1.08] tracking-[-0.02em] text-kp-accent sm:mt-5">{{ $c2('showroomv.title') }}</h1>
        </header>

        <div class="kp-sr-up kp-sr-d1 relative mt-8 sm:mt-10">
          {!! cms_edit(route('admin.showroom').'#onglets', 'Modifier les onglets (ordre, affichage)', '-2.6rem') !!}
          <div class="sticky top-[72px] z-40 overflow-hidden rounded-2xl border border-white/10 bg-black/60 shadow-[0_28px_90px_-38px_rgba(0,0,0,0.95)] ring-1 ring-white/[0.06] backdrop-blur-2xl sm:top-[84px]">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(700px_220px_at_50%_0%,rgba(201,169,98,0.12),transparent_55%)]"></div>
            <div class="relative p-3 sm:p-4 md:p-5">
              <p id="kp-sr-tabs" class="px-1 font-sans text-[10px] font-semibold uppercase tracking-[0.24em] text-white/45 sm:tracking-[0.28em]">{{ $c2('showroomv.lbl_model') }}</p>
              <div role="tablist" aria-labelledby="kp-sr-tabs" class="relative mt-3 grid grid-cols-1 gap-2 sm:gap-1.5 {{ ['sm:grid-cols-1','sm:grid-cols-1','sm:grid-cols-2','sm:grid-cols-3','sm:grid-cols-4','sm:grid-cols-5','sm:grid-cols-6'][min(max($srModels->count(), 1), 6)] }}">
                <template x-for="(m, mi) in models" :key="m.id">
                  <button type="button" role="tab" :id="'tab-' + m.id" :aria-selected="m.id === selectedId" :aria-controls="'panel-' + m.id" :tabindex="m.id === selectedId ? 0 : -1"
                    @click="selectModel(m.id)" @keydown.arrow-right.prevent="cycle(mi, 1)" @keydown.arrow-left.prevent="cycle(mi, -1)"
                    class="relative rounded-xl px-4 py-3.5 text-left transition sm:rounded-t-xl sm:py-4"
                    :class="m.id === selectedId ? 'bg-white/10 text-kp-accent ring-1 ring-kp-gold/25' : 'text-white/55 hover:bg-white/[0.06] hover:text-white/85'">
                    <span class="flex flex-wrap items-center gap-2">
                      <span class="font-serif text-base font-medium tracking-[-0.02em] sm:text-lg" x-text="m.name"></span>
                      <template x-if="m.badge"><span class="rounded-full border border-kp-gold/35 bg-kp-gold/10 px-2 py-0.5 text-[9px] font-semibold uppercase tracking-[0.16em] text-kp-gold" x-text="m.badge"></span></template>
                    </span>
                    <span class="mt-1 block text-xs text-white/45 sm:text-[13px]" x-text="m.tagline"></span>
                    <span x-show="m.id === selectedId" aria-hidden="true" class="absolute inset-x-3 bottom-0 hidden h-0.5 rounded-full bg-kp-gold/85 sm:block"></span>
                  </button>
                </template>
              </div>
            </div>
          </div>
        </div>

        @if(cms_is_admin())<div class="relative h-0"><a :href="'{{ route('admin.showroom') }}#modele-' + selected.dbid" target="_blank" rel="noopener" class="kp-cms-edit" style="top:.9rem" data-kp-admin-edit><svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg><span x-text="'Modifier ' + selected.name + ' (galerie, points forts)'">Modifier ce modèle</span></a></div>@endif
        <div class="kp-sr-up kp-sr-d2 mt-6 sm:mt-8" role="tabpanel" :id="'panel-' + selectedId" :aria-labelledby="'tab-' + selectedId">
          <template x-for="key in [selectedId]" :key="key">
          <div class="kp-sr-panel-enter grid gap-6 lg:grid-cols-12 lg:gap-8">
            {{-- Points forts --}}
            <aside class="order-1 min-w-0 lg:order-2 lg:col-span-4">
              <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-kp-elevated/55 p-5 shadow-[0_28px_90px_-38px_rgba(0,0,0,0.95)] ring-1 ring-white/5 backdrop-blur sm:p-6">
                <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(700px_280px_at_20%_12%,rgba(201,169,98,0.16),transparent_60%),linear-gradient(to_bottom,rgba(255,255,255,0.06),transparent_55%)]"></div>
                <div aria-hidden="true" class="kp-grain pointer-events-none absolute inset-0 opacity-[0.12]"></div>
                <div class="relative">
                  <p class="font-sans text-[10px] font-semibold uppercase tracking-[0.28em] text-white/45">{{ $c2('showroomv.lbl_highlights') }}</p>
                  <h2 class="mt-2 font-serif text-2xl font-medium tracking-[-0.02em] text-kp-accent sm:text-[1.65rem]" x-text="selected.name"></h2>
                  <p class="mt-2 text-sm leading-relaxed text-white/55" x-text="selected.description"></p>
                  <ul class="mt-5 space-y-3 border-t border-white/[0.08] pt-5 text-[13px] text-white/60 sm:text-sm">
                    <template x-for="h in selected.highlights" :key="h"><li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-kp-gold/70 shadow-[0_0_12px_rgba(201,169,98,0.35)]" aria-hidden="true"></span><span x-text="h"></span></li></template>
                  </ul>
                  <div class="mt-6 grid gap-2.5 sm:grid-cols-2 lg:grid-cols-1">
                    <a x-show="selected.brochure" :href="selected.brochure" :download="selected.brochureName" class="{{ $btn }}">{{ $c2('showroomv.btn_sheet') }}</a>
                    <a href="{{ $savUrl }}" class="{{ $btn }}">{{ $c2('showroomv.btn_appointment') }}</a>
                    <a href="{{ cms_url(content('showroomv.home_url')) }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-full border border-white/[0.12] bg-white/[0.04] px-6 py-3 font-sans text-[11px] font-semibold uppercase tracking-[0.18em] text-white/75 transition hover:border-white/[0.22] hover:bg-white/[0.06] hover:text-white sm:col-span-2 lg:col-span-1">{{ $c2('showroomv.btn_home') }}</a>
                  </div>
                </div>
              </div>
            </aside>

            {{-- Galerie + visuel principal --}}
            <div class="order-2 flex min-w-0 flex-col gap-4 sm:gap-5 lg:order-1 lg:col-span-8">
              <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="font-sans text-[10px] font-semibold uppercase tracking-[0.28em] text-white/40">{{ $c2('showroomv.lbl_visuals') }} — <span x-text="selected.name"></span></p>
                <div class="kp-hide-scrollbar flex gap-2 overflow-x-auto pb-0.5">
                  <template x-for="t in ['all','ext','int']" :key="t">
                    <button type="button" @click="setTag(t)" x-text="tagLabel(t)"
                      class="min-h-11 shrink-0 rounded-full border px-4 py-2.5 font-sans text-[11px] font-semibold uppercase tracking-[0.16em] transition sm:tracking-[0.18em]"
                      :class="tag === t ? 'border-kp-gold/55 bg-kp-gold/15 text-kp-accent shadow-[0_0_0_1px_rgba(201,169,98,0.14)]' : 'border-white/[0.12] bg-white/[0.04] text-white/65 hover:border-white/[0.22] hover:bg-white/[0.06] hover:text-white/80'"></button>
                  </template>
                </div>
              </div>

              <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-black/30 shadow-[0_30px_90px_-45px_rgba(0,0,0,0.95)] ring-1 ring-white/[0.06] sm:rounded-3xl">
                <div aria-hidden="true" class="pointer-events-none absolute inset-0 z-10 bg-gradient-to-b from-white/5 via-transparent to-black/40"></div>
                <div class="relative aspect-[4/3] w-full overflow-hidden sm:aspect-video">
                  <template x-for="(g, gi) in filtered" :key="g.src + gi">
                    <img :src="gi === index || loaded.includes(g.src) ? g.src : null" :alt="g.alt" @load="loaded.includes(g.src) || loaded.push(g.src)"
                      class="kp-sr-img absolute inset-0 h-full w-full object-cover object-center transition-opacity duration-[400ms] ease-[cubic-bezier(.22,1,.36,1)]"
                      :class="gi === index ? 'opacity-100' : 'opacity-0'" :aria-hidden="gi !== index">
                  </template>
                  <div x-show="!active" class="absolute inset-0 grid place-items-center"><p class="text-sm text-white/45">{{ $c2('showroomv.no_visual') }}</p></div>
                  <span x-show="active && active.tag" x-text="active ? tagLabel(active.tag) : ''" class="absolute left-4 top-4 z-20 rounded-full border border-white/[0.12] bg-black/55 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/80 backdrop-blur-md"></span>
                  <div x-show="filtered.length > 1" class="absolute inset-x-0 top-1/2 z-20 flex -translate-y-1/2 justify-between px-3 sm:px-4">
                    <button type="button" aria-label="{{ $c2('showroomv.a_prev') }}" @click="goTo(-1)" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/[0.14] bg-black/45 text-white/85 backdrop-blur-md transition hover:border-white/[0.28] hover:bg-black/60"><svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M15 6l-6 6 6 6"/></svg></button>
                    <button type="button" aria-label="{{ $c2('showroomv.a_next') }}" @click="goTo(1)" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/[0.14] bg-black/45 text-white/85 backdrop-blur-md transition hover:border-white/[0.28] hover:bg-black/60"><svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M9 6l6 6-6 6"/></svg></button>
                  </div>
                  <div x-show="filtered.length > 1" class="absolute bottom-4 left-1/2 z-20 flex -translate-x-1/2 items-center gap-2 rounded-full border border-white/10 bg-black/50 px-3 py-2 backdrop-blur-md">
                    <span class="font-sans text-[10px] font-semibold tabular-nums tracking-widest text-white/70" x-text="(index + 1) + ' / ' + filtered.length"></span>
                    <div class="flex gap-1.5" role="tablist" aria-label="{{ $c2('showroomv.a_thumbs') }}">
                      <template x-for="(g, gi) in filtered" :key="'d' + gi">
                        <button type="button" :aria-label="@js($c2('showroomv.a_image').' ') + (gi + 1)" :aria-current="gi === index" @click="show(gi)" class="h-1.5 w-1.5 shrink-0 rounded-full transition-colors" :class="gi === index ? 'bg-kp-gold' : 'bg-white/35 hover:bg-white/55'"></button>
                      </template>
                    </div>
                  </div>
                </div>
                <div class="border-t border-white/10 bg-black/40 px-4 py-4 sm:px-5 sm:py-4">
                  <p class="font-serif text-lg leading-snug text-kp-accent sm:text-xl" x-text="selected.tagline"></p>
                  <p class="kp-clamp-2 mt-1 min-h-10 text-sm leading-snug text-white/55 sm:min-h-11 sm:text-[15px]" aria-live="polite" x-text="active ? active.alt : '\u00a0'"></p>
                </div>
              </div>

              <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-3 ring-1 ring-white/5 backdrop-blur sm:p-4">
                <p class="px-1 font-sans text-[10px] font-semibold uppercase tracking-[0.28em] text-white/45">{{ $c2('showroomv.lbl_gallery') }}</p>
                <div x-ref="thumbs" @mouseenter="hovering = true" @mouseleave="hovering = false" class="kp-hide-scrollbar mt-3 flex snap-x snap-mandatory gap-2.5 overflow-x-auto overscroll-x-contain pb-1 sm:gap-3">
                  <template x-for="(g, gi) in filtered" :key="'t' + g.src + gi">
                    <button type="button" :data-thumb="gi" @click="show(gi)" :aria-label="@js($c2('showroomv.a_view').' ') + (gi + 1)" :aria-current="gi === index"
                      class="relative h-[72px] w-[108px] shrink-0 snap-center overflow-hidden rounded-xl border-2 transition sm:h-[80px] sm:w-[128px] md:h-[88px] md:w-[148px]"
                      :class="gi === index ? 'border-kp-gold/55' : 'border-transparent opacity-80 hover:border-white/[0.22] hover:opacity-100'">
                      <img :src="g.src" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover object-center">
                      <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-black/0 to-black/50"></div>
                      <span x-show="g.tag" x-text="tagLabel(g.tag)" class="absolute bottom-1.5 left-1.5 rounded-full border border-white/[0.12] bg-black/55 px-1.5 py-0.5 text-[8px] font-semibold uppercase tracking-[0.14em] text-white/70 sm:text-[9px]"></span>
                    </button>
                  </template>
                </div>
              </div>
            </div>
          </div>
          </template>
        </div>
      </div>

      <div class="relative mt-12 border-t border-white/[0.06] bg-kp-bg/70 py-12 sm:mt-14 sm:py-14">
        {!! cms_edit(route('admin.showroom').'#textes', 'Modifier cette section', '1rem') !!}
        <div class="mx-auto w-full max-w-[1440px] px-4 sm:px-6 lg:px-10">
          <div class="kp-sr-up kp-sr-d3 grid gap-8 lg:grid-cols-12 lg:items-center lg:gap-10">
            <div class="lg:col-span-7">
              <p class="font-sans text-[10px] font-semibold uppercase tracking-[0.32em] text-kp-muted sm:tracking-[0.38em]">{{ $c2('showroomv.help_kicker') }}</p>
              <h3 class="mt-3 font-serif text-[clamp(1.6rem,4.5vw,2rem)] font-medium tracking-[-0.02em] text-kp-accent">{{ $c2('showroomv.help_title') }}</h3>
              <p class="mt-3 max-w-xl text-sm leading-relaxed text-white/[0.52] sm:text-[15px]">{{ $c2('showroomv.help_text') }}</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row lg:col-span-5 lg:justify-end">
              <a href="{{ $savUrl }}" class="{{ $btn }}">{{ $c2('showroomv.btn_sav') }}</a>
              <a x-show="selected.brochure" :href="selected.brochure" :download="selected.brochureName" class="{{ $btn }}">{{ $c2('showroomv.btn_download') }}</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>
<script>
function kpShowroom(models) {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const labels = { all: @js($c2('showroomv.f_all')), ext: @js($c2('showroomv.f_ext')), int: @js($c2('showroomv.f_int')) };
  return {
    models, selectedId: models[0] ? models[0].id : null, tag: 'all', index: 0, hovering: false, progress: 0, loaded: [], timer: null,
    get selected() { return this.models.find(m => m.id === this.selectedId) || this.models[0] || { name: '', highlights: [], gallery: [] } },
    get filtered() { const base = this.selected.gallery || []; const list = this.tag === 'all' ? base : base.filter(g => g.tag === this.tag); return list.length ? list : base },
    get active() { return this.filtered[Math.min(this.index, Math.max(this.filtered.length - 1, 0))] },
    tagLabel(k) { return labels[k] || labels.all },
    init() {
      document.querySelectorAll('[data-sr-skeleton]').forEach(el => el.remove());
      const onScroll = () => { const r = this.$refs.root.getBoundingClientRect(); const h = r.height || 1; this.progress = Math.min(1, Math.max(0, -r.top / h)) };
      addEventListener('scroll', onScroll, { passive: true }); onScroll();
      if (!reduce) this.timer = setInterval(() => { if (!this.hovering && this.tag === 'all' && this.filtered.length > 1 && !document.hidden) this.index = (this.index + 1) % this.filtered.length }, 6500);
    },
    selectModel(id) { this.selectedId = id; this.index = 0; this.tag = 'all' },
    cycle(i, d) { const n = this.models.length; const next = this.models[(i + d + n) % n]; this.selectModel(next.id); setTimeout(() => document.getElementById('tab-' + next.id)?.focus(), 0) },
    setTag(t) { this.tag = t; this.index = Math.min(this.index, Math.max(this.filtered.length - 1, 0)) },
    goTo(d) { const n = this.filtered.length; if (n) this.index = (this.index + d + n) % n },
    show(i) { this.index = i; this.$nextTick(() => { const c = this.$refs.thumbs; const t = c && c.querySelector(`[data-thumb="${i}"]`); if (c && t) c.scrollTo({ left: Math.max(0, t.offsetLeft - (c.clientWidth - t.offsetWidth) / 2), behavior: reduce ? 'auto' : 'smooth' }) }) },
  };
}
</script>
@endsection
