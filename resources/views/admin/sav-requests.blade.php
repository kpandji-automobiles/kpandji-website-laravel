@extends('admin.layout')
@section('title', 'S.A.V. — demandes « Écrire au S.A.V. »')
@section('actions')<a href="{{ route('admin.sav') }}" class="rounded-full border border-white/20 px-5 py-2 text-xs font-bold uppercase tracking-[0.18em] text-white hover:border-kp-gold">← Contenu S.A.V.</a>@endsection
@section('content')
<div class="overflow-x-auto rounded-2xl border border-white/10">
<table class="w-full min-w-[900px] text-sm">
  <thead class="bg-white/[0.04] text-left text-[10px] uppercase tracking-[0.2em] text-kp-gold"><tr><th class="p-4">Date</th><th class="p-4">Nom</th><th class="p-4">Contact</th><th class="p-4">Modèle</th><th class="p-4">Panne / besoin</th><th class="p-4">Localisation</th><th class="p-4">Langue</th><th class="p-4 text-right">Statut</th></tr></thead>
  <tbody>
  @forelse($items as $q)
    <tr class="border-t border-white/10 align-top {{ $q->handled_at ? 'opacity-60' : '' }}">
      <td class="whitespace-nowrap p-4 text-white/60">{{ $q->created_at->format('d/m/Y H:i') }}</td>
      <td class="p-4 text-white">{{ $q->name }}</td>
      <td class="p-4">@if(filter_var($q->contact, FILTER_VALIDATE_EMAIL))<a href="mailto:{{ $q->contact }}" class="text-kp-gold">{{ $q->contact }}</a>@else<a href="tel:{{ preg_replace('/[^0-9+]/', '', $q->contact) }}" class="text-kp-gold">{{ $q->contact }}</a>@endif</td>
      <td class="p-4">{{ $q->model }}</td>
      <td class="max-w-xs whitespace-pre-line p-4 text-white/70">{{ $q->issue }}</td>
      <td class="p-4">{{ $q->location }}</td>
      <td class="p-4 uppercase text-white/50">{{ $q->locale ?: '—' }}</td>
      <td class="p-4 text-right"><div class="flex justify-end gap-2">
        <form method="POST" action="{{ route('admin.sav.requests.toggle', $q) }}">@csrf @method('PATCH')<button class="whitespace-nowrap rounded-full px-3 py-1.5 text-[11px] font-semibold {{ $q->handled_at ? 'bg-emerald-500/15 text-emerald-300' : 'bg-amber-500/15 text-amber-300 hover:bg-emerald-500/20' }}">{{ $q->handled_at ? '✓ Traitée' : 'Marquer traitée' }}</button></form>
        <form method="POST" action="{{ route('admin.sav.requests.delete', $q) }}" onsubmit="return confirm('Supprimer cette demande ?')">@csrf @method('DELETE')<button class="rounded-full px-3 py-1.5 text-[11px] font-semibold text-red-300 hover:bg-red-500/15">Supprimer</button></form>
      </div></td>
    </tr>
  @empty <tr><td colspan="8" class="p-10 text-center text-white/40">Aucune demande pour l'instant.</td></tr>
  @endforelse
  </tbody>
</table></div>
<div class="mt-6">{{ $items->links() }}</div>
@endsection
