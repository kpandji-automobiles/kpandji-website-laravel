@extends('admin.layout')
@section('title',"Demandes d'essai & messages Contact")
@section('content')
<div class="overflow-x-auto rounded-2xl border border-white/10">
<table class="w-full min-w-[900px] text-sm">
  <thead class="bg-white/[0.04] text-left text-[10px] uppercase tracking-[0.2em] text-kp-gold"><tr><th class="p-4">Date</th><th class="p-4">Type</th><th class="p-4">Nom</th><th class="p-4">Contact</th><th class="p-4">Modèle</th><th class="p-4">Date souhaitée</th><th class="p-4">Sujet / message</th><th class="p-4 text-right">Statut</th></tr></thead>
  <tbody>
  @forelse($items as $q)
    <tr class="border-t border-white/10 align-top {{ $q->handled_at ? 'opacity-60' : '' }}">
      <td class="p-4 whitespace-nowrap text-white/60">{{ $q->created_at->format('d/m/Y H:i') }}</td>
      <td class="p-4">{{ ['essai' => 'Essai', 'contact' => 'Contact (/contact)'][$q->type] ?? 'Information' }}</td>
      <td class="p-4 text-white">{{ $q->name }}</td>
      <td class="p-4">@if($q->phone)<a href="tel:{{ $q->phone }}" class="text-kp-gold">{{ $q->phone }}</a><br>@endif @if($q->email)<a href="mailto:{{ $q->email }}" class="text-white/60">{{ $q->email }}</a>@endif</td>
      <td class="p-4">{{ $q->car?->name ?? '—' }}</td>
      <td class="p-4">{{ $q->preferred_date?->format('d/m/Y') ?? '—' }}</td>
      <td class="p-4 max-w-xs whitespace-pre-line text-white/60">@if($q->subject)<span class="font-semibold text-white/85">{{ $q->subject }}</span><br>@endif{{ $q->message ?: '—' }}</td>
      <td class="p-4 text-right"><form method="POST" action="{{ route('admin.requests.toggle', $q) }}">@csrf @method('PATCH')<button class="whitespace-nowrap rounded-full px-3 py-1.5 text-[11px] font-semibold {{ $q->handled_at ? 'bg-emerald-500/15 text-emerald-300' : 'bg-amber-500/15 text-amber-300 hover:bg-emerald-500/20' }}">{{ $q->handled_at ? '✓ Traitée' : 'Marquer traitée' }}</button></form><form method="POST" action="{{ route('admin.requests.delete', $q) }}" class="mt-2" onsubmit="return confirm('Supprimer cette demande ?')">@csrf @method('DELETE')<button class="rounded-full px-3 py-1.5 text-[11px] font-semibold text-red-300/80 hover:bg-red-500/15">Supprimer</button></form></td>
    </tr>
  @empty <tr><td colspan="8" class="p-10 text-center text-white/40">Aucune demande pour l'instant.</td></tr>
  @endforelse
  </tbody>
</table></div>
<div class="mt-6">{{ $items->links() }}</div>
@endsection
