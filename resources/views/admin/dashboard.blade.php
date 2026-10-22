@extends('admin.layout')
@section('title','Tableau de bord')
@section('content')
<div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
  @foreach([['Voitures en ligne', "$carsActive / $carsTotal", route('admin.cars.index')], ['Demandes en attente', $reqPending, route('admin.requests')], ['Demandes au total', $reqTotal, route('admin.requests')], ['Promotion', config('promo.active') ? 'Active' : 'Désactivée', route('admin.promo')]] as [$l,$v,$h])
  <a href="{{ $h }}" class="rounded-2xl border border-white/10 bg-white/[0.03] p-6 transition hover:border-kp-gold/60"><p class="text-[11px] uppercase tracking-[0.25em] text-white/50">{{ $l }}</p><p class="mt-3 font-serif text-4xl text-kp-gold">{{ $v }}</p></a>
  @endforeach
</div>
<div class="mt-8 rounded-2xl border border-white/10 bg-white/[0.02] p-6">
  <div class="flex items-center justify-between"><h2 class="font-serif text-xl text-white">Dernières demandes</h2><a href="{{ route('admin.requests') }}" class="text-xs text-kp-gold">Tout voir →</a></div>
  <table class="mt-4 w-full text-sm"><tbody>
  @forelse($latest as $q)<tr class="border-t border-white/10"><td class="py-3 text-white">{{ $q->name }}</td><td class="text-white/60">{{ $q->phone }}</td><td class="text-white/60">{{ $q->car?->name ?? '—' }}</td><td class="text-white/40">{{ $q->created_at->format('d/m/Y H:i') }}</td><td class="text-right">{!! $q->handled_at ? '<span class="text-emerald-400">Traitée</span>' : '<span class="text-amber-400">En attente</span>' !!}</td></tr>
  @empty<tr><td class="py-6 text-center text-white/40">Aucune demande pour l'instant.</td></tr>@endforelse
  </tbody></table>
</div>
<div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('admin.cars.create') }}" class="rounded-full bg-kp-gold px-6 py-3 text-xs font-bold uppercase tracking-[0.2em] text-black hover:bg-white">+ Ajouter une voiture</a><a href="{{ route('admin.promo') }}" class="rounded-full border border-white/20 px-6 py-3 text-xs uppercase tracking-[0.2em] hover:border-kp-gold">Régler la promotion</a></div>

<h2 class="mt-12 font-serif text-xl text-white">Gérer le site</h2>
<div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
  @foreach(array_merge([['Voitures & offres', 'Modèles, prix, images, ordre', route('admin.cars.index')], ['Promotion', 'Pourcentage, date de fin, activation', route('admin.promo')], ['Pages', 'Pages du site et nouvelles pages', route('admin.pages.index')], ['Demandes d\'essai', 'Formulaires reçus', route('admin.requests')]], collect(config('cms.tabs'))->map(fn ($l, $k) => ['Contenu › '.$l, 'Textes, liens, images', route('admin.content', ['tab' => $k])])->values()->all()) as [$l, $d, $h])
    <a href="{{ $h }}" class="rounded-2xl border border-white/10 bg-white/[0.02] p-5 transition hover:border-kp-gold"><p class="font-semibold text-white">{{ $l }}</p><p class="mt-1 text-xs text-white/45">{{ $d }}</p></a>
  @endforeach
</div>
@endsection
