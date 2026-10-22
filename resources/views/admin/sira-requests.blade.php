@extends('admin.layout')
@section('title', 'Kpandji Sira — inscriptions')
@section('actions')<a href="{{ route('admin.sira') }}" class="rounded-full border border-white/20 px-5 py-2 text-xs font-bold uppercase tracking-[0.18em] text-white hover:border-kp-gold">← Contenu Sira</a>@endsection
@section('content')
<div class="overflow-x-auto rounded-2xl border border-white/10">
<table class="w-full min-w-[900px] text-sm">
  <thead class="bg-white/[0.04] text-left text-[10px] uppercase tracking-[0.2em] text-kp-gold"><tr><th class="p-4">Date</th><th class="p-4">Nom</th><th class="p-4">Pays</th><th class="p-4">Ville</th><th class="p-4">Contact</th><th class="p-4">Langue</th><th class="p-4 text-right">Statut</th></tr></thead>
  <tbody>
  @forelse($items as $q)
    <tr class="border-t border-white/10 align-top {{ $q->handled_at ? 'opacity-60' : '' }}">
      <td class="whitespace-nowrap p-4 text-white/60">{{ $q->created_at->format('d/m/Y H:i') }}</td>
      <td class="p-4 text-white">{{ $q->name }}</td>
      <td class="p-4">{{ $q->country }}</td>
      <td class="p-4">{{ $q->city }}</td>
      <td class="p-4"><a href="tel:{{ $q->phone }}" class="text-kp-gold">{{ $q->phone }}</a><br><a href="mailto:{{ $q->email }}" class="text-white/60">{{ $q->email }}</a></td>
      <td class="p-4 uppercase text-white/50">{{ $q->locale ?: '—' }}</td>
      <td class="p-4 text-right"><div class="flex justify-end gap-2">
        <form method="POST" action="{{ route('admin.sira.requests.toggle', $q) }}">@csrf @method('PATCH')<button class="whitespace-nowrap rounded-full px-3 py-1.5 text-[11px] font-semibold {{ $q->handled_at ? 'bg-emerald-500/15 text-emerald-300' : 'bg-amber-500/15 text-amber-300 hover:bg-emerald-500/20' }}">{{ $q->handled_at ? '✓ Traitée' : 'Marquer traitée' }}</button></form>
        <form method="POST" action="{{ route('admin.sira.requests.delete', $q) }}" onsubmit="return confirm('Supprimer cette inscription ?')">@csrf @method('DELETE')<button class="rounded-full px-3 py-1.5 text-[11px] font-semibold text-red-300 hover:bg-red-500/15">Supprimer</button></form>
      </div></td>
    </tr>
  @empty <tr><td colspan="7" class="p-10 text-center text-white/40">Aucune inscription pour l'instant.</td></tr>
  @endforelse
  </tbody>
</table></div>
<div class="mt-6">{{ $items->links() }}</div>
@endsection
