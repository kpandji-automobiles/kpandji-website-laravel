@extends('admin.layout')
@section('title', 'Opportunités')
@section('actions')<a href="{{ route('opportunities') }}" target="_blank" class="rounded-full bg-kp-gold px-5 py-2 text-xs font-bold uppercase tracking-[0.18em] text-black hover:bg-white">Voir la page ↗</a>@endsection
@section('content')
@php($groups = ['textes' => ['En-tête de la page', ['opps.eyebrow','opps.eyebrow_en','opps.title','opps.title_en','opps.intro','opps.intro_en']],
  'cartes' => ['Cartes Kpandji Sira / Kpandji Privilège', ['opps.cards','opps.cta','opps.cta_en']],
  'seo' => ['SEO & partage', ['opps.seo_title','opps.seo_description','opps.og_title','opps.og_description']]])
<p class="mb-6 max-w-3xl text-sm text-white/50">Tout le contenu de la page <a href="{{ route('opportunities') }}" target="_blank" class="text-kp-gold underline">/opportunities</a>. Images : chemin dans <code>public/</code> (ex. <code>models/para/pic2.jpg</code>) ou URL. Dégradé du nom : <code>bronze</code> ou <code>blanc</code>. Chaque bloc a son bouton « Enregistrer ».</p>
<nav class="mb-8 flex flex-wrap gap-2 text-xs">@foreach($groups as $id => [$t])<a href="#{{ $id }}" class="rounded-full border border-white/15 px-4 py-2 hover:border-kp-gold">{{ $t }}</a>@endforeach</nav>
@foreach($groups as $id => [$title, $keys])
<form id="{{ $id }}" method="POST" action="{{ route('admin.opps.save') }}" class="mb-10 scroll-mt-6 space-y-5">
  @csrf @method('PUT') <input type="hidden" name="_anchor" value="{{ $id }}">
  <h2 class="font-serif text-2xl text-white">{{ $title }}</h2>
  @include('admin.partials.cms-fields', ['fields' => array_intersect_key($fields, array_flip($keys))])
  <button class="rounded-full bg-kp-gold px-8 py-3 text-xs font-bold uppercase tracking-[0.2em] text-black hover:bg-white">Enregistrer</button>
</form>
@endforeach
@endsection
