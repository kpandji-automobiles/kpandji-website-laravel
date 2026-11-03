@extends('spaces.client.layout')
@section('h1', sp_t('msg_title'))
@section('space')
<div class="kp-client__panel" x-data="{ compose: @js($errors->any() || request()->has('ecrire')) }" x-init="if (location.hash === '#ecrire') compose = true">
  <div class="kp-section__head">
    <p class="body kp-muted">{{ sp_t('msg_intro') }}</p>
    <div class="kp-home-hero__actions">
      @if($unread)
        <form method="POST" action="{{ route('client.messages.read') }}">@csrf<button class="kp-btn kp-btn--secondary kp-btn--sm" data-sp-readall>{{ sp_t('msg_mark_all') }}</button></form>
      @endif
      <button type="button" class="kp-btn kp-btn--primary kp-btn--sm" data-sp-compose :aria-expanded="compose" aria-controls="ecrire" @click="compose = ! compose; $nextTick(() => compose && $refs.subject.focus())">{{ sp_t('msg_compose') }}</button>
    </div>
  </div>
  <form class="kp-form" id="ecrire" x-show="compose" x-cloak method="POST" action="{{ route('client.request') }}" data-sp-request>
    @csrf
    <h2 class="heading-s">{{ sp_t('msg_compose') }}</h2>
    <p class="body-s kp-muted">{{ sp_t('req_intro') }}</p>
    <div class="kp-form__grid">
      <div class="kp-field{{ $errors->has('subject') ? ' kp-field--error' : '' }}">
        <input class="kp-field__control" id="kp-request-subject" x-ref="subject" name="subject" required maxlength="160" value="{{ old('subject', sp_t('req_default')) }}">
        <label class="kp-field__label" for="kp-request-subject">{{ sp_t('req_subject') }}</label>
      </div>
      <x-kp.field name="investment_id" type="select" :label="sp_t('investment')" placeholder="—" :options="$investments->pluck('label', 'id')->all()" />
      <x-kp.field class="kp-form__wide" name="message" type="textarea" :label="sp_t('req_message')" required max="3000" :error="$errors->first('message')" />
    </div>
    <div class="kp-home-hero__actions">
      <button class="kp-btn kp-btn--gold">{{ sp_t('req_send') }}</button>
      <button type="button" class="kp-btn kp-btn--link kp-site-header__logout" @click="compose = false">{{ sp_t('cancel') }}</button>
    </div>
  </form>
</div>

<section class="kp-client__panel" aria-label="{{ sp_t('msg_title') }}">
  <p class="body-s kp-muted">{{ $threads->total() }} · {{ $unread }} {{ sp_t('msg_unread') }}</p>
  @forelse($threads as $t) @include('spaces.client._thread-row') @empty <p class="body kp-muted">{{ sp_t('msg_none') }}</p> @endforelse
  @if($threads->hasPages())
    <nav class="kp-client__pages" aria-label="Pagination">
      @if($threads->onFirstPage())<span class="kp-muted">← Précédent</span>@else<a href="{{ $threads->previousPageUrl() }}">← Précédent</a>@endif
      <span class="body-s kp-muted">Page {{ $threads->currentPage() }} / {{ $threads->lastPage() }}</span>
      @if($threads->hasMorePages())<a href="{{ $threads->nextPageUrl() }}">Suivant →</a>@else<span class="kp-muted">Suivant →</span>@endif
    </nav>
  @endif
</section>
@endsection
