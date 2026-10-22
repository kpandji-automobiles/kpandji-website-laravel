@extends('admin.layout')
@section('title', 'Candidature — '.$q->full_name)
@section('actions')<a href="{{ route('admin.emplois.applications') }}" class="rounded-full border border-white/20 px-5 py-2 text-xs font-bold uppercase tracking-[0.18em] text-white hover:border-kp-gold">← Toutes les candidatures</a>@endsection
@section('content')
@php($rows = ['Reçue le' => $q->created_at->format('d/m/Y H:i'), 'Nom complet' => $q->full_name, 'E-mail' => $q->email, 'Téléphone' => $q->phone, 'Poste visé' => $q->position, 'Synthèse du CV' => $q->cv_summary, 'Formation & certifications' => $q->education, 'Compétences' => $q->skills, 'Langues' => $q->languages, 'Message complémentaire' => $q->extras, 'Langue du site' => strtoupper((string) $q->locale)])
<div class="max-w-4xl rounded-2xl border border-white/10 bg-white/[0.02]" data-kp-application>
  <dl class="divide-y divide-white/10">
    @foreach($rows as $k => $v)
    <div class="grid gap-2 px-6 py-4 sm:grid-cols-[220px_1fr]"><dt class="text-[10px] font-semibold uppercase tracking-[0.2em] text-kp-gold">{{ $k }}</dt><dd class="whitespace-pre-line text-sm text-white/85">@if($k === 'E-mail')<a href="mailto:{{ $v }}" class="underline">{{ $v }}</a>@else{{ filled($v) ? $v : '—' }}@endif</dd></div>
    @endforeach
    <div class="grid gap-2 px-6 py-4 sm:grid-cols-[220px_1fr]"><dt class="text-[10px] font-semibold uppercase tracking-[0.2em] text-kp-gold">CV joint</dt><dd class="text-sm">@if($q->cv_path)<a href="{{ route('admin.emplois.applications.cv', $q) }}" data-kp-cv-download class="text-kp-gold underline">Télécharger « {{ $q->cv_name }} »</a> <span class="text-white/40">({{ number_format($q->cv_size / 1024, 0, ',', ' ') }} Ko)</span>@else<span class="text-white/40">Aucun fichier</span>@endif</dd></div>
  </dl>
</div>
<div class="mt-6 flex flex-wrap gap-3">
  <form method="POST" action="{{ route('admin.emplois.applications.toggle', $q) }}">@csrf @method('PATCH')<button class="rounded-full px-5 py-2 text-xs font-semibold {{ $q->handled_at ? 'bg-emerald-500/15 text-emerald-300' : 'bg-amber-500/15 text-amber-300' }}">{{ $q->handled_at ? '✓ Traitée — remettre en attente' : 'Marquer traitée' }}</button></form>
  <form method="POST" action="{{ route('admin.emplois.applications.delete', $q) }}" onsubmit="return confirm('Supprimer cette candidature et son CV ?')">@csrf @method('DELETE')<button data-kp-delete class="rounded-full border border-red-400/30 px-5 py-2 text-xs font-semibold text-red-300 hover:bg-red-500/15">Supprimer la candidature et le CV</button></form>
</div>
@endsection
