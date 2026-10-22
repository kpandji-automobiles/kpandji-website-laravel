{{-- Gabarit commun des espaces Manager / Client (noir & or, comme le site). Libellés : onglet CMS « Espaces Manager & Client ». --}}
@extends('layouts.app')
@section('seo_title')@yield('h1') — KPANDJI @endsection
@push('meta')<meta name="robots" content="noindex,nofollow">@endpush
@section('content')
<style>
  .sp-card{border:1px solid rgba(201,169,98,.16);background:linear-gradient(180deg,rgba(255,255,255,.035),rgba(255,255,255,.01));border-radius:24px;box-shadow:0 30px 80px -40px rgba(0,0,0,.9)}
  .sp-input{width:100%;min-height:46px;border-radius:14px;border:1px solid rgba(255,255,255,.12);background:rgba(0,0,0,.45);padding:.65rem 1rem;color:#fff;font-size:14px;transition:border-color .2s,box-shadow .2s}
  .sp-input:focus{outline:none;border-color:#c9a962;box-shadow:0 0 0 3px rgba(201,169,98,.18)}
  select.sp-input{appearance:none;background-image:linear-gradient(45deg,transparent 50%,#c9a962 50%),linear-gradient(135deg,#c9a962 50%,transparent 50%);background-position:calc(100% - 18px) 50%,calc(100% - 13px) 50%;background-size:5px 5px;background-repeat:no-repeat;padding-right:2.2rem}
  .sp-input::-webkit-calendar-picker-indicator{filter:invert(.8) sepia(1) saturate(3) hue-rotate(5deg)}
  .sp-label{display:block;margin-bottom:.4rem;font-size:10px;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:rgba(255,255,255,.45)}
  .sp-btn{display:inline-flex;min-height:46px;align-items:center;justify-content:center;gap:.5rem;border-radius:999px;padding:0 1.5rem;font-size:11px;font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:#0a0a0a;background:linear-gradient(135deg,#f3e2b3,#c9a962 55%,#9c7b3c);box-shadow:0 12px 30px -12px rgba(201,169,98,.7);transition:transform .2s,filter .2s}
  .sp-btn:hover{filter:brightness(1.08);transform:translateY(-1px)}
  .sp-ghost{display:inline-flex;min-height:40px;align-items:center;justify-content:center;gap:.4rem;border-radius:999px;border:1px solid rgba(255,255,255,.15);padding:0 1rem;font-size:10px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,255,255,.75);transition:all .2s}
  .sp-ghost:hover{border-color:#c9a962;color:#c9a962}
  .sp-badge{display:inline-flex;align-items:center;gap:.35rem;border-radius:999px;padding:.2rem .65rem;font-size:10px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;white-space:nowrap}
  .sp-b-nouveau,.sp-b-pending,.sp-b-actif{background:rgba(201,169,98,.14);color:#e7cf95;border:1px solid rgba(201,169,98,.35)}
  .sp-b-en_cours,.sp-b-en_attente,.sp-b-confirme{background:rgba(56,189,248,.1);color:#7dd3fc;border:1px solid rgba(56,189,248,.3)}
  .sp-b-traite,.sp-b-accepted,.sp-b-replied{background:rgba(52,211,153,.1);color:#6ee7b7;border:1px solid rgba(52,211,153,.3)}
  .sp-b-expired,.sp-b-revoked,.sp-b-cloture,.sp-b-annule,.sp-b-refused{background:rgba(255,255,255,.05);color:rgba(255,255,255,.5);border:1px solid rgba(255,255,255,.12)}
  .sp-table th{padding:.75rem 1rem;text-align:left;font-size:10px;font-weight:600;letter-spacing:.2em;text-transform:uppercase;color:rgba(255,255,255,.4);white-space:nowrap}
  .sp-table td{padding:.85rem 1rem;border-top:1px solid rgba(255,255,255,.06);font-size:14px;vertical-align:top}
  .sp-gold{background:linear-gradient(100deg,#c9a962,#f3e2b3 50%,#c9a962);-webkit-background-clip:text;background-clip:text;color:transparent}
  @keyframes sp-up{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}
  .sp-rise{animation:sp-up .6s cubic-bezier(.2,.7,.2,1) both}
  @media (prefers-reduced-motion:reduce){.sp-rise{animation:none}.sp-btn:hover{transform:none}}
</style>
@php
  $spU = auth()->user();
  if ($spU->isManager()) {
    $spCnt = \App\Support\Inbox::newCounts();
    $spVal = \App\Models\User::whereIn('role', ['client','manager'])->where('account_status','pending')->count();
    $spTab = request()->routeIs('manager.message') ? \App\Support\Inbox::tabOf((string) request()->route('source')) : null;
    $spNav = [
      ['nav_dashboard','manager.dashboard', request()->routeIs('manager.dashboard'), 0],
      ['nav_invitations','manager.invitations', request()->routeIs('manager.invitations*'), $spVal],
      ['nav_messages','manager.inbox', request()->routeIs('manager.inbox','manager.conversation') || $spTab==='messages', $spCnt['messages'] + \App\Models\Conversation::unreadTotal($spU)],
      ['nav_reservations','manager.reservations', request()->routeIs('manager.reservations') || $spTab==='reservations', $spCnt['reservations']],
      ['nav_sav','manager.sav', request()->routeIs('manager.sav') || $spTab==='sav', $spCnt['sav']],
      ['nav_other','manager.other', request()->routeIs('manager.other') || $spTab==='autres', $spCnt['autres']],
      ['nav_clients','manager.clients', request()->routeIs('manager.client*'), 0],
    ];
  } else {
    $spUnread = \App\Support\ClientThreads::unread($spU);
    $spNav = [
      ['cnav_overview','client.dashboard', request()->routeIs('client.dashboard'), 0],
      ['cnav_messages','client.messages', request()->routeIs('client.messages*','client.thread*'), $spUnread],
      ['cnav_opps','client.opportunities', request()->routeIs('client.opportunities'), 0],
      ['cnav_gains','client.gains', request()->routeIs('client.gains'), 0],
    ];
  }
@endphp
<div data-sp-space class="relative min-h-screen overflow-x-clip bg-[#050505] pb-24 pt-28 sm:pt-32">
  <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(60%_40%_at_15%_0%,rgba(201,169,98,.13),transparent_70%),radial-gradient(40%_30%_at_100%_10%,rgba(201,169,98,.07),transparent_70%)]"></div>
  <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="sp-rise flex flex-wrap items-end justify-between gap-4">
      <div class="min-w-0">
        <p class="flex items-center gap-3 text-[10px] font-semibold uppercase tracking-[0.35em] text-kp-gold"><span class="h-px w-8 bg-kp-gold/70"></span>{{ sp_t($spU->isManager() ? 'manager_title' : 'client_title') }}</p>
        <h1 class="mt-3 font-serif text-3xl leading-tight text-white sm:text-4xl">@yield('h1')</h1>
      </div>
      <div class="flex items-center gap-3 text-sm text-white/50">
        <span class="hidden truncate sm:inline">{{ sp_t('hello') }}, <span class="text-white/80">{{ $spU->name }}</span></span>
        @if($spU->isAdmin())<a href="{{ route('admin.dashboard') }}" class="sp-ghost">{{ sp_t('nav_admin') }}</a>@endif
        <a href="{{ route('space.password') }}" data-sp-pw title="{{ sp_t('nav_password') }}" class="sp-ghost {{ request()->routeIs('space.password*') ? '!border-kp-gold !text-kp-gold' : '' }}"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg><span class="hidden sm:inline">{{ sp_t('nav_password') }}</span></a>
        <form method="POST" action="{{ route('account.logout') }}">@csrf<button class="sp-ghost">{{ sp_t('logout') }}</button></form>
      </div>
    </div>
    {{-- Barre de navigation de l'espace : collante sous le header, onglet actif doré, badges = nouveaux éléments ; mobile = défilement horizontal --}}
    <div data-sp-navbar class="sticky z-30 -mx-4 mt-7 px-4 sm:mx-0 sm:px-0" style="top:var(--sp-top,80px)">
      <nav data-sp-nav aria-label="{{ sp_t('nav_menu') }}" class="relative overflow-hidden rounded-2xl border border-kp-gold/20 bg-[#080808]/90 shadow-[0_18px_50px_-24px_rgba(0,0,0,.95)] ring-1 ring-white/[0.04] backdrop-blur-xl">
        <span aria-hidden="true" class="pointer-events-none absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold/70 to-transparent"></span>
        <ul class="flex snap-x gap-1 overflow-x-auto p-1.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
          @foreach($spNav as [$l, $r, $on, $badge])
            <li class="shrink-0 snap-start"><a href="{{ route($r) }}" @if($on) aria-current="page" data-sp-active @endif data-sp-tab="{{ $l }}"
              class="relative flex min-h-11 items-center gap-2 whitespace-nowrap rounded-xl px-3.5 text-[11px] font-semibold uppercase tracking-[0.14em] transition {{ $on ? 'bg-gradient-to-br from-[#f3e2b3] via-kp-gold to-[#a8843f] text-black shadow-[0_8px_24px_-10px_rgba(201,169,98,.8)]' : 'text-white/60 hover:bg-white/[0.05] hover:text-white' }}">
              {{ sp_t($l) }}
              @if($badge)<span data-sp-badge class="inline-flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-[10px] font-bold tracking-normal {{ $on ? 'bg-black text-kp-gold' : 'bg-kp-gold text-black shadow-[0_0_12px_rgba(201,169,98,.6)]' }}">{{ $badge }}</span>@endif
            </a></li>
          @endforeach
        </ul>
      </nav>
    </div>
    <script>(()=>{const h=document.querySelector('header'),s=document.documentElement;const f=()=>{const b=h?Math.max(0,h.getBoundingClientRect().bottom):0;s.style.setProperty('--sp-top',(b+8)+'px')};f();addEventListener('scroll',f,{passive:true});addEventListener('resize',f);
      const a=document.querySelector('[data-sp-active]');if(a){const u=a.closest('ul');u.scrollLeft=a.offsetLeft-(u.clientWidth-a.offsetWidth)/2}})()</script>
    @if(session('ok'))<div data-sp-flash class="sp-rise mt-6 rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-5 py-3 text-sm text-emerald-200">{{ session('ok') }}</div>@endif
    @if($errors->any())<div class="mt-6 rounded-2xl border border-red-400/30 bg-red-500/10 px-5 py-3 text-sm text-red-200">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>@endif
    <div class="mt-8">@yield('space')</div>
  </div>
</div>
@endsection
