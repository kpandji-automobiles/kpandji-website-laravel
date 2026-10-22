@extends('admin.layout')
@section('title', 'Modèles (accueil)')
@section('actions')<a href="{{ route('admin.modeles.create') }}" class="rounded-full bg-kp-gold px-5 py-2 text-xs font-bold uppercase tracking-[0.18em] text-black hover:bg-white">+ Ajouter un modèle</a>@endsection
@section('content')
<div class="overflow-x-auto rounded-2xl border border-white/10">
<table class="w-full text-sm">
  <thead class="bg-white/[0.03] text-left text-[10px] uppercase tracking-[0.2em] text-white/50"><tr><th class="p-4">Ordre</th><th class="p-4">Modèle</th><th class="p-4">Lien « Découvrir »</th><th class="p-4">Statut</th><th class="p-4"></th></tr></thead>
  <tbody class="divide-y divide-white/5">
  @foreach($modeles as $m)
    <tr class="hover:bg-white/[0.02]">
      <td class="p-4 text-white/60">{{ $m->sort }}</td>
      <td class="p-4"><div class="flex items-center gap-3">@if($m->top_image)<img src="{{ cms_asset($m->top_image) }}" class="h-10 w-16 rounded object-cover" alt="">@endif<div><p class="font-semibold text-white">{{ $m->name }}</p><p class="max-w-md truncate text-xs text-white/40">{{ $m->description_fr }}</p></div></div></td>
      <td class="p-4"><a href="{{ $m->link() }}" target="_blank" class="text-kp-gold hover:underline">{{ parse_url($m->link(), PHP_URL_PATH) }}</a></td>
      <td class="p-4"><span class="{{ $m->is_active ? 'text-emerald-400' : 'text-white/40' }}">Accueil : {{ $m->is_active ? 'oui' : 'non' }}</span><br><span class="{{ $m->in_showroom ? 'text-emerald-400' : 'text-white/40' }}">Showroom : {{ $m->in_showroom ? 'oui' : 'non' }}</span></td>
      <td class="p-4 text-right"><a href="{{ route('admin.modeles.edit', $m) }}" class="rounded-full border border-white/15 px-4 py-1.5 text-xs hover:border-kp-gold">Modifier</a>
        <form method="POST" action="{{ route('admin.modeles.destroy', $m) }}" class="inline" onsubmit="return confirm('Retirer ce modèle de la section ?')">@csrf @method('DELETE')<button class="ml-1 rounded-full border border-white/15 px-4 py-1.5 text-xs text-red-400 hover:border-red-500">Supprimer</button></form></td>
    </tr>
  @endforeach
  </tbody>
</table></div>
<p class="mt-4 text-xs text-white/40">Section « Modèles » de l'accueil (au-dessus du S.A.V.) et showroom virtuel (/showroom). Textes du showroom : <a class="text-kp-gold underline" href="{{ route('admin.showroom') }}">Showroom</a>. Le titre de la section se modifie dans <a class="text-kp-gold underline" href="{{ route('admin.content', ['tab' => 'modeles']) }}">Contenu › Modèles</a>.</p>
@endsection
