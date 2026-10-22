@extends('admin.layout')
@section('title', 'Contenu du site')
@section('actions')<a href="{{ route('home') }}" target="_blank" class="text-xs text-white/60 hover:text-white">Voir le site ↗</a>@endsection
@section('content')
@php
  $in = 'mt-2 w-full rounded-lg border border-white/15 bg-black px-4 py-3 text-sm text-white outline-none focus:border-kp-gold';
  $lb = 'block text-xs uppercase tracking-[0.2em] text-white/60';
@endphp
<style>.kp-prose h2{font-size:1.3rem;font-weight:600}.kp-prose h3{font-size:1.1rem;font-weight:600}.kp-prose ul{list-style:disc;padding-left:1.2em}.kp-prose a{color:#c9a962;text-decoration:underline}</style>
<div class="mb-6 flex flex-wrap gap-2">
  @foreach($tabs as $k => $label)
    <a href="{{ route('admin.content', ['tab' => $k]) }}" class="rounded-full px-5 py-2 text-xs font-semibold uppercase tracking-[0.18em] transition {{ $tab === $k ? 'bg-kp-gold text-black' : 'border border-white/15 text-white/70 hover:border-kp-gold hover:text-white' }}">{{ $label }}</a>
  @endforeach
  <a href="{{ route('admin.pages.index') }}" class="rounded-full border border-dashed border-white/20 px-5 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white/60 hover:border-kp-gold hover:text-white">Pages →</a>
</div>
<p class="mb-6 max-w-3xl text-sm text-white/50">Les valeurs affichées sont celles du site. Cochez « Rétablir » pour revenir au texte d'origine. Liens acceptés : <code class="text-kp-gold">/showroom</code>, <code class="text-kp-gold">#offres</code>, <code class="text-kp-gold">https://…</code>.
  @if($tab === 'showroomv') Ces textes se modifient aussi, avec les modèles et galeries, dans <a class="text-kp-gold underline" href="{{ route('admin.showroom') }}">Showroom</a>.@endif
  @if($tab === 'promo') Pourcentage, dates et activation de la promotion : <a class="text-kp-gold underline" href="{{ route('admin.promo') }}">page Promotion</a>.@endif</p>

<form method="POST" enctype="multipart/form-data" action="{{ route('admin.content.save', $tab) }}" class="space-y-5">
  @csrf @method('PUT')
  @include('admin.partials.cms-fields')
  <div class="sticky bottom-0 -mx-5 flex justify-end border-t border-white/10 bg-kp-black/95 px-5 py-4 backdrop-blur lg:-mx-10 lg:px-10">
    <button class="rounded-full bg-kp-gold px-8 py-3 text-xs font-bold uppercase tracking-[0.2em] text-black hover:bg-white">Enregistrer « {{ $tabs[$tab] }} »</button>
  </div>
</form>
@endsection
