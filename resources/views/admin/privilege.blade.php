@extends('admin.layout')
@section('title', 'Kpandji Privilège')
@section('actions')<a href="{{ route('admin.privilege.requests') }}" class="mr-2 rounded-full border border-white/20 px-5 py-2 text-xs font-bold uppercase tracking-[0.18em] text-white hover:border-kp-gold">Demandes @if($pending)<span class="ml-1 rounded-full bg-kp-gold px-2 text-black">{{ $pending }}</span>@endif</a><a href="{{ route('privilege') }}" target="_blank" class="rounded-full bg-kp-gold px-5 py-2 text-xs font-bold uppercase tracking-[0.18em] text-black hover:bg-white">Voir la page ↗</a>@endsection
@section('content')
@php($all = array_keys($fields))
@php($pick = fn (...$pre) => array_values(array_filter($all, function ($k) use ($pre) { foreach ($pre as $p) if (str_starts_with($k, 'priv.'.$p)) return true; return false; })))
@php($groups = [
  'hero' => ['Hero (haut de page)', $pick('hero_', 'back', 'scroll')],
  'chemins' => ['Deux façons de profiter (cartes, chiffres, points)', $pick('paths', 'points')],
  'parcours' => ['Parcours — 4 étapes', $pick('steps')],
  'contact' => ['Contact (téléphone, e-mail, devise)', $pick('contact_', 'phone', 'email', 'tagline')],
  'whatsapp' => ['WhatsApp — bouton flottant & fenêtre (numéro, lien, textes)', $pick('whatsapp', 'wa_')],
  'formulaires' => ['Formulaires « Parler à un conseiller » / « Nous contacter » & messages', $pick('a_', 'c_', 'f_', 'e_')],
  'seo' => ['SEO & partage', $pick('seo_', 'og_')],
])
<p class="mb-6 max-w-3xl text-sm text-white/50">Tout le contenu de la page <a href="{{ route('privilege') }}" target="_blank" class="text-kp-gold underline">/privilege</a>. Images des cartes : chemin dans <code>public/</code> (ex. <code>models/prest.png</code>). Les demandes envoyées par les deux formulaires sont dans <a href="{{ route('admin.privilege.requests') }}" class="text-kp-gold underline">Demandes</a>.</p>
<nav class="mb-8 flex flex-wrap gap-2 text-xs">@foreach($groups as $id => [$t])<a href="#{{ $id }}" class="rounded-full border border-white/15 px-4 py-2 hover:border-kp-gold">{{ $t }}</a>@endforeach</nav>
@foreach($groups as $id => [$title, $keys])
<form id="{{ $id }}" method="POST" enctype="multipart/form-data" action="{{ route('admin.privilege.save') }}" class="mb-10 scroll-mt-6 space-y-5">
  @csrf @method('PUT') <input type="hidden" name="_anchor" value="{{ $id }}">
  <h2 class="font-serif text-2xl text-white">{{ $title }}</h2>
  @include('admin.partials.cms-fields', ['fields' => array_intersect_key($fields, array_flip($keys))])
  <button class="rounded-full bg-kp-gold px-8 py-3 text-xs font-bold uppercase tracking-[0.2em] text-black hover:bg-white">Enregistrer</button>
</form>
@endforeach
@endsection
