@extends('spaces.layout')
@section('h1', $t['subject'])
@section('space')
<a href="{{ route('client.messages') }}" class="text-xs uppercase tracking-[0.2em] text-white/45 hover:text-kp-gold">← {{ sp_t('msg_back') }}</a>
<div data-sp-conversation class="mx-auto mt-5 max-w-3xl space-y-4">
  @if($t['notice'])
    <div class="sp-card sp-rise border-kp-gold/30 p-6"><p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-kp-gold">{{ sp_t('msg_system') }} · {{ $t['notice']->created_at->format('d/m/Y H:i') }}</p>
      <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-white/85">{{ $t['notice']->body }}</p></div>
  @else
    @php $o = $t['opener']; @endphp
    @if(!empty($o['staff']))
    <div class="flex justify-start" data-sp-bubble="advisor"><div class="sp-rise max-w-[88%] rounded-3xl rounded-bl-md border border-kp-gold/30 bg-gradient-to-br from-kp-gold/[0.12] to-kp-gold/[0.03] p-5">
      <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-kp-gold">{{ sp_t('msg_advisor') }} · {{ \Illuminate\Support\Carbon::parse($o['date'])->format('d/m/Y H:i') }}</p>
      <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-white/90">{{ $o['body'] }}</p></div></div>
    @else
    {{-- Message d'origine (du client) --}}
    <div class="flex justify-end"><div class="sp-rise max-w-[88%] rounded-3xl rounded-br-md border border-white/10 bg-white/[0.05] p-5">
      <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-white/45">{{ sp_t('msg_you') }} · {{ sp_t('msg_your_request') }} · {{ \Illuminate\Support\Carbon::parse($o['date'])->format('d/m/Y H:i') }}</p>
      @if($o['source']!=='client')<p class="mt-2 text-sm text-white">{{ $o['subject'] }}</p>@endif
      @if($o['body'])<p class="mt-2 whitespace-pre-line text-sm text-white/80">{{ $o['body'] }}</p>@endif
    </div></div>
    @endif
    @foreach($t['messages'] as $m) @php $mine = $m->user_id === auth()->id(); @endphp
      <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}" data-sp-bubble="{{ $mine ? 'me' : 'advisor' }}">
        <div class="sp-rise max-w-[88%] rounded-3xl p-5 {{ $mine ? 'rounded-br-md border border-white/10 bg-white/[0.05]' : 'rounded-bl-md border border-kp-gold/30 bg-gradient-to-br from-kp-gold/[0.12] to-kp-gold/[0.03]' }}">
          <p class="text-[10px] font-semibold uppercase tracking-[0.22em] {{ $mine ? 'text-white/45' : 'text-kp-gold' }}">{{ $mine ? sp_t('msg_you') : sp_t('msg_advisor') }} · {{ $m->created_at->format('d/m/Y H:i') }}</p>
          <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-white/90">{{ $m->body }}</p>
        </div></div>
    @endforeach
    @if(in_array($t['source'], ['client','dm']))
      <form method="POST" action="{{ route('client.thread.reply', $t['key']) }}" data-sp-followup class="sp-card mt-6 space-y-3 p-5">@csrf
        <label class="sp-label">{{ sp_t('msg_followup') }}</label><textarea name="body" rows="3" required maxlength="3000" class="sp-input"></textarea><button class="sp-btn">{{ sp_t('req_send') }}</button></form>
    @else
      <a href="{{ route('client.messages') }}#ecrire" class="sp-ghost mt-4">✉ {{ sp_t('msg_compose') }}</a>
    @endif
  @endif
</div>
@endsection
