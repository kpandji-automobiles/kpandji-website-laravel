@extends('spaces.layout')
@section('h1', sp_t('msg_title'))
@section('space')
@php $unread = $threads->sum('unread'); @endphp
<div x-data="{c: {{ $errors->any() || request()->has('ecrire') ? 'true' : 'false' }}}" x-init="if(location.hash==='#ecrire'){c=true}">
<div class="sp-rise flex flex-wrap items-center justify-between gap-4">
  <p class="text-sm text-white/45">{{ sp_t('msg_intro') }}</p>
  <div class="flex flex-wrap gap-2">
    @if($unread)<form method="POST" action="{{ route('client.messages.read') }}">@csrf<button data-sp-readall class="sp-ghost">✓ {{ sp_t('msg_mark_all') }}</button></form>@endif
    <button type="button" data-sp-compose @click="c=!c;$nextTick(()=>c&&$refs.subj.focus())" class="sp-btn"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20l4-1 11-11-3-3L5 16l-1 4z"/></svg>{{ sp_t('msg_compose') }}</button>
  </div>
</div>
<form id="ecrire" x-show="c" x-cloak x-transition.opacity method="POST" action="{{ route('client.request') }}" data-sp-request class="sp-card mt-5 space-y-4 border-kp-gold/35 p-6 sm:p-8">@csrf
  <h2 class="font-serif text-2xl text-white">{{ sp_t('msg_compose') }}</h2><p class="text-sm text-white/50">{{ sp_t('req_intro') }}</p>
  <div class="grid gap-3 sm:grid-cols-2"><div><label class="sp-label">{{ sp_t('req_subject') }}</label><input x-ref="subj" name="subject" required maxlength="160" value="{{ old('subject', sp_t('req_default')) }}" class="sp-input"></div>
    <div><label class="sp-label">{{ sp_t('investment') }}</label><select name="investment_id" class="sp-input"><option value="">—</option>@foreach($investments as $iv)<option value="{{ $iv->id }}">{{ $iv->label }}</option>@endforeach</select></div></div>
  <div><label class="sp-label">{{ sp_t('req_message') }}</label><textarea name="message" rows="5" required maxlength="3000" class="sp-input">{{ old('message') }}</textarea></div>
  <div class="flex gap-2"><button class="sp-btn">{{ sp_t('req_send') }}</button><button type="button" @click="c=false" class="sp-ghost">{{ sp_t('cancel') }}</button></div>
</form>
</div>
<div class="sp-card sp-rise mt-6 overflow-hidden" style="animation-delay:.08s">
  <p class="px-6 py-3 text-xs text-white/40">{{ $threads->count() }} · {{ $unread }} {{ sp_t('msg_unread') }}</p>
  @forelse($threads as $t) @include('spaces.client._thread-row') @empty <p class="border-t border-white/[0.06] px-6 py-6 text-sm text-white/40">{{ sp_t('msg_none') }}</p> @endforelse
</div>
@endsection
