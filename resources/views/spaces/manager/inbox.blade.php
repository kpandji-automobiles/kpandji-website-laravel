@extends('spaces.layout')
@section('h1', sp_t('tab_'.$tab.'_title'))
@section('space')
<p class="sp-rise -mt-2 mb-5 text-sm text-white/45">{{ sp_t('tab_'.$tab.'_intro') }}</p>
@if($tab==='messages')
  <div class="sp-rise mb-5 flex flex-wrap items-center justify-between gap-3">
    <div data-sp-boxes class="flex max-w-full gap-1 overflow-x-auto rounded-full border border-white/10 bg-black/40 p-1 [scrollbar-width:none]">
      @foreach([[null,'box_forms',0],['sent','box_sent',$boxCounts['sent_unread'] ?? 0],['received','box_received',$boxCounts['received_unread'] ?? 0]] as [$bx,$bl,$bu])
        <a href="{{ route('manager.inbox', $bx ? ['box'=>$bx] : []) }}" data-sp-box="{{ $bx ?? 'forms' }}" class="flex min-h-10 shrink-0 items-center gap-2 whitespace-nowrap rounded-full px-4 text-[10px] font-semibold uppercase tracking-[0.16em] transition {{ $box===$bx ? 'bg-white/10 text-kp-gold ring-1 ring-kp-gold/40' : 'text-white/55 hover:text-white' }}">{{ sp_t($bl) }}
          @if($bu)<span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-kp-gold px-1.5 text-[10px] font-bold tracking-normal text-black">{{ $bu }}</span>@endif</a>
      @endforeach
    </div>
    <button type="button" data-sp-dm-open onclick="window.dispatchEvent(new CustomEvent('sp-dm-open'))" class="sp-btn"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16v12H4z"/><path d="M4 7l8 6 8-6"/></svg>{{ sp_t('dm_button') }}</button>
  </div>
  @include('spaces.manager._compose')
@endif
@if($box)
<div class="sp-card sp-rise overflow-hidden" data-sp-convs="{{ $box }}">
  <p class="px-5 py-3 text-xs text-white/40">{{ $convs->count() }}</p>
  @forelse($convs as $c) @php $other = $box==='sent' ? $c->recipient : $c->sender; @endphp
    <a href="{{ route('manager.conversation', $c) }}" data-sp-conv="{{ $c->id }}" class="flex flex-col gap-2 border-t border-white/[0.06] px-5 py-4 transition hover:bg-white/[0.03] sm:flex-row sm:items-center sm:gap-5">
      <div class="min-w-0 flex-1"><p class="truncate text-sm {{ $c->unread ? 'font-semibold text-white' : 'text-white/80' }}">{{ $c->subject }}</p>
        <p class="mt-0.5 truncate text-xs text-white/45">{{ sp_t($box==='sent' ? 'dm_to' : 'dm_from') }} : <span class="text-white/75">{{ $other?->name ?? '—' }}</span> · {{ $other?->email }}@if($box==='sent' && $c->recipient) · {{ sp_t('role_'.$c->recipient->role) }}@endif</p></div>
      <div class="flex shrink-0 flex-wrap items-center gap-2">
        @if($c->unread)<span class="sp-badge sp-b-nouveau">{{ $c->unread }} {{ sp_t('msg_new') }}</span>@endif
        @if($c->replies_count)<span class="sp-badge sp-b-replied">{{ $c->replies_count }} {{ sp_t('dm_replies') }}</span>@endif
        @if($box==='sent')<span data-sp-readstate class="sp-badge {{ $c->read_at ? 'sp-b-traite' : 'sp-b-expired' }}">{{ $c->read_at ? sp_t('dm_read').' '.$c->read_at->format('d/m H:i') : sp_t('dm_unread') }}</span>@endif
        <span class="w-28 text-right text-xs text-white/35">{{ $c->created_at->format('d/m/Y H:i') }}</span>
      </div>
    </a>
  @empty <p class="border-t border-white/10 px-5 py-6 text-sm text-white/40">{{ sp_t('none') }}</p> @endforelse
</div>
@else
<form method="GET" data-sp-filters class="sp-card sp-rise grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-6 lg:items-end">
  <div class="sm:col-span-2"><label class="sp-label">{{ sp_t('inbox_search') }}</label><input name="q" value="{{ request('q') }}" class="sp-input"></div>
  @if(count($sources) > 1)
  <div><label class="sp-label">{{ sp_t('type') }}</label><select name="source" class="sp-input"><option value="">{{ sp_t('all') }}</option>@foreach($sources as $s)<option value="{{ $s }}" @selected(request('source')===$s)>{{ sp_t('src_'.$s) }}</option>@endforeach</select></div>
  @endif
  <div><label class="sp-label">{{ sp_t('status') }}</label><select name="status" class="sp-input"><option value="">{{ sp_t('all') }}</option>@foreach($statuses as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ sp_t('st_'.$s) }} ({{ $counts[$s] ?? 0 }})</option>@endforeach</select></div>
  <div><label class="sp-label">{{ sp_t('from') }}</label><input type="date" name="from" value="{{ request('from') }}" class="sp-input"></div>
  <div><label class="sp-label">{{ sp_t('to') }}</label><input type="date" name="to" value="{{ request('to') }}" class="sp-input"></div>
  <div><label class="sp-label">{{ sp_t('replied') }}</label><select name="replied" class="sp-input"><option value="">{{ sp_t('all') }}</option><option value="1" @selected(request('replied')==='1')>{{ sp_t('replied') }}</option><option value="0" @selected(request('replied')==='0')>—</option></select></div>
  <div class="flex gap-2 sm:col-span-2 lg:col-span-1"><button class="sp-btn">{{ sp_t('filter') }}</button>@if(request()->query())<a href="{{ url()->current() }}" class="sp-ghost self-center">{{ sp_t('reset') }}</a>@endif</div>
</form>
<div class="sp-card sp-rise mt-5 overflow-hidden" style="animation-delay:.1s" data-sp-list="{{ $tab }}">
  <p class="px-5 py-3 text-xs text-white/40">{{ $items->count() }}</p>
  @forelse($items as $i) @include('spaces.manager._row') @empty <p class="border-t border-white/10 px-5 py-6 text-sm text-white/40">{{ sp_t('none') }}</p> @endforelse
</div>
@endif
@endsection
