@extends('admin.layout')
@section('title', 'S.A.V.')
@section('actions')<a href="{{ route('admin.sav.requests') }}" class="mr-2 rounded-full border border-white/20 px-5 py-2 text-xs font-bold uppercase tracking-[0.18em] text-white hover:border-kp-gold">Demandes @if($pending)<span class="ml-1 rounded-full bg-kp-gold px-2 text-black">{{ $pending }}</span>@endif</a><a href="{{ route('sav') }}" target="_blank" class="rounded-full bg-kp-gold px-5 py-2 text-xs font-bold uppercase tracking-[0.18em] text-black hover:bg-white">Voir la page ↗</a>@endsection
@section('content')
@php($all = array_keys($fields))
@php($pick = fn (...$pre) => array_values(array_filter($all, function ($k) use ($pre) { foreach ($pre as $p) if (str_starts_with($k, 'savp.'.$p)) return true; return false; })))
@php($groups = [
  'hero' => ['Hero & encadré (téléphone, e-mail, chiffres)', $pick('hero_', 'btn_call', 'btn_workshop', 'aside_', 'phone', 'email', 'stats')],
  'bandeau' => ['Bandeau défilant', $pick('marquee')],
  'piliers' => ['Quatre piliers', $pick('pil_', 'pillars')],
  'atelier' => ['Notre atelier (photos)', $pick('ws_', 'gallery')],
  'prestations' => ['Prestations', $pick('srv_', 'services')],
  'parcours' => ['Parcours client', $pick('proc_', 'steps')],
  'fin' => ['Fin de page (appel à l\'action)', $pick('cta_', 'btn_write', 'btn_contact', 'contact_url', 'badges')],
  'formulaire' => ['Formulaire « Écrire au S.A.V. » & messages', $pick('f_', 'e_')],
  'seo' => ['SEO & partage', $pick('seo_', 'og_')],
])
<p class="mb-6 max-w-3xl text-sm text-white/50">Tout le contenu de la page <a href="{{ route('sav') }}" target="_blank" class="text-kp-gold underline">/sav</a>. Photos de l'atelier : chemin dans <code>public/</code> (ex. <code>garage2.jpg</code>). Icônes des piliers : clock, shield, wrench, check. Les demandes du formulaire « Écrire au S.A.V. » sont dans <a href="{{ route('admin.sav.requests') }}" class="text-kp-gold underline">Demandes</a>. (La section S.A.V. de la page d'accueil se modifie dans Contenu du site › S.A.V.)</p>
<nav class="mb-8 flex flex-wrap gap-2 text-xs">@foreach($groups as $id => [$t])<a href="#{{ $id }}" class="rounded-full border border-white/15 px-4 py-2 hover:border-kp-gold">{{ $t }}</a>@endforeach</nav>
@foreach($groups as $id => [$title, $keys])
<form id="{{ $id }}" method="POST" enctype="multipart/form-data" action="{{ route('admin.sav.save') }}" class="mb-10 scroll-mt-6 space-y-5">
  @csrf @method('PUT') <input type="hidden" name="_anchor" value="{{ $id }}">
  <h2 class="font-serif text-2xl text-white">{{ $title }}</h2>
  @include('admin.partials.cms-fields', ['fields' => array_intersect_key($fields, array_flip($keys))])
  <button class="rounded-full bg-kp-gold px-8 py-3 text-xs font-bold uppercase tracking-[0.2em] text-black hover:bg-white">Enregistrer</button>
</form>
@endforeach
@endsection
