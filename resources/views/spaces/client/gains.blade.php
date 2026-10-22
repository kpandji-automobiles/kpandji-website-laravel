@extends('spaces.layout')
@section('h1', sp_t('gains_title'))
@section('space')
@php $max = max(1, max($months ?: [0])); $en = app()->getLocale()==='en'; $fmt = fn($v) => number_format($v,0,',',' '); @endphp
<p class="sp-rise -mt-2 mb-5 text-sm text-white/45">{{ sp_t('gains_intro') }}</p>
<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
  @foreach([['gains_total',$total,true],['type_revenu',$rev,false],['type_versement',$paid,false],['gains_net_note',$rev-$fees,false]] as $n => [$l,$v,$g])
    <div data-sp-gkpi="{{ $l }}" class="sp-card sp-rise relative overflow-hidden p-5 sm:p-6 {{ $g ? 'border-kp-gold/35' : '' }}" style="animation-delay:{{ .05*$n }}s">
      @if($g)<span aria-hidden="true" class="absolute -right-12 -top-12 h-36 w-36 rounded-full bg-kp-gold/15 blur-2xl"></span>@endif
      <p class="sp-label">{{ sp_t($l) }}</p><p class="mt-2 font-serif text-xl sm:text-3xl {{ $g ? 'sp-gold' : 'text-white' }}">{{ $fmt($v) }}</p><p class="text-[10px] tracking-[0.2em] text-white/35">FCFA</p>
    </div>
  @endforeach
</div>
@if($fees)<p class="mt-3 text-xs text-white/40">{{ sp_t('kpi_fees') }} : {{ fcfa($fees) }} ({{ sp_t('gains_net_note') }} = {{ sp_t('type_revenu') }} − {{ sp_t('kpi_fees') }})</p>@endif

<form method="GET" data-sp-gfilters class="sp-card sp-rise mt-6 grid grid-cols-2 gap-3 p-4 lg:grid-cols-[1fr_1fr_1fr_1fr_auto] lg:items-end">
  <div><label class="sp-label">{{ sp_t('from') }}</label><input type="date" name="from" value="{{ request('from') }}" class="sp-input"></div>
  <div><label class="sp-label">{{ sp_t('to') }}</label><input type="date" name="to" value="{{ request('to') }}" class="sp-input"></div>
  <div><label class="sp-label">{{ sp_t('type') }}</label><select name="type" class="sp-input"><option value="">{{ sp_t('all') }}</option>@foreach(['revenu','versement'] as $t)<option value="{{ $t }}" @selected(request('type')===$t)>{{ sp_t('type_'.$t) }}</option>@endforeach</select></div>
  <div><label class="sp-label">{{ sp_t('investment') }}</label><select name="investment" class="sp-input"><option value="">{{ sp_t('all') }}</option>@foreach($investments as $iv)<option value="{{ $iv->id }}" @selected(request('investment')==$iv->id)>{{ $iv->label }}</option>@endforeach</select></div>
  <div class="col-span-2 flex flex-wrap gap-2 lg:col-span-1"><button class="sp-btn">{{ sp_t('filter') }}</button>@if(request()->query())<a href="{{ route('client.gains') }}" class="sp-ghost self-center">{{ sp_t('reset') }}</a>@endif</div>
</form>

