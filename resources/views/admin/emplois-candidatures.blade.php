@extends('admin.layout')
@section('title', 'Emplois — candidatures')
@section('actions')<a href="{{ route('admin.emplois') }}" class="rounded-full border border-white/20 px-5 py-2 text-xs font-bold uppercase tracking-[0.18em] text-white hover:border-kp-gold">← Contenu de la page</a>@endsection
@section('content')
<p class="mb-6 max-w-3xl text-sm text-white/50">Candidatures envoyées depuis <a href="{{ route('emplois') }}" target="_blank" class="text-kp-gold underline">/emplois</a>. Les CV sont stockés en privé (non accessibles publiquement) et supprimés avec la candidature.</p>
<div class="overflow-x-auto rounded-2xl border border-white/10">
<table class="w-full min-w-[900px] text-sm">
  <thead class="bg-white/[0.04] text-left text-[10px] uppercase tracking-[0.2em] text-kp-gold"><tr><th class="p-4">Date</th><th class="p-4">Candidat</th><th class="p-4">Poste visé</th><th class="p-4">CV</th><th class="p-4 text-right">Statut</th></tr></thead>
  <tbody>
  @forelse($items as $q)
    <tr class="border-t border-white/10 align-top {{ $q->handled_at ? 'opacity-60' : '' }}">
      <td class="whitespace-nowrap p-4 text-white/60">{{ $q->created_at->format('d/m/Y H:i') }}</td>
      <td class="p-4"><a href="{{ route('admin.emplois.applications.show', $q) }}" class="font-semibold text-white hover:text-kp-gold">{{ $q->full_name }}</a><br><a href="mailto:{{ $q->email }}" class="text-white/60">{{ $q->email }}</a>@if($q->phone)<br><a href="tel:{{ $q->phone }}" class="text-kp-gold">{{ $q->phone }}</a>@endif</td>
      <td class="max-w-xs p-4">{{ $q->position }}</td>
      <td class="p-4">@if($q->cv_path)<a href="{{ route('admin.emplois.applications.cv', $q) }}" class="text-kp-gold underline">Télécharger</a><br><span class="text-[11px] text-white/40">{{ $q->cv_name }} · {{ number_format($q->cv_size / 1024, 0, ',', ' ') }} Ko</span>@else<span class="text-white/40">—</span>@endif</td>
      <td class="p-4 text-right">
        <a href="{{ route('admin.emplois.applications.show', $q) }}" class="mb-2 inline-block rounded-full border border-white/15 px-3 py-1.5 text-[11px] font-semibold text-white/80 hover:border-kp-gold">Voir le détail</a>
        <form method="POST" action="{{ route('admin.emplois.applications.toggle', $q) }}">@csrf @method('PATCH')<button class="whitespace-nowrap rounded-full px-3 py-1.5 text-[11px] font-semibold {{ $q->handled_at ? 'bg-emerald-500/15 text-emerald-300' : 'bg-amber-500/15 text-amber-300 hover:bg-emerald-500/20' }}">{{ $q->handled_at ? '✓ Traitée' : 'Marquer traitée' }}</button></form>
        <form method="POST" action="{{ route('admin.emplois.applications.delete', $q) }}" class="mt-2" onsubmit="return confirm('Supprimer cette candidature et son CV ?')">@csrf @method('DELETE')<button class="rounded-full px-3 py-1.5 text-[11px] font-semibold text-red-300/80 hover:bg-red-500/15">Supprimer</button></form>
      </td>
    </tr>
  @empty <tr><td colspan="5" class="p-10 text-center text-white/40">Aucune candidature pour l'instant.</td></tr>
  @endforelse
  </tbody>
</table></div>
<div class="mt-6">{{ $items->links() }}</div>
@endsection
