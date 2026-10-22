@extends('spaces.layout')
@section('h1', sp_t('nav_invitations'))
@section('space')
@php $lk = session('inv_link'); @endphp
@if($lk)
  <div data-sp-link x-data="{c:false}" class="sp-card sp-rise mb-6 border-kp-gold/40 p-6">
    <p class="text-sm text-white">{{ sp_t('inv_link_ready') }} <span class="text-kp-gold">{{ $lk['name'] }}</span></p>
    <p class="mt-1 text-xs {{ $lk['status']==='sent' ? 'text-emerald-300' : 'text-amber-200' }}">{{ sp_t(['sent'=>'inv_mail_sent','logged'=>'inv_mail_logged'][$lk['status']] ?? 'inv_mail_failed') }}</p>
    <div class="mt-4 flex flex-col gap-2 sm:flex-row"><input readonly value="{{ $lk['url'] }}" x-ref="u" data-sp-link-url class="sp-input font-mono text-xs" @focus="$el.select()">
      <button type="button" class="sp-btn shrink-0" @click="navigator.clipboard?.writeText($refs.u.value).catch(()=>{});$refs.u.select();c=true;setTimeout(()=>c=false,2000)"><span x-text="c ? @js(sp_t('inv_copied')) : @js(sp_t('inv_copy'))"></span></button>
      <a target="_blank" rel="noopener" href="https://wa.me/{{ preg_replace('/\D/','',$lk['phone'] ?? '') }}?text={{ rawurlencode(str_replace([':name',':link'],[$lk['name'],$lk['url']],sp_t('inv_mail_body'))) }}" class="sp-ghost shrink-0 self-center">{{ sp_t('inv_whatsapp') }}</a></div>
  </div>
@endif
<section id="a-valider" data-sp-validate class="sp-card sp-rise mb-6 overflow-hidden {{ $toValidate->count() ? 'border-kp-gold/45 shadow-[0_0_60px_-30px_rgba(201,169,98,.7)]' : '' }}">
  <div class="flex flex-wrap items-center gap-3 px-6 pt-6"><h2 class="font-serif text-2xl text-white">{{ sp_t('val_title') }}</h2>
    <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full px-2 text-[11px] font-bold {{ $toValidate->count() ? 'bg-kp-gold text-black' : 'bg-white/10 text-white/50' }}">{{ $toValidate->count() }}</span></div>
  <p class="px-6 pt-2 text-sm text-white/45">{{ sp_t('val_intro') }}</p>
  <div class="mt-4">
    @forelse($toValidate as $u)
      <div data-sp-pending="{{ $u->email }}" class="flex flex-col gap-3 border-t border-white/[0.06] px-6 py-4 sm:flex-row sm:items-center">
        <div class="flex min-w-0 flex-1 items-center gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-kp-gold/40 bg-kp-gold/10 font-serif text-kp-gold">{{ mb_strtoupper(mb_substr($u->name,0,1)) }}</span>
          <div class="min-w-0"><p class="truncate text-sm text-white">{{ $u->name }} <span class="sp-badge sp-b-pending ml-1">{{ sp_t('role_'.$u->role) }}</span></p><p class="truncate text-xs text-white/40">{{ $u->email }}{{ $u->phone ? ' · '.$u->phone : '' }} · {{ $u->created_at->format('d/m/Y H:i') }}</p></div></div>
        <div class="flex gap-2"><form method="POST" action="{{ route('manager.accounts.approve', $u) }}">@csrf<button data-sp-approve class="sp-btn !min-h-10 !px-5">{{ sp_t('val_ok') }}</button></form>
          <form method="POST" action="{{ route('manager.accounts.refuse', $u) }}" onsubmit="return confirm(@js(sp_t('val_no').' ?'))">@csrf<button data-sp-refuse class="sp-ghost hover:!border-red-400 hover:!text-red-300">{{ sp_t('val_no') }}</button></form></div>
      </div>
    @empty <p class="border-t border-white/[0.06] px-6 py-5 text-sm text-white/40">{{ sp_t('val_none') }}</p> @endforelse
  </div>
  @if($refused->count())
    <details class="border-t border-white/[0.06] px-6 py-4"><summary class="cursor-pointer text-xs uppercase tracking-[0.2em] text-white/40">{{ sp_t('val_refused_list') }} ({{ $refused->count() }})</summary>
      @foreach($refused as $u)<div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-sm"><span class="text-white/60">{{ $u->name }} · {{ $u->email }} <span class="sp-badge sp-b-refused">{{ sp_t('role_'.$u->role) }}</span></span>
        <form method="POST" action="{{ route('manager.accounts.approve', $u) }}">@csrf<button class="sp-ghost">{{ sp_t('val_reapprove') }}</button></form></div>@endforeach</details>
  @endif
