@extends('layouts.app')
@section('seo_title', sp_t('acc_title').' — KPANDJI')
@push('meta')<meta name="robots" content="noindex,nofollow">@endpush
@section('content')
<section class="relative flex min-h-screen items-center justify-center overflow-hidden bg-[#050505] px-4 pb-16 pt-32">
  <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(50%_40%_at_50%_0%,rgba(201,169,98,.16),transparent_70%)]"></div>
  <div data-sp-invite class="relative w-full max-w-md rounded-[28px] border border-kp-gold/25 bg-[#0b0b0b]/90 p-7 shadow-[0_40px_120px_-40px_rgba(201,169,98,.35)] sm:p-9">
    <div class="absolute inset-x-10 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold to-transparent"></div>
    <p class="text-[10px] font-semibold uppercase tracking-[0.35em] text-kp-gold">KPANDJI</p>
    <h1 class="mt-3 font-serif text-3xl text-white">{{ sp_t('acc_title') }}</h1>
    @if($inv)
      <p class="mt-3 text-sm text-white/60">{{ sp_t('acc_intro') }}</p>
      <form method="POST" action="{{ request()->fullUrl() }}" class="mt-7 space-y-4">@csrf
        <div class="rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-3 text-sm"><p class="text-white">{{ $inv->name }}</p><p class="text-white/45">{{ $inv->email }}</p></div>
        @if($errors->any())<div class="rounded-xl border border-red-400/30 bg-red-500/10 px-4 py-2 text-sm text-red-200">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>@endif
        <div><label for="a1" style="display:block;margin-bottom:.4rem;font-size:10px;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:rgba(255,255,255,.45)">{{ sp_t('pw_new') }}</label>
          <input id="a1" name="password" type="password" required minlength="8" autocomplete="new-password" class="min-h-[46px] w-full rounded-[14px] border border-white/15 bg-black/50 px-4 text-white focus:border-kp-gold focus:outline-none"></div>
        <div><label for="a2" style="display:block;margin-bottom:.4rem;font-size:10px;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:rgba(255,255,255,.45)">{{ sp_t('pw_confirm') }}</label>
          <input id="a2" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" class="min-h-[46px] w-full rounded-[14px] border border-white/15 bg-black/50 px-4 text-white focus:border-kp-gold focus:outline-none"></div>
        <button class="mt-2 inline-flex min-h-12 w-full items-center justify-center rounded-full bg-gradient-to-br from-[#f3e2b3] via-kp-gold to-[#9c7b3c] text-[11px] font-bold uppercase tracking-[0.22em] text-black shadow-[0_14px_36px_-12px_rgba(201,169,98,.7)]">{{ sp_t('acc_submit') }}</button>
      </form>
    @else
      <p data-sp-invite-invalid class="mt-4 text-sm leading-relaxed text-white/60">{{ sp_t('acc_invalid') }}</p>
    @endif
  </div>
</section>
@endsection
