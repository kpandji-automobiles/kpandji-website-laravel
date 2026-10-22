@extends('admin.layout')
@section('title', $modele->exists ? 'Modèle : '.$modele->name : 'Nouveau modèle')
@section('actions')<a href="{{ route('admin.modeles.index') }}" class="text-xs text-white/60 hover:text-white">← Tous les modèles</a>@endsection
@section('content')
@php
  $in = 'mt-2 w-full rounded-lg border border-white/15 bg-black px-4 py-3 text-sm text-white outline-none focus:border-kp-gold';
  $lb = 'block text-xs uppercase tracking-[0.2em] text-white/60';
  $chars = old('char_fr') ? collect(old('char_fr'))->map(fn ($fr, $i) => ['fr' => $fr, 'en' => old('char_en')[$i] ?? ''])->values()->all() : ($modele->characteristics ?? []);
  while (count($chars) < 4) $chars[] = ['fr' => '', 'en' => ''];
@endphp
<form method="POST" enctype="multipart/form-data" action="{{ $modele->exists ? route('admin.modeles.update', $modele) : route('admin.modeles.store') }}" class="grid gap-8 xl:grid-cols-3">
  @csrf @if($modele->exists) @method('PUT') @endif
  <div class="space-y-6 xl:col-span-2">
    <section class="space-y-5 rounded-2xl border border-white/10 bg-white/[0.02] p-6">
      <div class="grid gap-5 sm:grid-cols-2">
        <label class="{{ $lb }}">Nom *<input name="name" value="{{ old('name', $modele->name) }}" required class="{{ $in }}"></label>
        <label class="{{ $lb }}">Page « Découvrir »
          <select name="car_slug" class="{{ $in }}"><option value="">— ShowRoom —</option>
            @foreach($cars as $c)<option value="{{ $c->slug }}" @selected(old('car_slug', $modele->car_slug) === $c->slug)>{{ $c->name }} (/modeles/{{ $c->slug }})</option>@endforeach
          </select></label>
      </div>
      <label class="{{ $lb }}">Description (FR) — 3 lignes affichées<textarea name="description_fr" rows="4" class="{{ $in }}">{{ old('description_fr', $modele->description_fr) }}</textarea></label>
      <label class="{{ $lb }}">Description (EN)<textarea name="description_en" rows="4" class="{{ $in }}">{{ old('description_en', $modele->description_en) }}</textarea></label>
    </section>
    <section class="space-y-4 rounded-2xl border border-white/10 bg-white/[0.02] p-6">
      <h2 class="font-serif text-xl text-white">Caractéristiques <span class="text-sm text-white/40">(4 premières affichées en pastilles)</span></h2>
      @foreach($chars as $i => $c)
      <div class="grid gap-3 sm:grid-cols-2">
        <input name="char_fr[]" value="{{ $c['fr'] ?? '' }}" placeholder="FR" class="{{ $in }} mt-0">
        <input name="char_en[]" value="{{ $c['en'] ?? '' }}" placeholder="EN" class="{{ $in }} mt-0">
      </div>
      @endforeach
      <p class="text-xs text-white/35">Laisser une ligne vide pour la supprimer. Enregistrez pour obtenir de nouvelles lignes vides.</p>
    </section>
    <section class="space-y-5 rounded-2xl border border-white/10 bg-white/[0.02] p-6">
      <h2 class="font-serif text-xl text-white">Médias</h2>
      @foreach(\App\Http\Controllers\Admin\ModeleController::MEDIA as $f => [$label, $type])
      <div class="grid gap-4 sm:grid-cols-[160px_1fr] sm:items-center">
        <div class="flex aspect-[16/10] items-center justify-center overflow-hidden rounded-lg border border-white/10 bg-black">
          @if($modele->$f)@if($type === 'video')<video src="{{ cms_asset($modele->$f) }}" muted preload="metadata" class="h-full w-full object-cover"></video>@else<img src="{{ cms_asset($modele->$f) }}" class="h-full w-full object-cover" alt="">@endif
          @else<span class="text-xs text-white/30">Aucun</span>@endif
        </div>
        <div>
          <label class="{{ $lb }}">{{ $label }} — chemin public<input name="{{ $f }}" value="{{ old($f, $modele->$f) }}" placeholder="models/…" class="{{ $in }}"></label>
          <input type="file" name="{{ $f }}_file" accept="{{ $type === 'video' ? 'video/mp4,video/webm,video/quicktime' : 'image/jpeg,image/png,image/webp' }}" class="mt-2 block w-full text-xs text-white/60 file:mr-3 file:rounded-full file:border-0 file:bg-kp-gold file:px-4 file:py-2 file:text-xs file:font-semibold file:text-black">
        </div>
      </div>
      @endforeach
      <p class="text-xs text-white/35">Un fichier envoyé remplace le chemin. Vider le chemin retire le média.</p>
    </section>
    <section class="rounded-2xl border border-kp-gold/25 bg-kp-gold/[0.04] p-6 text-sm text-white/70">
      <h2 class="font-serif text-xl text-white">Showroom virtuel</h2>
      <p class="mt-2">Le contenu de ce modèle dans le showroom (/showroom) — onglet, badge, accroche, description, fiche PDF, points forts et galerie — se modifie dans
        <a href="{{ route('admin.showroom') }}{{ $modele->exists ? '#modele-'.$modele->id : '' }}" class="text-kp-gold underline">Admin › Showroom</a>.</p>
    </section>
  </div>
  <div class="space-y-6">
    <section class="space-y-4 rounded-2xl border border-white/10 bg-white/[0.02] p-6 text-sm">
      <label class="flex items-center gap-3"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $modele->is_active)) class="h-4 w-4 accent-kp-gold"> Affiché sur l'accueil</label>
      <label class="{{ $lb }}">Ordre<input type="number" name="sort" min="0" value="{{ old('sort', $modele->sort ?? 0) }}" class="{{ $in }}"></label>
      <button class="w-full rounded-full bg-kp-gold py-3 text-xs font-bold uppercase tracking-[0.2em] text-black hover:bg-white">Enregistrer</button>
      <a href="{{ url('/') }}#modeles" target="_blank" class="block text-center text-xs text-kp-gold hover:underline">Voir la section ↗</a>
    </section>
  </div>
</form>
@endsection
