@extends('spaces.layout')
@section('h1', $m['name'])
@section('space')
@php
  $raw = collect($m['raw'])->except(['id','created_at','updated_at','handled_at','cv_path','ip','user_id','investment_id','u_name','u_email','u_phone','locale'])->filter(fn($v)=>$v!==null&&$v!=='');
  $mailLbl = fn($s) => sp_t('mail_'.($s ?: 'none'));
@endphp
@php $tabRoute = ['messages'=>'manager.inbox','reservations'=>'manager.reservations','sav'=>'manager.sav','autres'=>'manager.other'][\App\Support\Inbox::tabOf($m['source'])]; $sts = \App\Support\Inbox::statuses($m['source']); @endphp
<a href="{{ route($tabRoute) }}" class="text-xs uppercase tracking-[0.2em] text-white/45 hover:text-kp-gold">← {{ sp_t('back_inbox') }}</a>
<div class="mt-4 grid gap-6 lg:grid-cols-12">
  <div class="space-y-6 lg:col-span-7">
    <article class="sp-card sp-rise p-6 sm:p-8">
      <div class="flex flex-wrap items-center gap-2"><span class="sp-badge sp-b-pending">{{ sp_t('src_'.$m['source']) }}</span><span class="sp-badge sp-b-{{ $m['status'] }}">{{ sp_t('st_'.$m['status']) }}</span>@if($replies->count())<span class="sp-badge sp-b-replied">✓ {{ sp_t('replied') }}</span>@endif
        <span class="ml-auto text-xs text-white/40">{{ \Illuminate\Support\Carbon::parse($m['date'])->format('d/m/Y H:i') }}</span></div>
      <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
        <div><dt class="sp-label">{{ sp_t('msg_from') }}</dt><dd class="text-white">{{ $m['name'] }}</dd></div>
        <div><dt class="sp-label">{{ sp_t('msg_subject') }}</dt><dd class="text-white">{{ $m['subject'] }}</dd></div>
        @if($m['email'])<div><dt class="sp-label">{{ sp_t('email') }}</dt><dd><a href="mailto:{{ $m['email'] }}" class="break-all text-kp-gold">{{ $m['email'] }}</a></dd></div>@endif
        @if($m['source']==='essai')<div><dt class="sp-label">{{ sp_t('model') }}</dt><dd class="text-white">{{ $m['model'] ?: '—' }}</dd></div>
          <div><dt class="sp-label">{{ sp_t('wish_date') }}</dt><dd class="text-white">{{ $m['wish_date'] ? \Illuminate\Support\Carbon::parse($m['wish_date'])->format('d/m/Y') : '—' }}</dd></div>
          <div><dt class="sp-label">{{ sp_t('type') }}</dt><dd class="text-white">{{ sp_t('kind_'.($m['kind'] ?? 'essai')) }}</dd></div>@endif
        @if($m['source']==='sav' && ($m['location'] ?? null))<div><dt class="sp-label">{{ sp_t('location') }}</dt><dd class="text-white">{{ $m['location'] }}</dd></div>@endif
        <div><dt class="sp-label">{{ sp_t('received') }}</dt><dd class="text-white">{{ \Illuminate\Support\Carbon::parse($m['date'])->format('d/m/Y H:i') }}</dd></div>
        @if($m['phone'])<div><dt class="sp-label">{{ sp_t('phone') }}</dt><dd><a href="tel:{{ $m['phone'] }}" class="text-kp-gold">{{ $m['phone'] }}</a></dd></div>@endif
      </dl>
      @if($m['body'])<div class="mt-6 whitespace-pre-line rounded-2xl border border-white/10 bg-black/30 p-5 text-sm leading-relaxed text-white/80">{{ $m['body'] }}</div>@endif
      <details class="mt-5 text-sm"><summary class="cursor-pointer text-xs uppercase tracking-[0.2em] text-white/40">{{ sp_t('msg_details') }}</summary>
        <dl class="mt-3 grid gap-2 sm:grid-cols-2">@foreach($raw as $k=>$v)<div class="min-w-0"><dt class="text-[10px] uppercase tracking-[0.18em] text-white/35">{{ $k }}</dt><dd class="break-words text-white/70">{{ \Illuminate\Support\Str::limit((string)$v, 400) }}</dd></div>@endforeach</dl></details>
      @if($m['source']==='emplois' && auth()->user()->isAdmin())<a href="{{ route('admin.emplois.applications.show', $m['id']) }}" class="sp-ghost mt-4">CV →</a>@endif
    </article>
    <section class="sp-card sp-rise p-6 sm:p-8" style="animation-delay:.08s">
      <h2 class="font-serif text-xl text-white">{{ sp_t('thread') }}</h2>
      <div data-sp-thread class="mt-4 space-y-3">
        @forelse($replies as $rp)
          <div class="rounded-2xl border border-kp-gold/15 bg-kp-gold/[0.04] p-4"><div class="flex flex-wrap items-center gap-2 text-xs text-white/45"><span class="text-kp-gold">{{ $rp->author->name ?? '—' }}</span><span>· {{ $rp->created_at->format('d/m/Y H:i') }}</span><span class="ml-auto">{{ $rp->to_email ?: '—' }} · {{ $mailLbl($rp->mail_status) }}</span></div>
            <p class="mt-2 whitespace-pre-line text-sm text-white/85">{{ $rp->body }}</p></div>
        @empty <p class="text-sm text-white/40">{{ sp_t('none') }}</p> @endforelse
      </div>
    </section>
  </div>
  <aside class="space-y-6 lg:col-span-5">
    <form method="POST" action="{{ route('manager.message.status', [$m['source'], $m['id']]) }}" class="sp-card sp-rise p-6" style="animation-delay:.1s">@csrf @method('PATCH')
      <p class="sp-label">{{ sp_t('status') }}</p>
      <div class="mt-2 grid {{ count($sts)===4 ? 'grid-cols-2 sm:grid-cols-4 lg:grid-cols-2' : 'grid-cols-3' }} gap-2">@foreach($sts as $s)<button name="status" value="{{ $s }}" data-sp-status="{{ $s }}" class="min-h-11 rounded-xl border text-[10px] font-semibold uppercase tracking-[0.14em] transition {{ $m['status']===$s ? 'border-kp-gold bg-kp-gold text-black' : 'border-white/10 text-white/60 hover:border-kp-gold/50' }}">{{ sp_t('st_'.$s) }}</button>@endforeach</div>
    </form>
    <form method="POST" action="{{ route('manager.message.reply', [$m['source'], $m['id']]) }}" data-sp-reply class="sp-card sp-rise space-y-4 p-6" style="animation-delay:.14s">@csrf
      <h2 class="font-serif text-xl text-white">{{ sp_t('reply') }}</h2>
      @if($m['email'])<p class="text-xs text-white/45">→ {{ $m['email'] }}</p>@else<p class="rounded-xl border border-amber-400/25 bg-amber-400/10 px-3 py-2 text-xs text-amber-100">{{ sp_t('reply_no_email') }}</p>@endif
      <textarea name="body" rows="7" required maxlength="5000" class="sp-input">{{ old('body') }}</textarea>
      <button class="sp-btn w-full">{{ sp_t('reply_send') }}</button>
      @if(\App\Support\SpaceMail::isFake())<p class="text-[11px] text-white/35">{{ sp_t('email') }} : {{ sp_t('mail_logged') }}</p>@endif
    </form>
  </aside>
</div>
@endsection
