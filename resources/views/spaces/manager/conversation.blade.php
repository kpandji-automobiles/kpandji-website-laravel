@extends('spaces.layout')
@section('h1', $c->subject)
@section('space')
@php $me = auth()->user(); @endphp
<a href="{{ route('manager.inbox', ['box' => $c->sender_id === $me->id ? 'sent' : 'received']) }}" class="text-xs uppercase tracking-[0.2em] text-white/45 hover:text-kp-gold">← {{ sp_t('dm_back') }}</a>
<p class="mt-3 text-sm text-white/50">{{ sp_t('dm_from') }} <span class="text-white/80">{{ $c->sender?->name }}</span> → {{ sp_t('dm_to') }} <span class="text-white/80">{{ $c->recipient?->name }}</span> ({{ $c->recipient?->email }})
  · <span data-sp-readstate class="sp-badge {{ $c->read_at ? 'sp-b-traite' : 'sp-b-expired' }}">{{ $c->read_at ? sp_t('dm_read_on').' '.$c->read_at->format('d/m/Y H:i') : sp_t('dm_unread') }}</span></p>
<div data-sp-conversation class="mx-auto mt-6 max-w-3xl space-y-4">
  @foreach(collect([(object)['user_id'=>$c->sender_id,'author'=>$c->sender,'created_at'=>$c->created_at,'body'=>$c->body]])->concat($replies) as $m) @php $mine = $m->user_id === $me->id; @endphp
    <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}" data-sp-bubble="{{ $mine ? 'me' : 'other' }}">
      <div class="max-w-[88%] rounded-3xl p-5 {{ $mine ? 'rounded-br-md border border-kp-gold/30 bg-gradient-to-br from-kp-gold/[0.12] to-kp-gold/[0.03]' : 'rounded-bl-md border border-white/10 bg-white/[0.05]' }}">
        <p class="text-[10px] font-semibold uppercase tracking-[0.22em] {{ $mine ? 'text-kp-gold' : 'text-white/50' }}">{{ $mine ? sp_t('msg_you') : ($m->author->name ?? '—') }} · {{ $m->created_at->format('d/m/Y H:i') }}</p>
        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-white/90">{{ $m->body }}</p></div></div>
  @endforeach
  @if($c->involves($me))
  <form method="POST" action="{{ route('manager.conversation.reply', $c) }}" data-sp-conv-reply class="sp-card mt-6 space-y-3 p-5">@csrf
    <label class="sp-label">{{ sp_t('dm_reply') }}</label><textarea name="body" rows="3" required maxlength="5000" class="sp-input"></textarea><button class="sp-btn">{{ sp_t('reply_send') }}</button></form>
  @endif
</div>
@endsection
