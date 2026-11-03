{{-- Changement de mot de passe des clients et managers (design system). Textes : Contenus › Espaces (sp.*). --}}
@extends('layouts.site')
@section('title', sp_t('pw_title'))
@push('meta')<meta name="robots" content="noindex,nofollow">@endpush

@section('content')
<section class="kp-account" data-sp-password>
  <div class="kp-account__card">
    <a class="kp-btn kp-btn--link kp-account__back" href="{{ auth()->user()->workspaceUrl() ?? route('home') }}">← Mon espace</a>
    <h1 class="heading-l">{{ sp_t('pw_title') }}</h1>
    @if(session('ok'))<div class="kp-form__done" role="status"><p class="heading-s">{{ session('ok') }}</p></div>@endif
    <form class="kp-form" method="POST" action="{{ route('space.password.update') }}">
      @csrf @method('PUT')
      @if($errors->any())<div class="kp-login__error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
      <div class="kp-field{{ $errors->has('current') ? ' kp-field--error' : '' }}">
        <input class="kp-field__control" id="kp-current-password" type="password" name="current" required autocomplete="current-password">
        <label class="kp-field__label" for="kp-current-password">{{ sp_t('pw_current') }}</label>
      </div>
      <div class="kp-field{{ $errors->has('password') ? ' kp-field--error' : '' }}">
        <input class="kp-field__control" id="kp-new-password" type="password" name="password" required minlength="8" autocomplete="new-password">
        <label class="kp-field__label" for="kp-new-password">{{ sp_t('pw_new') }}</label>
      </div>
      <div class="kp-field">
        <input class="kp-field__control" id="kp-new-password-confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
        <label class="kp-field__label" for="kp-new-password-confirmation">{{ sp_t('pw_confirm') }}</label>
      </div>
      <button class="kp-btn kp-btn--primary">{{ sp_t('save') }}</button>
    </form>
  </div>
</section>
@endsection