</section>
<div class="grid gap-6 lg:grid-cols-12">
  <form method="POST" action="{{ route('manager.invitations.store') }}" data-sp-invite-form class="sp-card sp-rise h-fit space-y-4 p-6 sm:p-8 lg:col-span-4">@csrf
    <h2 class="font-serif text-2xl text-white">{{ sp_t('inv_title') }}</h2>
    <p class="text-sm text-white/50">{{ sp_t('inv_intro') }}</p>
    <div><label class="sp-label" for="i1">{{ sp_t('name') }}</label><input id="i1" name="name" required maxlength="120" value="{{ old('name') }}" class="sp-input"></div>
    <div><label class="sp-label" for="i2">{{ sp_t('email') }}</label><input id="i2" name="email" type="email" required value="{{ old('email') }}" class="sp-input"></div>
    <div><span class="sp-label">{{ sp_t('role') }}</span><div class="grid grid-cols-2 gap-2">
      @foreach(['client','manager'] as $ro)<label class="relative cursor-pointer"><input type="radio" name="role" value="{{ $ro }}" class="peer sr-only" @checked(old('role','client')===$ro)>
        <span class="flex min-h-11 items-center justify-center rounded-xl border border-white/12 text-[11px] font-semibold uppercase tracking-[0.16em] text-white/60 transition peer-checked:border-kp-gold peer-checked:bg-kp-gold/15 peer-checked:text-kp-gold peer-focus-visible:ring-2 peer-focus-visible:ring-kp-gold/50">{{ sp_t('role_'.$ro) }}</span></label>@endforeach
    </div></div>
    <div><label class="sp-label" for="i3">{{ sp_t('phone') }}</label><input id="i3" name="phone" value="{{ old('phone') }}" class="sp-input" placeholder="+225…"></div>
    <button class="sp-btn w-full">{{ sp_t('inv_send') }}</button>
  </form>
  <div class="sp-card sp-rise overflow-hidden lg:col-span-8" style="animation-delay:.08s">
    <h2 class="px-6 py-5 font-serif text-xl text-white">{{ sp_t('inv_list') }}</h2>
    @forelse($items as $inv) @php $st = $inv->state(); @endphp
      <div data-sp-inv="{{ $inv->email }}" class="flex flex-col gap-3 border-t border-white/[0.06] px-6 py-4 sm:flex-row sm:items-center">
        <div class="min-w-0 flex-1"><p class="truncate text-sm text-white">{{ $inv->name }}</p><p class="truncate text-xs text-white/40">{{ $inv->email }}{{ $inv->phone ? ' · '.$inv->phone : '' }}</p>
          <p class="mt-1 text-[11px] text-white/30">{{ $inv->created_at->format('d/m/Y') }} · {{ $inv->inviter->name ?? '—' }}@if($st==='pending') · {{ sp_t('inv_expires') }} {{ $inv->expires_at->format('d/m/Y') }}@endif · {{ sp_t('mail_'.($inv->mail_status ?: 'none')) }}</p></div>
        <div class="flex flex-wrap items-center gap-2"><span class="sp-badge sp-b-expired">{{ sp_t('role_'.($inv->role ?? 'client')) }}</span><span class="sp-badge sp-b-{{ $st }}">{{ sp_t('inv_'.$st) }}</span>
          @if($st!=='accepted')<form method="POST" action="{{ route('manager.invitations.resend', $inv->id) }}">@csrf<button class="sp-ghost">{{ sp_t('inv_resend') }}</button></form>@endif
          @if($st==='pending')<form method="POST" action="{{ route('manager.invitations.revoke', $inv->id) }}">@csrf<button class="sp-ghost hover:!border-red-400 hover:!text-red-300">{{ sp_t('inv_revoke') }}</button></form>@endif
        </div>
      </div>
    @empty <p class="border-t border-white/10 px-6 py-6 text-sm text-white/40">{{ sp_t('none') }}</p> @endforelse
  </div>
</div>
@endsection
