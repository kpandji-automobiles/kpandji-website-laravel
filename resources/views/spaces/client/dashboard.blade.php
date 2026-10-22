@extends('spaces.layout')
@section('h1', sp_t('client_title'))
@section('space')
@php $max = max(1, max($months ?: [0])); $en = app()->getLocale()==='en'; @endphp
<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
  @foreach([['kpi_invested',$totals['invested'],true],['kpi_revenue',$totals['revenue'],false],['kpi_net',$totals['net'],false],['kpi_paid',$totals['paid'],false]] as $n => [$l,$v,$g])
    <div data-sp-kpi="{{ $l }}" class="sp-card sp-rise relative overflow-hidden p-5 sm:p-6 {{ $g ? 'border-kp-gold/35' : '' }}" style="animation-delay:{{ .05*$n }}s">
      @if($g)<span aria-hidden="true" class="absolute -right-12 -top-12 h-36 w-36 rounded-full bg-kp-gold/15 blur-2xl"></span>@endif
      <p class="sp-label">{{ sp_t($l) }}</p><p class="mt-2 font-serif text-xl sm:text-3xl {{ $g ? 'sp-gold' : 'text-white' }}">{{ number_format($v,0,',',' ') }}</p><p class="text-[10px] tracking-[0.2em] text-white/35">FCFA</p>
    </div>
  @endforeach
</div>
@if($totals['fees'])<p class="mt-3 text-xs text-white/40">{{ sp_t('kpi_fees') }} : {{ fcfa($totals['fees']) }}</p>@endif

@if($investments->isEmpty())
  <div class="sp-card mt-6 p-8 text-center text-sm text-white/55">{{ sp_t('no_investment') }}</div>
@else
<div class="mt-8 grid gap-6 lg:grid-cols-12">
  <section class="sp-card sp-rise p-6 lg:col-span-7" style="animation-delay:.2s">
    <h2 class="font-serif text-xl text-white">{{ sp_t('chart') }}</h2>
    <div data-sp-chart class="mt-6 flex h-56 items-end gap-1.5 sm:gap-2">
      @foreach($months as $k => $v)
        <div class="group flex h-full min-w-0 flex-1 flex-col items-center justify-end gap-2">
          <div class="relative w-full rounded-t-md bg-gradient-to-t from-[#8a6d2f] via-kp-gold to-[#f3e2b3] transition-[filter] group-hover:brightness-110 {{ $v ? '' : '!bg-none bg-white/[0.06]' }}" style="height:{{ $v ? max(3, round($v/$max*100)) : 2 }}%" title="{{ $k }} : {{ fcfa($v) }}">
            @if($v)<span class="pointer-events-none absolute -top-7 left-1/2 hidden -translate-x-1/2 whitespace-nowrap rounded-md bg-black px-2 py-1 text-[10px] text-kp-gold ring-1 ring-kp-gold/30 group-hover:block">{{ number_format($v,0,',',' ') }}</span>@endif
          </div>
          <span class="text-[9px] text-white/35 sm:text-[10px]">{{ \Illuminate\Support\Carbon::parse($k.'-01')->locale($en?'en':'fr')->isoFormat('MMM') }}</span>
        </div>
      @endforeach
    </div>
  </section>
  <section class="space-y-4 lg:col-span-5">
    <h2 class="font-serif text-xl text-white">{{ sp_t('my_investments') }}</h2>
    @foreach($investments as $iv)
      <a href="{{ route('client.gains', ['investment' => $iv->id]) }}" data-sp-my-inv class="sp-card group block p-5 transition hover:border-kp-gold/40">
        <div class="flex items-center justify-between gap-3"><p class="truncate font-serif text-lg text-white">{{ $iv->label }}</p><span class="sp-badge sp-b-{{ $iv->status }}">{{ sp_t('ist_'.$iv->status) }}</span></div>
        <p class="mt-1 text-xs text-white/40">{{ $iv->program ? sp_t('prog_'.$iv->program) : '' }}{{ $iv->start_date ? ' · '.$iv->start_date->format('d/m/Y') : '' }}</p>
        <div class="mt-4 grid grid-cols-3 gap-2 text-xs"><div><p class="text-white/40">{{ sp_t('invested') }}</p><p class="mt-1 font-semibold text-kp-gold">{{ number_format($iv->amount,0,',',' ') }}</p></div>
          <div><p class="text-white/40">{{ sp_t('kpi_revenue') }}</p><p class="mt-1 font-semibold text-white">{{ number_format($iv->rev ?? 0,0,',',' ') }}</p></div>
          <div><p class="text-white/40">{{ sp_t('kpi_paid') }}</p><p class="mt-1 font-semibold text-white">{{ number_format($iv->paid ?? 0,0,',',' ') }}</p></div></div>
        @if($iv->notes)<p class="mt-3 line-clamp-2 text-xs text-white/45">{{ $iv->notes }}</p>@endif
      </a>
    @endforeach
  </section>
</div>

@endif

<section data-sp-latest class="sp-card sp-rise mt-8 overflow-hidden">
  <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-5"><h2 class="font-serif text-xl text-white">{{ sp_t('msg_latest') }}</h2>
    <div class="flex gap-2"><a href="{{ route('client.messages') }}#ecrire" class="sp-btn !min-h-10">✉ {{ sp_t('msg_compose') }}</a><a href="{{ route('client.messages') }}" class="sp-ghost">{{ sp_t('see_all') }} →</a></div></div>
  @forelse($threads as $t) @include('spaces.client._thread-row') @empty <p class="border-t border-white/[0.06] px-6 py-5 text-sm text-white/40">{{ sp_t('msg_none') }}</p> @endforelse
</section>
@endsection
