@extends('spaces.layout')
@section('h1', sp_t('pw_title'))
@section('space')
<form method="POST" action="{{ route('space.password.update') }}" class="sp-card sp-rise max-w-xl space-y-5 p-6 sm:p-8">@csrf @method('PUT')
  <div><label class="sp-label" for="pw0">{{ sp_t('pw_current') }}</label><input id="pw0" name="current" type="password" required autocomplete="current-password" class="sp-input"></div>
  <div><label class="sp-label" for="pw1">{{ sp_t('pw_new') }}</label><input id="pw1" name="password" type="password" required minlength="8" autocomplete="new-password" class="sp-input"></div>
  <div><label class="sp-label" for="pw2">{{ sp_t('pw_confirm') }}</label><input id="pw2" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" class="sp-input"></div>
  <button class="sp-btn">{{ sp_t('save') }}</button>
</form>
@endsection
