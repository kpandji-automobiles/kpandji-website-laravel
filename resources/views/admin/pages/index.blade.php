@extends('admin.layout')
@section('title', 'Pages')
@section('actions')<a href="{{ route('admin.pages.create') }}" class="rounded-full bg-kp-gold px-5 py-2 text-xs font-bold uppercase tracking-[0.18em] text-black hover:bg-white">+ Nouvelle page</a>@endsection
@section('content')
<div class="overflow-x-auto rounded-2xl border border-white/10">
<table class="w-full text-sm">
  <thead class="bg-white/[0.03] text-left text-[10px] uppercase tracking-[0.2em] text-white/50"><tr><th class="p-4">Page</th><th class="p-4">Adresse</th><th class="p-4">Statut</th><th class="p-4">Menu</th><th class="p-4"></th></tr></thead>
  <tbody class="divide-y divide-white/5">
  @foreach($pages as $p)
    <tr class="hover:bg-white/[0.02]">
      <td class="p-4"><p class="font-semibold text-white">{{ $p->title }}</p><p class="text-xs text-white/40">{{ $p->kicker }}</p></td>
      <td class="p-4"><a href="{{ $p->url() }}" target="_blank" class="text-kp-gold hover:underline">/{{ $p->slug }}</a> @if($p->is_system)<span class="ml-2 rounded bg-white/10 px-2 py-0.5 text-[10px] text-white/50">page du site</span>@endif</td>
      <td class="p-4">@if($p->published)<span class="text-emerald-400">Publiée</span>@else<span class="text-white/40">Brouillon</span>@endif</td>
      <td class="p-4 text-white/60">{{ $p->in_nav ? 'Oui (#'.$p->nav_order.')' : '—' }}</td>
      <td class="p-4 text-right"><a href="{{ route('admin.pages.edit', $p) }}" class="rounded-full border border-white/15 px-4 py-1.5 text-xs hover:border-kp-gold">Modifier</a>
        @unless($p->is_system)<form method="POST" action="{{ route('admin.pages.destroy', $p) }}" class="inline" onsubmit="return confirm('Supprimer cette page ?')">@csrf @method('DELETE')<button class="ml-1 rounded-full border border-white/15 px-4 py-1.5 text-xs text-red-400 hover:border-red-500">Supprimer</button></form>@endunless</td>
    </tr>
  @endforeach
  </tbody>
</table></div>
<p class="mt-4 text-xs text-white/40">Les pages du site (header/pied de page) peuvent être modifiées ou dépubliées mais pas supprimées. Les nouvelles pages sont servies à <code>/slug</code> (et <code>/page/slug</code>). Les liens du menu principal se gèrent dans <a class="text-kp-gold underline" href="{{ route('admin.content', ['tab' => 'header']) }}">Contenu › Header</a>, ou via « Ajouter au menu » ci-dessous.</p>
@endsection
