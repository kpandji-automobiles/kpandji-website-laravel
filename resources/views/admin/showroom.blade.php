@extends('admin.layout')
@section('title', 'Showroom')
@section('actions')<a href="{{ route('showroom') }}" target="_blank" class="rounded-full bg-kp-gold px-5 py-2 text-xs font-bold uppercase tracking-[0.18em] text-black hover:bg-white">Voir la page ↗</a>@endsection
@section('content')
@php
  $in = 'mt-2 w-full rounded-lg border border-white/15 bg-black px-4 py-3 text-sm text-white outline-none focus:border-kp-gold';
  $lb = 'block text-xs uppercase tracking-[0.2em] text-white/60';
  $card = 'rounded-2xl border border-white/10 bg-white/[0.02] p-6';
  $sm = 'rounded-lg border border-white/15 px-2.5 py-2 text-xs hover:border-kp-gold';
@endphp
<p class="mb-6 max-w-3xl text-sm text-white/50">Tout le contenu de la page <a href="{{ route('showroom') }}" target="_blank" class="text-kp-gold underline">/showroom</a> : onglets des modèles, contenu de chaque modèle (badge, accroche, description, fiche PDF, points forts, galerie) et textes de la page. Chaque bloc a son bouton « Enregistrer ».</p>
<nav class="mb-8 flex flex-wrap gap-2 text-xs">
  <a href="#onglets" class="rounded-full border border-white/15 px-4 py-2 hover:border-kp-gold">Onglets</a>
  @foreach($modeles->where('in_showroom', true) as $m)<a href="#modele-{{ $m->id }}" class="rounded-full border border-white/15 px-4 py-2 hover:border-kp-gold">{{ $m->name }}</a>@endforeach
  <a href="#textes" class="rounded-full border border-white/15 px-4 py-2 hover:border-kp-gold">Textes, boutons & SEO</a>
</nav>

{{-- 1. Onglets : affichage + ordre --}}
<form id="onglets" method="POST" action="{{ route('admin.showroom.order') }}" class="{{ $card }} mb-8 scroll-mt-6">
  @csrf @method('PUT')
  <h2 class="font-serif text-2xl text-white">Onglets des modèles</h2>
  <p class="mt-1 text-xs text-white/40">Cochez les modèles affichés dans le showroom ; l'ordre (croissant) est celui des onglets. Indépendant de la section « Modèles » de l'accueil. Nouveau modèle : <a href="{{ route('admin.modeles.create') }}" class="text-kp-gold underline">Modèles (accueil) › Ajouter</a> puis complétez-le ici.</p>
  <div class="mt-5 divide-y divide-white/5 rounded-xl border border-white/10">
    @foreach($modeles as $m)
    <div class="flex flex-wrap items-center gap-4 px-4 py-3">
      <label class="flex flex-1 items-center gap-3 text-sm"><input type="checkbox" name="in_showroom[{{ $m->id }}]" value="1" @checked($m->in_showroom) class="h-4 w-4 accent-kp-gold">
        @php($thumb = ($m->gallery[0]['src'] ?? null) ?: $m->top_image)
        @if($thumb)<img src="{{ cms_asset($thumb) }}" class="h-9 w-14 rounded object-cover" alt="">@endif
        <span class="font-semibold text-white">{{ $m->name }}</span>@if($m->badge_fr)<span class="rounded-full border border-kp-gold/40 px-2 py-0.5 text-[9px] uppercase tracking-[0.16em] text-kp-gold">{{ $m->badge_fr }}</span>@endif</label>
      <label class="flex items-center gap-2 text-xs text-white/50">Ordre <input type="number" name="sr_sort[{{ $m->id }}]" min="0" value="{{ $m->sr_sort }}" class="w-20 rounded-lg border border-white/15 bg-black px-3 py-2 text-sm text-white"></label>
    </div>
    @endforeach
  </div>
  <button class="mt-5 rounded-full bg-kp-gold px-8 py-3 text-xs font-bold uppercase tracking-[0.2em] text-black hover:bg-white">Enregistrer les onglets</button>
</form>

