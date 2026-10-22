@extends('admin.layout')
@section('title', 'Kpandji Sira')
@section('actions')<a href="{{ route('admin.sira.requests') }}" class="mr-2 rounded-full border border-white/20 px-5 py-2 text-xs font-bold uppercase tracking-[0.18em] text-white hover:border-kp-gold">Inscriptions @if($pending)<span class="ml-1 rounded-full bg-kp-gold px-2 text-black">{{ $pending }}</span>@endif</a><a href="{{ route('sira') }}" target="_blank" class="rounded-full bg-kp-gold px-5 py-2 text-xs font-bold uppercase tracking-[0.18em] text-black hover:bg-white">Voir la page ↗</a>@endsection
@section('content')
@php($all = array_keys($fields))
@php($pick = fn (...$pre) => array_values(array_filter($all, function ($k) use ($pre) { foreach ($pre as $p) if (str_starts_with($k, 'sira.'.$p)) return true; return false; })))
@php($groups = [
  'hero' => ['Hero (haut de page)', $pick('hero_', 'back', 'cta')],
  'bref' => ['En bref', $pick('brief')],
  'parcours' => ['Comment ça marche', $pick('how_', 'steps')],
  'niveaux' => ['Les 3 niveaux', $pick('tiers', 'veh_short', 'minimum', 'vehicles', 'brochure_btn', 'model_alt')],
  'inclus' => ['Ce qui est inclus', $pick('inc_', 'included')],
  'fin' => ['Fin de page', $pick('final_', 'advisor')],
  'formulaire' => ['Formulaire « M\'inscrire au projet » & messages', $pick('f_', 'e_')],
  'seo' => ['SEO & partage', $pick('seo_', 'og_')],
])
<p class="mb-6 max-w-3xl text-sm text-white/50">Tout le contenu de la page <a href="{{ route('sira') }}" target="_blank" class="text-kp-gold underline">/sira</a>. Images et fiches PDF des niveaux : chemin dans <code>public/</code> (ex. <code>derniers/banco.png</code>). Couleur d'un niveau : code hex (ex. <code>#CD7F32</code>). Icônes « Inclus » : shield, doc, umbrella, card, gear, car. Les inscriptions envoyées par le formulaire sont dans <a href="{{ route('admin.sira.requests') }}" class="text-kp-gold underline">Inscriptions</a>.</p>
<nav class="mb-8 flex flex-wrap gap-2 text-xs">@foreach($groups as $id => [$t])<a href="#{{ $id }}" class="rounded-full border border-white/15 px-4 py-2 hover:border-kp-gold">{{ $t }}</a>@endforeach</nav>
@foreach($groups as $id => [$title, $keys])
<form id="{{ $id }}" method="POST" enctype="multipart/form-data" action="{{ route('admin.sira.save') }}" class="mb-10 scroll-mt-6 space-y-5">
  @csrf @method('PUT') <input type="hidden" name="_anchor" value="{{ $id }}">
  <h2 class="font-serif text-2xl text-white">{{ $title }}</h2>
  @include('admin.partials.cms-fields', ['fields' => array_intersect_key($fields, array_flip($keys))])
  <button class="rounded-full bg-kp-gold px-8 py-3 text-xs font-bold uppercase tracking-[0.2em] text-black hover:bg-white">Enregistrer</button>
</form>
@endforeach
@endsection
