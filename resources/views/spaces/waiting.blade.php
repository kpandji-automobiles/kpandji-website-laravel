{{-- Page d'attente : compte invité pas encore validé (ou refusé) --}}
@extends('layouts.app')
@section('seo_title', sp_t($u->account_status==='refused' ? 'wait_refused_title' : 'wait_title').' — KPANDJI')
@push('meta')<meta name="robots" content="noindex,nofollow">@endpush
@section('content')
@php $ref = $u->account_status === 'refused'; @endphp
<style>@keyframes sp-wait-spin{to{transform:rotate(360deg)}}@keyframes sp-wait-pulse{0%,100%{opacity:.5}50%{opacity:1}}.sp-wait-ring{animation:sp-wait-spin 14s linear infinite}.sp-wait-dot{animation:sp-wait-pulse 2.4s ease-in-out infinite}@media (prefers-reduced-motion:reduce){.sp-wait-ring,.sp-wait-dot{animation:none}}</style>
<section data-sp-waiting="{{ $u->account_status }}" class="relative flex min-h-screen items-center justify-center overflow-hidden bg-[#050505] px-4 pb-16 pt-32">
  <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(50%_40%_at_50%_10%,rgba(201,169,98,.16),transparent_70%)]"></div>
  <div class="relative w-full max-w-lg rounded-[28px] border border-kp-gold/25 bg-[#0b0b0b]/90 p-8 text-center shadow-[0_40px_120px_-40px_rgba(201,169,98,.35)] sm:p-10">
    <div class="absolute inset-x-12 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold to-transparent"></div>
    <div class="relative mx-auto flex h-24 w-24 items-center justify-center">
      <span class="sp-wait-ring absolute inset-0 rounded-full border border-dashed border-kp-gold/40"></span>
      <span class="absolute inset-3 rounded-full bg-kp-gold/10 ring-1 ring-kp-gold/30"></span>
      @if($ref)
        <svg class="relative h-9 w-9 text-white/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M8 12h8"/></svg>
      @else
        <svg class="sp-wait-dot relative h-9 w-9 text-kp-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
      @endif
    </div>
    <p class="mt-6 text-[10px] font-semibold uppercase tracking-[0.35em] text-kp-gold">KPANDJI</p>
    <h1 class="mt-3 font-serif text-3xl text-white">{{ sp_t($ref ? 'wait_refused_title' : 'wait_title') }}</h1>
    <p class="mx-auto mt-4 max-w-sm text-sm leading-relaxed text-white/60">{{ sp_t($ref ? 'wait_refused_text' : 'wait_text') }}</p>
    <p class="mt-5 text-xs text-white/40">{{ $u->name }} · {{ $u->email }}</p>
    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
      @unless($ref)<a href="{{ route('space.waiting') }}" class="inline-flex min-h-12 items-center justify-center rounded-full bg-gradient-to-br from-[#f3e2b3] via-kp-gold to-[#9c7b3c] px-7 text-[11px] font-bold uppercase tracking-[0.2em] text-black">{{ sp_t('wait_refresh') }}</a>@endunless
      <a href="{{ route('home') }}" class="inline-flex min-h-12 items-center justify-center rounded-full border border-white/15 px-7 text-[11px] font-semibold uppercase tracking-[0.2em] text-white/70 hover:border-kp-gold hover:text-kp-gold">{{ sp_t('wait_home') }}</a>
      <form method="POST" action="{{ route('account.logout') }}">@csrf<button class="inline-flex min-h-12 w-full items-center justify-center rounded-full px-5 text-[11px] font-semibold uppercase tracking-[0.2em] text-white/45 hover:text-white">{{ sp_t('logout') }}</button></form>
    </div>
  </div>
</section>
@endsection
