@extends('spaces.layout')
@section('h1', sp_t('clients_title'))
@section('space')
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
  @forelse($clients as $n => $c)
    <a href="{{ route('manager.client', $c) }}" data-sp-client="{{ $c->email }}" class="sp-card sp-rise group block p-6 transition hover:border-kp-gold/40" style="animation-delay:{{ min($n,8)*.04 }}s">
      <div class="flex items-center gap-4"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-kp-gold/40 bg-kp-gold/10 font-serif text-lg text-kp-gold">{{ mb_strtoupper(mb_substr($c->name,0,1)) }}</span>
        <div class="min-w-0"><p class="truncate font-serif text-lg text-white">{{ $c->name }}</p><p class="truncate text-xs text-white/40">{{ $c->email }}</p></div></div>
      <div class="mt-5 flex items-end justify-between"><div><p class="sp-label">{{ sp_t('kpi_invested') }}</p><p class="sp-gold font-serif text-xl">{{ fcfa($c->investments_sum_amount ?? 0) }}</p></div>
        <span class="text-xs text-white/40">{{ $c->investments_count }} · {{ sp_t('open') }} →</span></div>
    </a>
  @empty <p class="text-sm text-white/40">{{ sp_t('none') }} <a class="text-kp-gold" href="{{ route('manager.invitations') }}">{{ sp_t('inv_title') }} →</a></p> @endforelse
</div>
@endsection
