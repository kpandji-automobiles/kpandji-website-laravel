@extends('spaces.layout')
@section('h1', sp_t('nav_dashboard'))
@section('space')
<div class="grid gap-4 sm:grid-cols-3">
  @foreach([['kpi_new_msgs',$open,'manager.inbox'],['kpi_pending_inv',$pending,'manager.invitations'],['kpi_clients',$clients,'manager.clients']] as $n => [$l,$v,$r])
    <a href="{{ route($r) }}" class="sp-card sp-rise group relative overflow-hidden p-6" style="animation-delay:{{ .06*$n }}s">
      <span class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-kp-gold/10 blur-2xl transition group-hover:bg-kp-gold/20"></span>
      <p class="sp-label">{{ sp_t($l) }}</p><p class="sp-gold mt-2 font-serif text-5xl">{{ $v }}</p>
    </a>
  @endforeach
</div>
<div class="sp-card sp-rise mt-6 overflow-hidden" style="animation-delay:.2s">
  <div class="flex items-center justify-between px-5 py-4"><h2 class="font-serif text-xl text-white">{{ sp_t('latest_msgs') }}</h2><a href="{{ route('manager.inbox') }}" class="sp-ghost">{{ sp_t('nav_inbox') }} →</a></div>
  @forelse($latest as $i) @include('spaces.manager._row') @empty <p class="border-t border-white/10 px-5 py-6 text-sm text-white/40">{{ sp_t('none') }}</p> @endforelse
</div>
@endsection
