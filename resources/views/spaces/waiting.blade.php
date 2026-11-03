{{-- Compte invité pas encore validé (ou refusé) par un manager (design system). Textes : Contenus › Espaces (sp.*). --}}
@extends('layouts.site')
@php($refused = $u->account_status === 'refused')
@section('title', sp_t($refused ? 'wait_refused_title' : 'wait_title'))
@push('meta')<meta name="robots" content="noindex,nofollow">@endpush

@section('content')
<section class="kp-account" data-sp-waiting="{{ $u->account_status }}">
  <div class="kp-account__card">
    <span class="kp-status {{ $refused ? 'kp-status--refused' : 'kp-status--wait' }}">{{ $refused ? 'Accès non activé' : 'Compte en attente' }}</span>
    <h1 class="heading-l">{{ sp_t($refused ? 'wait_refused_title' : 'wait_title') }}</h1>
    <p class="body kp-muted">{{ sp_t($refused ? 'wait_refused_text' : 'wait_text') }}</p>
    <p class="body-s kp-muted">{{ $u->name }} · {{ $u->email }}</p>
    <div class="kp-home-hero__actions">
      @unless($refused)<a class="kp-btn kp-btn--primary" href="{{ route('space.waiting') }}">{{ sp_t('wait_refresh') }}</a>@endunless
      <a class="kp-btn kp-btn--secondary" href="{{ route('home') }}">{{ sp_t('wait_home') }}</a>
      <form method="POST" action="{{ route('account.logout') }}">@csrf<button class="kp-btn kp-btn--link kp-site-header__logout">{{ sp_t('logout') }}</button></form>
    </div>
  </div>
</section>
@endsection
