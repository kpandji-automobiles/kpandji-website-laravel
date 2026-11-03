{{-- Gabarit de l'espace client (design system) : en-tête de l'espace, onglets, messages flash. Libellés : Contenus › Espaces (sp.*). --}}
@extends('layouts.site')
@section('title')@yield('h1')@endsection
@push('meta')<meta name="robots" content="noindex,nofollow">@endpush
@php
  $client = auth()->user();
  $unreadMessages = \App\Support\ClientThreads::unread($client);
  $tabs = [
    ['cnav_overview', 'client.dashboard', request()->routeIs('client.dashboard'), 0],
    ['cnav_messages', 'client.messages', request()->routeIs('client.messages*', 'client.thread*'), $unreadMessages],
    ['cnav_opps', 'client.opportunities', request()->routeIs('client.opportunities'), 0],
    ['cnav_gains', 'client.gains', request()->routeIs('client.gains'), 0],
  ];
@endphp

@section('content')
<div class="kp-client" data-sp-space>
  <section class="kp-client__head">
    <div class="kp-wrap kp-client__head-inner">
      <div class="kp-section__intro">
        <span class="kp-eyebrow">{{ sp_t('client_title') }}</span>
        <h1 class="heading-l">@yield('h1')</h1>
      </div>
      <div class="kp-client__account">
        <span class="body-s kp-muted">{{ sp_t('hello') }}, <strong>{{ $client->name }}</strong></span>
        <a class="kp-btn kp-btn--secondary kp-btn--sm" href="{{ route('space.password') }}" data-sp-pw>{{ sp_t('nav_password') }}</a>
        <form method="POST" action="{{ route('account.logout') }}">@csrf<button class="kp-btn kp-btn--link kp-site-header__logout">{{ sp_t('logout') }}</button></form>
      </div>
    </div>
    <nav class="kp-wrap kp-client__tabs" aria-label="{{ sp_t('nav_menu') }}">
      @foreach($tabs as [$label, $route, $active, $badge])
        <a class="kp-chip" href="{{ route($route) }}" @if($active) aria-current="page" @endif data-sp-tab="{{ $label }}">
          {{ sp_t($label) }}@if($badge) <span class="kp-chip__count" data-sp-badge>{{ $badge }}</span>@endif
        </a>
      @endforeach
    </nav>
  </section>

  <div class="kp-wrap kp-client__body">
    @if(session('ok'))<div class="kp-form__done" role="status" data-sp-flash><p class="body">{{ session('ok') }}</p></div>@endif
    @if($errors->any())<div class="kp-login__error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    @yield('space')
  </div>
</div>
@endsection