<div class="mt-6 grid gap-6 lg:grid-cols-12">
  <section class="sp-card sp-rise p-6 lg:col-span-8">
    <div class="flex items-center justify-between gap-3"><h2 class="font-serif text-xl text-white">{{ sp_t('gains_chart') }}</h2><a href="{{ route('client.csv', request()->only('from','to','type','investment')) }}" data-sp-csv class="sp-ghost">↓ {{ sp_t('export_csv') }}</a></div>
    <div data-sp-chart class="mt-6 flex h-56 items-end gap-1.5 sm:gap-2">
      @foreach($months as $k => $v)
        <div class="group flex h-full min-w-0 flex-1 flex-col items-center justify-end gap-2">
          <div class="relative w-full rounded-t-md {{ $v ? 'bg-gradient-to-t from-[#8a6d2f] via-kp-gold to-[#f3e2b3]' : 'bg-white/[0.06]' }}" style="height:{{ $v ? max(3, round($v/$max*100)) : 2 }}%" title="{{ $k }} : {{ fcfa($v) }}">
            @if($v)<span class="pointer-events-none absolute -top-7 left-1/2 hidden -translate-x-1/2 whitespace-nowrap rounded-md bg-black px-2 py-1 text-[10px] text-kp-gold ring-1 ring-kp-gold/30 group-hover:block">{{ $fmt($v) }}</span>@endif
          </div>
          <span class="text-[9px] text-white/35 sm:text-[10px]">{{ \Illuminate\Support\Carbon::parse($k.'-01')->locale($en?'en':'fr')->isoFormat('MMM') }}</span>
        </div>
      @endforeach
    </div>
  </section>
  <section class="sp-card sp-rise overflow-hidden lg:col-span-4">
    <h2 class="px-6 pt-6 font-serif text-xl text-white">{{ sp_t('per_year') }}</h2>
    <table data-sp-years class="sp-table mt-3 w-full"><thead><tr><th>{{ sp_t('year') }}</th><th class="!text-right">{{ sp_t('total') }}</th></tr></thead><tbody>
      @forelse($byYear as $y => $t)<tr><td class="text-white/80">{{ $y }}</td><td class="text-right font-semibold text-kp-gold">{{ $fmt($t['total']) }}</td></tr>@empty<tr><td colspan="2" class="text-white/40">{{ sp_t('none') }}</td></tr>@endforelse
    </tbody></table>
  </section>
</div>

<section class="sp-card sp-rise mt-6 overflow-hidden">
  <h2 class="px-6 pt-6 font-serif text-xl text-white">{{ sp_t('per_month') }}</h2>
  <div class="mt-3 overflow-x-auto"><table data-sp-months class="sp-table w-full min-w-[480px]"><thead><tr><th>{{ sp_t('month') }}</th><th class="!text-right">{{ sp_t('type_revenu') }}</th><th class="!text-right">{{ sp_t('type_versement') }}</th><th class="!text-right">{{ sp_t('total') }}</th></tr></thead><tbody>
    @forelse($byMonth as $k => $t)<tr><td class="text-white/80">{{ \Illuminate\Support\Carbon::parse($k.'-01')->locale($en?'en':'fr')->isoFormat('MMMM YYYY') }}</td><td class="text-right text-white/70">{{ $fmt($t['revenu']) }}</td><td class="text-right text-white/70">{{ $fmt($t['versement']) }}</td><td class="text-right font-semibold text-white">{{ $fmt($t['total']) }}</td></tr>@empty<tr><td colspan="4" class="text-white/40">{{ sp_t('none') }}</td></tr>@endforelse
  </tbody></table></div>
</section>

<section class="sp-card sp-rise mt-6 overflow-hidden">
  <h2 class="px-6 pt-6 font-serif text-xl text-white">{{ sp_t('history') }}</h2>
  <div class="mt-3 overflow-x-auto"><table data-sp-history class="sp-table w-full min-w-[560px]"><thead><tr><th>{{ sp_t('date') }}</th><th>{{ sp_t('investment') }}</th><th>{{ sp_t('type') }}</th><th>{{ sp_t('description') }}</th><th class="!text-right">FCFA</th></tr></thead><tbody>
    @forelse($entries as $e)<tr><td class="whitespace-nowrap text-white/70">{{ $e->entry_date->format('d/m/Y') }}</td><td class="text-white/80">{{ $e->investment->label }}</td>
      <td><span class="text-xs uppercase tracking-[0.14em] {{ $e->type==='versement' ? 'text-sky-300' : 'text-kp-gold' }}">{{ sp_t('type_'.$e->type) }}</span></td><td class="text-white/55">{{ $e->description }}</td><td class="whitespace-nowrap text-right font-semibold text-white">{{ $fmt($e->amount) }}</td></tr>
    @empty<tr><td colspan="5" class="text-white/40">{{ sp_t('none') }}</td></tr>@endforelse
  </tbody></table></div>
</section>
@endsection
