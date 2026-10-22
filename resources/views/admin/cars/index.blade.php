@extends('admin.layout')
@section('title','Voitures & offres')
@section('actions')<a href="{{ route('admin.cars.create') }}" class="rounded-full bg-kp-gold px-5 py-2.5 text-[11px] font-bold uppercase tracking-[0.2em] text-black hover:bg-white">+ Ajouter</a>@endsection
@section('content')
<form method="POST" action="{{ route('admin.cars.reorder') }}" id="order-form">@csrf</form>
<div class="overflow-x-auto rounded-2xl border border-white/10">
<table class="w-full min-w-[900px] text-sm">
  <thead class="bg-white/[0.04] text-left text-[10px] uppercase tracking-[0.2em] text-kp-gold"><tr><th class="p-4 w-24">Ordre</th><th class="p-4">Photo</th><th class="p-4">Modèle</th><th class="p-4">Prix catalogue</th><th class="p-4">Prix promo</th><th class="p-4">Pack campagne</th><th class="p-4">Visible</th><th class="p-4 text-right">Actions</th></tr></thead>
  <tbody>
  @forelse($cars as $car)
    <tr class="border-t border-white/10 {{ $car->is_active ? '' : 'opacity-50' }}">
      <td class="p-4"><input form="order-form" type="number" min="0" name="order[{{ $car->id }}]" value="{{ $car->sort_order }}" class="w-16 rounded-lg border border-white/15 bg-black px-2 py-1.5 text-center text-white"></td>
      <td class="p-4"><div class="flex h-14 w-24 items-center justify-center rounded-lg bg-black"><img src="{{ asset($car->image) }}" alt="" class="max-h-full max-w-full object-contain"></div></td>
      <td class="p-4"><p class="font-serif text-lg text-white">{{ $car->name }}</p><p class="text-[11px] text-white/40">{{ $car->category }} · /modeles/{{ $car->slug }}@if($car->hasVersions()) · {{ count($car->versions) }} versions @endif</p></td>
      <td class="p-4 text-white/70">{{ $car->price ? \App\Models\Car::fcfa($car->price) : '—' }}</td>
      <td class="p-4 font-semibold text-red-400">{{ $car->promo_price ? \App\Models\Car::fcfa($car->promo_price) : '—' }}</td>
      <td class="p-4 text-white/70">{{ $car->campaign_price ? \App\Models\Car::fcfa($car->campaign_price) : '—' }}</td>
      <td class="p-4"><form method="POST" action="{{ route('admin.cars.toggle', $car) }}">@csrf @method('PATCH')
        <button class="relative h-6 w-11 rounded-full transition {{ $car->is_active ? 'bg-kp-gold' : 'bg-white/15' }}" title="{{ $car->is_active ? 'Masquer' : 'Afficher' }}"><span class="absolute top-0.5 h-5 w-5 rounded-full bg-white transition-all {{ $car->is_active ? 'left-[22px]' : 'left-0.5' }}"></span></button></form></td>
      <td class="p-4 text-right whitespace-nowrap">
        <a href="{{ route('cars.show', $car) }}" target="_blank" class="mr-2 text-white/50 hover:text-white">Voir</a>
        <a href="{{ route('admin.cars.edit', $car) }}" class="mr-2 rounded-full border border-white/20 px-3 py-1.5 text-xs hover:border-kp-gold">Modifier</a>
        <form method="POST" action="{{ route('admin.cars.destroy', $car) }}" class="inline" x-data @submit.prevent="if (confirm('Supprimer définitivement « {{ addslashes($car->name) }} » ?')) $el.submit()">@csrf @method('DELETE')<button class="rounded-full border border-red-500/40 px-3 py-1.5 text-xs text-red-300 hover:bg-red-600 hover:text-white">Supprimer</button></form>
      </td>
    </tr>
  @empty <tr><td colspan="8" class="p-10 text-center text-white/40">Aucune voiture.</td></tr>
  @endforelse
  </tbody>
</table></div>
@if($cars->count())<button form="order-form" class="mt-5 rounded-full border border-white/20 px-6 py-2.5 text-xs uppercase tracking-[0.2em] hover:border-kp-gold">Enregistrer l'ordre d'affichage</button>@endif
@endsection
