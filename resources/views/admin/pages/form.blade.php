@extends('admin.layout')
@section('title', $page->exists ? 'Page : '.$page->title : 'Nouvelle page')
@section('actions')<a href="{{ route('admin.pages.index') }}" class="text-xs text-white/60 hover:text-white">← Toutes les pages</a>@endsection
@section('content')
@php
  $in = 'mt-2 w-full rounded-lg border border-white/15 bg-black px-4 py-3 text-sm text-white outline-none focus:border-kp-gold';
  $lb = 'block text-xs uppercase tracking-[0.2em] text-white/60';
@endphp
<style>.kp-prose h2{font-size:1.3rem;font-weight:600}.kp-prose h3{font-size:1.1rem;font-weight:600}.kp-prose ul{list-style:disc;padding-left:1.2em}.kp-prose a{color:#c9a962;text-decoration:underline}</style>
<form method="POST" enctype="multipart/form-data" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="grid gap-8 xl:grid-cols-3"
      x-data="{ title: @js(old('title', $page->title)), slug: @js(old('slug', $page->slug)), touched: {{ $page->exists ? 'true' : 'false' }}, preview: @js($page->hero_image ? cms_asset($page->hero_image) : null),
                slugify(s){ return (s||'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'') } }">
  @csrf @if($page->exists) @method('PUT') @endif
  <div class="space-y-6 xl:col-span-2">
    <section class="space-y-5 rounded-2xl border border-white/10 bg-white/[0.02] p-6">
      <div class="grid gap-5 sm:grid-cols-2">
        <label class="{{ $lb }}">Titre *<input name="title" x-model="title" @input="if(!touched) slug = slugify(title)" required class="{{ $in }}"></label>
        <label class="{{ $lb }}">Slug (adresse)
          @if($page->is_system)<input value="{{ $page->slug }}" disabled class="{{ $in }} opacity-50"><span class="mt-1 block normal-case tracking-normal text-white/35">Adresse fixe (page du site)</span>
          @else<input name="slug" x-model="slug" @input="touched = true" class="{{ $in }}"><span class="mt-1 block normal-case tracking-normal text-white/35">{{ url('/') }}/<span x-text="slug || '…'"></span></span>@endif
        </label>
      </div>
      <label class="{{ $lb }}">Surtitre (doré)<input name="kicker" value="{{ old('kicker', $page->kicker) }}" class="{{ $in }}"></label>
      <label class="{{ $lb }}">Introduction<textarea name="intro" rows="3" class="{{ $in }}">{{ old('intro', $page->intro) }}</textarea></label>
      <div><p class="{{ $lb }}">Contenu</p>@include('admin.partials.richtext', ['name' => 'body', 'value' => old('body', $page->body)])
        <p class="mt-2 text-xs text-white/35">Si le contenu est vide, la mention « Page provisoire » s'affiche. Scripts et styles sont supprimés à l'enregistrement.</p></div>
    </section>
    <section class="space-y-5 rounded-2xl border border-white/10 bg-white/[0.02] p-6">
      <h2 class="font-serif text-xl text-white">Référencement (SEO)</h2>
      <label class="{{ $lb }}">Titre SEO (onglet du navigateur)<input name="seo_title" value="{{ old('seo_title', $page->seo_title) }}" class="{{ $in }}" placeholder="Par défaut : {{ $page->title ?: 'Titre' }} — {{ config('promo.title') }}"></label>
      <label class="{{ $lb }}">Description SEO<textarea name="seo_description" rows="2" maxlength="300" class="{{ $in }}">{{ old('seo_description', $page->seo_description) }}</textarea></label>
    </section>
  </div>
  <div class="space-y-6">
    <section class="space-y-4 rounded-2xl border border-white/10 bg-white/[0.02] p-6 text-sm">
      <label class="flex items-center gap-3"><input type="checkbox" name="published" value="1" @checked(old('published', $page->published)) class="h-4 w-4 accent-kp-gold"> Publiée</label>
      <label class="flex items-center gap-3"><input type="checkbox" name="in_nav" value="1" @checked(old('in_nav', $page->in_nav)) class="h-4 w-4 accent-kp-gold"> Ajouter au menu principal</label>
      <label class="{{ $lb }}">Ordre dans le menu<input type="number" name="nav_order" min="0" value="{{ old('nav_order', $page->nav_order ?? 0) }}" class="{{ $in }}"></label>
      <button class="w-full rounded-full bg-kp-gold py-3 text-xs font-bold uppercase tracking-[0.2em] text-black hover:bg-white">Enregistrer</button>
      @if($page->exists)<a href="{{ $page->url() }}" target="_blank" class="block text-center text-xs text-kp-gold hover:underline">Voir la page ↗</a>@endif
    </section>
    <section class="space-y-3 rounded-2xl border border-white/10 bg-white/[0.02] p-6">
      <p class="{{ $lb }}">Image d'en-tête</p>
      <div class="flex aspect-video items-center justify-center overflow-hidden rounded-xl border border-white/10 bg-black"><template x-if="preview"><img :src="preview" class="h-full w-full object-cover" alt=""></template><span x-show="!preview" class="text-xs text-white/30">Aucune image</span></div>
      <input type="file" name="hero" accept="image/jpeg,image/png,image/webp,image/gif" @change="const f = $event.target.files[0]; if (f) preview = URL.createObjectURL(f)" class="block w-full text-xs text-white/60 file:mr-3 file:rounded-full file:border-0 file:bg-kp-gold file:px-4 file:py-2 file:text-xs file:font-semibold file:text-black">
      @if($page->hero_image)<label class="flex items-center gap-2 text-xs text-white/50"><input type="checkbox" name="remove_hero" value="1" class="accent-kp-gold"> Retirer l'image</label>@endif
    </section>
  </div>
</form>
@if($page->exists && ! $page->is_system)
<form method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="mt-8" onsubmit="return confirm('Supprimer définitivement cette page ?')">@csrf @method('DELETE')<button class="text-xs text-red-400 hover:underline">Supprimer cette page</button></form>
@endif
@endsection
