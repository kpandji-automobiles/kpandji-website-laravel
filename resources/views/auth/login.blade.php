{{-- Connexion clients, managers et administrateurs (design system). Envoi vers AccountAuthController@login ; textes : Contenus › Header (header.login_*). --}}
@extends('layouts.site')
@section('title', content('header.login_title'))
@push('meta')<meta name="robots" content="noindex,nofollow">@endpush
@php($loginError = session('errors')?->getBag('kpLogin')->first('login'))

@section('content')
<div class="kp-auth" data-kp-login-page>
  <div class="kp-auth__visual">
    @if(filled(content('header.login_page_image')))<img src="{{ cms_asset(content('header.login_page_image')) }}" alt="">@endif
    <div class="kp-auth__visual-text">
      <span class="kp-eyebrow kp-on-stage-gold">{{ content('header.login_page_eyebrow') }}</span>
      <p class="display-l">{{ content('header.login_page_title') }}</p>
    </div>
  </div>
  <div class="kp-auth__panel">
    <form class="kp-form" method="POST" action="{{ route('account.login') }}">
      @csrf
      <span class="kp-eyebrow">{{ content('header.login_kicker') }}</span>
      <h1 class="heading-l">{{ content('header.login_title') }}</h1>
      <p class="body kp-muted">{{ content('header.login_subtitle') }}</p>
      @if($loginError)<p class="kp-login__error" role="alert">{{ $loginError }}</p>@endif
      <x-kp.field name="email" type="email" :label="content('header.login_email')" required max="190" :class="$loginError ? 'kp-field--error' : ''" />
      <div class="kp-field{{ $loginError ? ' kp-field--error' : '' }}">
        <input class="kp-field__control" id="kp-password" type="password" name="password" required autocomplete="current-password">
        <label class="kp-field__label" for="kp-password">{{ content('header.login_password') }}</label>
      </div>
      <label class="kp-login__remember body-s"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> {{ content('header.login_remember') }}</label>
      <button class="kp-btn kp-btn--primary">{{ content('header.login_submit') }}</button>
      @if(filled(content('header.login_page_note')))
        <p class="kp-budget__result">{{ content('header.login_page_note') }} <a href="{{ route('opportunities') }}">Découvrir les opportunités</a></p>
      @endif
    </form>
  </div>
</div>
@endsection
