@extends('admin.layout')
@section('title','Promotion')
@section('content')
@php($in='mt-2 w-full rounded-lg border border-white/15 bg-black px-4 py-3 text-white outline-none focus:border-kp-gold')
<form method="POST" action="{{ route('admin.promo.save') }}" class="max-w-2xl space-y-5 rounded-2xl border border-white/10 bg-white/[0.02] p-8">@csrf @method('PUT')
  <label class="flex items-center gap-3 text-sm"><input type="checkbox" name="active" value="1" @checked(old('active', config('promo.active'))) class="h-5 w-5 accent-[#c9a962]"> <span class="text-white">Promotion active</span> <span class="text-white/40">(décochée : la section « offres » est masquée sur l'accueil)</span></label>
  <label class="block text-xs uppercase tracking-[0.2em] text-white/60">Titre de la promotion<input name="title" value="{{ old('title', config('promo.title')) }}" required class="{{ $in }}"></label>
  <label class="block text-xs uppercase tracking-[0.2em] text-white/60">Sous-titre<textarea name="subtitle" rows="2" class="{{ $in }}">{{ old('subtitle', config('promo.subtitle')) }}</textarea></label>
  <div class="grid gap-5 sm:grid-cols-2">
    <label class="block text-xs uppercase tracking-[0.2em] text-white/60">Réduction globale (%)<input type="number" step="0.5" min="0" max="90" name="reduction_percent" value="{{ old('reduction_percent', config('promo.reduction_percent')) }}" class="{{ $in }}"><span class="mt-1 block normal-case tracking-normal text-white/35">0 = pas de pourcentage affiché. Appliqué aux modèles ayant un prix catalogue sans prix promo.</span></label>
    <label class="block text-xs uppercase tracking-[0.2em] text-white/60">Date de fin<input type="date" name="ends_at" value="{{ old('ends_at', config('promo.ends_at') ? \Carbon\Carbon::parse(config('promo.ends_at'))->format('Y-m-d') : '') }}" class="{{ $in }}"><span class="mt-1 block normal-case tracking-normal text-white/35">Vide = pas de compte à rebours.</span></label>
  </div>
  <button class="rounded-full bg-kp-gold px-8 py-3 text-xs font-bold uppercase tracking-[0.22em] text-black hover:bg-white">Enregistrer</button>
</form>
@endsection