{{-- 2. Contenu par modèle --}}
@foreach($modeles->where('in_showroom', true) as $m)
@php($uid = fn () => 'g'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(8)))
<form id="modele-{{ $m->id }}" method="POST" enctype="multipart/form-data" action="{{ route('admin.showroom.model', $m) }}" class="{{ $card }} mb-8 scroll-mt-6 space-y-5"
  x-data="{ hl: @js(array_values($m->highlights ?? [])), gal: @js(collect($m->gallery ?? [])->map(fn ($g) => ['uid' => $uid(), 'src' => $g['src'] ?? '', 'alt' => $g['alt'] ?? '', 'tag' => $g['tag'] ?? '', 'preview' => cms_asset($g['src'] ?? '')])->values()),
    newUid(){ return 'g' + Math.random().toString(36).slice(2, 10) }, move(a, i, d){ const j = i + d; if (j < 0 || j >= a.length) return; const r = a.splice(i, 1)[0]; a.splice(j, 0, r) } }">
  @csrf @method('PUT')
  <input type="hidden" name="highlights_json" :value="JSON.stringify(hl)">
  <input type="hidden" name="gallery_json" :value="JSON.stringify(gal.map(g => ({ uid: g.uid, src: g.src, alt: g.alt, tag: g.tag })))">
  <div class="flex flex-wrap items-center justify-between gap-3">
    <h2 class="font-serif text-2xl text-white">{{ $m->name }} <span class="text-sm text-white/40">— onglet {{ $m->sr_sort }}</span></h2>
    <a href="{{ route('admin.modeles.edit', $m) }}" class="text-xs text-white/50 hover:text-white">Nom, lien et section d'accueil ›</a>
  </div>
  <div class="grid gap-5 sm:grid-cols-2">
    <label class="{{ $lb }}">Badge (FR) — vide = aucun<input name="badge_fr" value="{{ $m->badge_fr }}" class="{{ $in }}"></label>
    <label class="{{ $lb }}">Badge (EN)<input name="badge_en" value="{{ $m->badge_en }}" class="{{ $in }}"></label>
    <label class="{{ $lb }}">Accroche (FR) — sous le nom de l'onglet et sous l'image<input name="tagline_fr" value="{{ $m->tagline_fr }}" class="{{ $in }}"></label>
    <label class="{{ $lb }}">Accroche (EN)<input name="tagline_en" value="{{ $m->tagline_en }}" class="{{ $in }}"></label>
    <label class="{{ $lb }}">Description « Points forts » (FR)<textarea name="sr_description_fr" rows="4" class="{{ $in }}">{{ $m->sr_description_fr }}</textarea></label>
    <label class="{{ $lb }}">Description « Points forts » (EN)<textarea name="sr_description_en" rows="4" class="{{ $in }}">{{ $m->sr_description_en }}</textarea></label>
  </div>
  <div class="grid gap-4 rounded-xl border border-white/10 p-4 sm:grid-cols-2 sm:items-end">
    <label class="{{ $lb }}">Fiche technique (PDF) — chemin public @if($m->brochure)<a href="{{ cms_asset($m->brochure) }}" target="_blank" class="ml-2 normal-case tracking-normal text-kp-gold underline">ouvrir ↗</a>@endif
      <input name="brochure" value="{{ $m->brochure }}" placeholder="fiche.pdf — vide = aucun bouton" class="{{ $in }}"></label>
    <label class="{{ $lb }}">…ou téléverser un PDF (50 Mo max)<input type="file" name="brochure_file" accept="application/pdf" class="mt-2 block w-full text-xs text-white/60 file:mr-3 file:rounded-full file:border-0 file:bg-kp-gold file:px-4 file:py-2 file:text-xs file:font-semibold file:text-black"></label>
  </div>

  <div>
    <p class="{{ $lb }}">Points forts (FR / EN)</p>
    <template x-for="(h, i) in hl" :key="i">
      <div class="mt-2 flex flex-wrap items-center gap-2">
        <input x-model="h.fr" placeholder="FR" class="{{ $in }} !mt-0 min-w-[200px] flex-1"><input x-model="h.en" placeholder="EN" class="{{ $in }} !mt-0 min-w-[200px] flex-1">
        <button type="button" @click="move(hl, i, -1)" class="{{ $sm }}">↑</button><button type="button" @click="move(hl, i, 1)" class="{{ $sm }}">↓</button><button type="button" @click="hl.splice(i, 1)" class="{{ $sm }} text-red-400">✕</button>
      </div>
    </template>
    <button type="button" @click="hl.push({ fr: '', en: '' })" class="mt-3 rounded-full border border-dashed border-white/25 px-4 py-2 text-xs text-white/70 hover:border-kp-gold hover:text-white">+ Ajouter un point fort</button>
  </div>

  <div>
    <p class="{{ $lb }}">Galerie (<span x-text="gal.length"></span> images — la 1ʳᵉ s'affiche en premier)</p>
    <div class="mt-3 grid gap-3 xl:grid-cols-2">
      <template x-for="(g, i) in gal" :key="g.uid">
        <div class="flex gap-3 rounded-xl border border-white/10 bg-black/40 p-3">
          <div class="w-36 shrink-0">
            <div class="relative aspect-[4/3] overflow-hidden rounded-lg bg-black"><img x-show="g.preview" :src="g.preview" class="h-full w-full object-cover" alt=""><span class="absolute left-1 top-1 rounded bg-black/70 px-1.5 text-[10px] text-white/70" x-text="i + 1"></span></div>
            <input type="file" :name="'gf[' + g.uid + ']'" accept="image/jpeg,image/png,image/webp" @change="const f = $event.target.files[0]; if (f) { g.preview = URL.createObjectURL(f); g.src = g.src || 'upload' }" class="mt-2 block w-full text-[10px] text-white/50 file:mr-2 file:rounded-full file:border-0 file:bg-kp-gold file:px-2 file:py-1 file:text-[10px] file:font-semibold file:text-black">
          </div>
          <div class="min-w-0 flex-1 space-y-2">
            <input x-model="g.src" @change="g.preview = g.src && g.src !== 'upload' ? ('{{ url('/') }}/' + g.src.replace(/^\//, '')) : g.preview" placeholder="Chemin (models/showcase/…) ou téléverser" class="{{ $in }} !mt-0 !py-2">
            <input x-model="g.alt" placeholder="Légende / texte alternatif" class="{{ $in }} !mt-0 !py-2">
            <div class="flex flex-wrap items-center gap-2">
              <select x-model="g.tag" class="{{ $in }} !mt-0 !w-auto !py-2"><option value="ext">Extérieur</option><option value="int">Intérieur</option><option value="">—</option></select>
              <button type="button" @click="move(gal, i, -1)" class="{{ $sm }}" title="Monter">↑</button><button type="button" @click="move(gal, i, 1)" class="{{ $sm }}" title="Descendre">↓</button><button type="button" @click="gal.splice(i, 1)" class="{{ $sm }} text-red-400" title="Retirer">✕</button>
            </div>
          </div>
        </div>
      </template>
    </div>
    <button type="button" @click="gal.push({ uid: newUid(), src: '', alt: @js($m->name), tag: 'ext', preview: '' })" class="mt-3 rounded-full border border-dashed border-white/25 px-4 py-2 text-xs text-white/70 hover:border-kp-gold hover:text-white">+ Ajouter une image</button>
  </div>
  <div class="flex items-center gap-4 border-t border-white/10 pt-5">
    <button class="rounded-full bg-kp-gold px-8 py-3 text-xs font-bold uppercase tracking-[0.2em] text-black hover:bg-white">Enregistrer {{ $m->name }}</button>
    <a href="{{ route('showroom') }}" target="_blank" class="text-xs text-kp-gold hover:underline">Voir la page ↗</a>
  </div>
</form>
@endforeach

{{-- 3. Textes de la page --}}
<form id="textes" method="POST" enctype="multipart/form-data" action="{{ route('admin.showroom.texts') }}" class="scroll-mt-6 space-y-5">
  @csrf @method('PUT')
  <h2 class="font-serif text-2xl text-white">Textes, boutons, SEO et image de chargement</h2>
  <p class="text-xs text-white/40">Cochez « Rétablir l'original » pour revenir au texte d'origine. Mêmes réglages que Contenu › Showroom.</p>
  @include('admin.partials.cms-fields')
  <div class="sticky bottom-0 -mx-5 flex justify-end border-t border-white/10 bg-kp-black/95 px-5 py-4 backdrop-blur lg:-mx-10 lg:px-10">
    <button class="rounded-full bg-kp-gold px-8 py-3 text-xs font-bold uppercase tracking-[0.2em] text-black hover:bg-white">Enregistrer les textes</button>
  </div>
</form>
@endsection
