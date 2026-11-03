{{-- Activation d'un compte invité : choix du mot de passe (design system). Lien signé ; textes : Contenus › Espaces (sp.*). --}}
@extends('layouts.site')
@section('title', sp_t('acc_title'))
@push('meta')<meta name="robots" content="noindex,nofollow">@endpush

@section('content')
<section class="kp-account" data-sp-invite>
  <div class="kp-account__card">
    <span class="kp-eyebrow">KPANDJI</span>
    <h1 class="heading-l">{{ sp_t('acc_title') }}</h1>
    @if($inv)
      <p class="body kp-muted">{{ sp_t('acc_intro') }}</p>
      <form class="kp-form" method="POST" action="{{ request()->fullUrl() }}">
        @csrf
        <div class="kp-budget__result"><strong>{{ $inv->name }}</strong>{{ $inv->email }}</div>
        @if($errors->any())<div class="kp-login__error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        <div class="kp-field{{ $errors->has('password') ? ' kp-field--error' : '' }}">
          <input class="kp-field__control" id="kp-new-password" type="password" name="password" required minlength="8" autocomplete="new-password">
          <label class="kp-field__label" for="kp-new-password">{{ sp_t('pw_new') }}</label>
        </div>
        <div class="kp-field">
          <input class="kp-field__control" id="kp-new-password-confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
          <label class="kp-field__label" for="kp-new-password-confirmation">{{ sp_t('pw_confirm') }}</label>
        </div>
        <button class="kp-btn kp-btn--gold">{{ sp_t('acc_submit') }}</button>
      </form>
    @else
      <p class="body kp-muted" data-sp-invite-invalid>{{ sp_t('acc_invalid') }}</p>
      <a class="kp-btn kp-btn--secondary" href="{{ route('home') }}">{{ sp_t('wait_home') }}</a>
    @endif
  </div>
</section>
@endsection
