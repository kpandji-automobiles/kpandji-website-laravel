@extends('admin.layout')
@section('title', 'Kpandji Automobiles')
@section('actions')<a href="{{ route('automobiles') }}" target="_blank" class="rounded-full bg-kp-gold px-5 py-2 text-xs font-bold uppercase tracking-[0.18em] text-black hover:bg-white">Voir la page ↗</a>@endsection
@section('content')
@php($all = array_keys($fields))
@php($pick = fn (...$pre) => array_values(array_filter($all, function ($k) use ($pre) { foreach ($pre as $p) if (str_starts_with($k, 'brand.'.$p)) return true; return false; })))
@php($groups = [
  'hero' => ['Hero & chiffres', $pick('hero_', 'btn_', 'discover_url', 'contact_url', 'scroll', 'stats')],
  'histoire' => ['Notre histoire (fondateur, origine, mission, citation)', $pick('story_', 'founder_', 'origin_', 'mission_', 'quote')],
  'bandeau' => ['Bandeau défilant', $pick('marquee')],
  'engagements' => ['Nos engagements (piliers)', $pick('pil_', 'pillar')],
  'trajectoire' => ['Notre trajectoire', $pick('jr_', 'journey')],
  'installations' => ['Nos installations (photos)', $pick('gal_', 'gallery')],
  'fin' => ['Fin de page (appel à l\'action)', $pick('cta_')],
  'seo' => ['SEO & partage', $pick('seo_', 'og_')],
])
<p class="mb-6 max-w-3xl text-sm text-white/50">Tout le contenu de la page <a href="{{ route('automobiles') }}" target="_blank" class="text-kp-gold underline">/kpandji-automobiles</a>. Images : envoi de fichier ou chemin dans <code>public/</code>. Icônes des piliers : pulse, star, clock, pin, truck, globe.</p>
<nav class="mb-8 flex flex-wrap gap-2 text-xs">@foreach($groups as $id => [$t])<a href="#{{ $id }}" class="rounded-full border border-white/15 px-4 py-2 hover:border-kp-gold">{{ $t }}</a>@endforeach</nav>
@foreach($groups as $id => [$title, $keys])
<form id="{{ $id }}" method="POST" enctype="multipart/form-data" action="{{ route('admin.brand.save') }}" class="mb-10 scroll-mt-6 space-y-5">
  @csrf @method('PUT') <input type="hidden" name="_anchor" value="{{ $id }}">
  <h2 class="font-serif text-2xl text-white">{{ $title }}</h2>
  @include('admin.partials.cms-fields', ['fields' => array_intersect_key($fields, array_flip($keys))])
  <button class="rounded-full bg-kp-gold px-8 py-3 text-xs font-bold uppercase tracking-[0.2em] text-black hover:bg-white">Enregistrer</button>
</form>
@endforeach
@endsection
