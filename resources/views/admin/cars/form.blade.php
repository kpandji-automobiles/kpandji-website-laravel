@extends('admin.layout')
@section('title', $car->exists ? 'Modifier : '.$car->name : 'Nouvelle voiture')
@section('actions')<a href="{{ route('admin.cars.index') }}" class="text-xs text-white/60 hover:text-white">← Retour à la liste</a>@endsection
@section('content')
@php
  $in = 'mt-2 w-full rounded-lg border border-white/15 bg-black px-4 py-3 text-white outline-none focus:border-kp-gold';
  $lb = 'block text-xs uppercase tracking-[0.2em] text-white/60';
  $specsText = old('specs_text', collect($car->specs ?? [])->map(fn ($rows, $g) => "## $g\n" . collect($rows)->map(fn ($v, $k) => "$k : $v")->implode("\n"))->implode("\n\n"));
  $versions = old('versions', $car->versions ?? []);
@endphp
<form method="POST" enctype="multipart/form-data" action="{{ $car->exists ? route('admin.cars.update', $car) : route('admin.cars.store') }}" class="grid gap-8 xl:grid-cols-3"
      x-data="{ name: @js(old('name', $car->name)), slug: @js(old('slug', $car->slug)), slugTouched: {{ $car->exists ? 'true' : 'false' }}, preview: @js($car->image ? asset($car->image) : null),
                versions: @js(array_values($versions)),
                slugify(s){ return (s||'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'') } }">
  @csrf @if($car->exists) @method('PUT') @endif
  <div class="space-y-6 xl:col-span-2">
    <section class="space-y-5 rounded-2xl border border-white/10 bg-white/[0.02] p-6">
      <h2 class="font-serif text-xl text-white">Informations</h2>
      <div class="grid gap-5 sm:grid-cols-2">
        <label class="{{ $lb }}">Nom du modèle *<input name="name" x-model="name" @input="if(!slugTouched) slug = slugify(name)" required class="{{ $in }}"></label>
        <label class="{{ $lb }}">Slug (URL)<input name="slug" x-model="slug" @input="slugTouched = true" class="{{ $in }}"><span class="mt-1 block normal-case tracking-normal text-white/35">/modeles/<span x-text="slug || '…'"></span></span></label>
        <label class="{{ $lb }}">Catégorie<input name="category" value="{{ old('category', $car->category) }}" placeholder="SUV, Pick-up 4x4…" class="{{ $in }}"></label>
        <label class="{{ $lb }}">Garantie<input name="warranty" value="{{ old('warranty', $car->warranty) }}" class="{{ $in }}"></label>
      </div>
      <label class="{{ $lb }}">Description<textarea name="description" rows="4" class="{{ $in }}">{{ old('description', $car->description) }}</textarea></label>
      <label class="{{ $lb }}">Points forts (un par ligne, 4 affichés sur les cartes)<textarea name="highlights_text" rows="4" class="{{ $in }}">{{ old('highlights_text', implode("\n", $car->highlights ?? [])) }}</textarea></label>
    </section>

    <section class="space-y-5 rounded-2xl border border-white/10 bg-white/[0.02] p-6">
      <h2 class="font-serif text-xl text-white">Prix <span class="text-sm text-white/40">(FCFA, nombres entiers sans espaces)</span></h2>
      <div class="grid gap-5 sm:grid-cols-2">
        <label class="{{ $lb }}">Prix catalogue (avant promo)<input type="number" min="0" name="price" value="{{ old('price', $car->price) }}" class="{{ $in }}"><span class="mt-1 block normal-case tracking-normal text-white/35">Si rempli avec un prix promo : ancien prix barré + « Économisez ».</span></label>
        <label class="{{ $lb }}">Prix promo TTC<input type="number" min="0" name="promo_price" value="{{ old('promo_price', $car->promo_price) }}" class="{{ $in }}"><span class="mt-1 block normal-case tracking-normal text-white/35">Multi-versions : mettez le prix le plus bas (« à partir de »).</span></label>
        <label class="{{ $lb }}">Pack campagne (entretien inclus)<input type="number" min="0" name="campaign_price" value="{{ old('campaign_price', $car->campaign_price) }}" class="{{ $in }}"></label>
        <label class="{{ $lb }}">Coût entretien<input type="number" min="0" name="maintenance_cost" value="{{ old('maintenance_cost', $car->maintenance_cost) }}" class="{{ $in }}"></label>
      </div>
    </section>

    <section class="space-y-4 rounded-2xl border border-white/10 bg-white/[0.02] p-6">
      <div class="flex items-center justify-between"><h2 class="font-serif text-xl text-white">Versions <span class="text-sm text-white/40">(optionnel)</span></h2>
        <button type="button" @click="versions.push({version:'',motorisation:'',boite:'',transmission:'',promo_price:'',maintenance_cost:'',campaign_price:''})" class="rounded-full border border-kp-gold/60 px-4 py-1.5 text-xs text-kp-gold hover:bg-kp-gold hover:text-black">+ Ajouter une version</button></div>
      <template x-for="(v, i) in versions" :key="i">
        <div class="relative grid gap-3 rounded-xl border border-white/10 bg-black/40 p-4 sm:grid-cols-4">
          <button type="button" @click="versions.splice(i,1)" class="absolute right-3 top-2 text-lg text-white/40 hover:text-red-400" title="Retirer">×</button>
          <input :name="`versions[${i}][version]`" x-model="v.version" placeholder="Version *" class="rounded-lg border border-white/15 bg-black px-3 py-2 text-white sm:col-span-2">
          <input :name="`versions[${i}][motorisation]`" x-model="v.motorisation" placeholder="Motorisation" class="rounded-lg border border-white/15 bg-black px-3 py-2 text-white sm:col-span-2">
          <input :name="`versions[${i}][boite]`" x-model="v.boite" placeholder="Boîte" class="rounded-lg border border-white/15 bg-black px-3 py-2 text-white">
          <input :name="`versions[${i}][transmission]`" x-model="v.transmission" placeholder="Transmission" class="rounded-lg border border-white/15 bg-black px-3 py-2 text-white">
          <input type="number" min="0" :name="`versions[${i}][promo_price]`" x-model="v.promo_price" placeholder="Prix promo" class="rounded-lg border border-white/15 bg-black px-3 py-2 text-white">
          <input type="number" min="0" :name="`versions[${i}][maintenance_cost]`" x-model="v.maintenance_cost" placeholder="Entretien" class="rounded-lg border border-white/15 bg-black px-3 py-2 text-white">
          <input type="number" min="0" :name="`versions[${i}][campaign_price]`" x-model="v.campaign_price" placeholder="Pack campagne" class="rounded-lg border border-white/15 bg-black px-3 py-2 text-white sm:col-span-2">
        </div>
      </template>
      <p x-show="!versions.length" class="text-sm text-white/40">Aucune version — le modèle est présenté avec ses prix ci-dessus.</p>
    </section>

    <section class="space-y-3 rounded-2xl border border-white/10 bg-white/[0.02] p-6">
      <h2 class="font-serif text-xl text-white">Fiche technique</h2>
      <p class="text-xs text-white/40">Une ligne « ## Groupe » pour chaque rubrique, puis « Libellé : valeur » par ligne.</p>
      <textarea name="specs_text" rows="12" class="{{ $in }} font-mono text-xs">{{ $specsText }}</textarea>
    </section>
  </div>

  <div class="space-y-6">
    <section class="space-y-4 rounded-2xl border border-white/10 bg-white/[0.02] p-6">
      <h2 class="font-serif text-xl text-white">Photo</h2>
      <div class="flex h-48 items-center justify-center overflow-hidden rounded-xl bg-[radial-gradient(ellipse_at_center,rgba(201,169,98,0.18),transparent_65%)] bg-black">
        <template x-if="preview"><img :src="preview" class="max-h-full max-w-full object-contain" alt=""></template>
        <span x-show="!preview" class="text-sm text-white/30">Aucune photo</span>
      </div>
      <input type="file" name="image_file" accept="image/png,image/jpeg,image/webp" @change="const f=$event.target.files[0]; if(f) preview = URL.createObjectURL(f)" class="block w-full text-xs text-white/60 file:mr-3 file:rounded-full file:border-0 file:bg-kp-gold file:px-4 file:py-2 file:text-xs file:font-bold file:text-black">
      <p class="text-[11px] text-white/35">PNG détouré conseillé · JPG/PNG/WebP · 8 Mo max.</p>
    </section>
    <section class="space-y-3 rounded-2xl border border-white/10 bg-white/[0.02] p-6">
      <h2 class="font-serif text-xl text-white">Fiche PDF</h2>
      @if($car->brochure)<p class="text-sm"><a href="{{ asset($car->brochure) }}" target="_blank" class="text-kp-gold">Fiche actuelle ↗</a></p><label class="flex items-center gap-2 text-xs text-white/60"><input type="checkbox" name="remove_brochure" value="1" class="accent-red-500"> Retirer la fiche</label>@endif
      <input type="file" name="brochure_file" accept="application/pdf" class="block w-full text-xs text-white/60 file:mr-3 file:rounded-full file:border-0 file:bg-white/10 file:px-4 file:py-2 file:text-xs file:text-white">
    </section>
    <section class="space-y-4 rounded-2xl border border-white/10 bg-white/[0.02] p-6">
      <h2 class="font-serif text-xl text-white">Affichage</h2>
      <label class="flex items-center gap-3 text-sm text-white"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $car->is_active ?? true)) class="h-5 w-5 accent-[#c9a962]"> Visible sur le site</label>
      <label class="{{ $lb }}">Ordre d'affichage<input type="number" min="0" name="sort_order" value="{{ old('sort_order', $car->sort_order) }}" class="{{ $in }}"></label>
    </section>
    <button class="w-full rounded-full bg-kp-gold py-4 text-xs font-bold uppercase tracking-[0.22em] text-black hover:bg-white">{{ $car->exists ? 'Enregistrer les modifications' : 'Créer la voiture' }}</button>
  </div>
</form>
@endsection
