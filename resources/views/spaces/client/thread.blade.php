@extends('spaces.client.layout')
@section('h1', $t['subject'])
@section('space')
<a class="kp-btn kp-btn--link kp-account__back" href="{{ route('client.messages') }}">← {{ sp_t('msg_back') }}</a>
<div class="kp-client__panel kp-thread" data-sp-conversation>
  @if($t['notice'])
    <div class="kp-thread__bubble" data-sp-bubble="system">
      <span class="kp-price__label">{{ sp_t('msg_system') }} · {{ $t['notice']->created_at->format('d/m/Y H:i') }}</span>
      <p class="body">{{ $t['notice']->body }}</p>
    </div>
  @else
    @php($opener = $t['opener'])
    <div class="kp-thread__bubble{{ empty($opener['staff']) ? ' is-mine' : '' }}" data-sp-bubble="{{ empty($opener['staff']) ? 'me' : 'advisor' }}">
      <span class="kp-price__label">{{ empty($opener['staff']) ? sp_t('msg_you').' · '.sp_t('msg_your_request') : sp_t('msg_advisor') }} · {{ \Illuminate\Support\Carbon::parse($opener['date'])->format('d/m/Y H:i') }}</span>
      @if(empty($opener['staff']) && $opener['source'] !== 'client')<p class="heading-s">{{ $opener['subject'] }}</p>@endif
      @if($opener['body'])<p class="body">{{ $opener['body'] }}</p>@endif
    </div>
    @foreach($t['messages'] as $message)
      @php($mine = $message->user_id === auth()->id())
      <div class="kp-thread__bubble{{ $mine ? ' is-mine' : '' }}" data-sp-bubble="{{ $mine ? 'me' : 'advisor' }}">
        <span class="kp-price__label">{{ $mine ? sp_t('msg_you') : sp_t('msg_advisor') }} · {{ $message->created_at->format('d/m/Y H:i') }}</span>
        <p class="body">{{ $message->body }}</p>
      </div>
    @endforeach
    @if(in_array($t['source'], ['client', 'dm'], true))
      <form class="kp-form" method="POST" action="{{ route('client.thread.reply', $t['key']) }}" data-sp-followup>
        @csrf
        <x-kp.field name="body" type="textarea" :label="sp_t('msg_followup')" required max="3000" :error="$errors->first('body')" />
        <button class="kp-btn kp-btn--primary">{{ sp_t('req_send') }}</button>
      </form>
    @else
      <a class="kp-btn kp-btn--secondary" href="{{ route('client.messages', ['ecrire' => 1]) }}#ecrire">{{ sp_t('msg_compose') }}</a>
    @endif
  @endif
</div>
@endsection
