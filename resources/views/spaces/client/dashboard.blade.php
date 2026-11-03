@extends('spaces.client.layout')
@section('h1', sp_t('client_title'))
@section('space')
@php($fmt = fn ($value) => number_format($value, 0, ',', ' '))
<div class="kp-client__kpis">
  @foreach([['kpi_invested', $totals['invested']], ['kpi_revenue', $totals['revenue']], ['kpi_net', $totals['net']], ['kpi_paid', $totals['paid']]] as [$label, $value])
    <div class="kp-kpi" data-sp-kpi="{{ $label }}"><span class="kp-kpi__label">{{ sp_t($label) }}</span><span class="kp-kpi__value">{{ $fmt($value) }}</span><span class="body-s kp-muted">FCFA</span></div>
  @endforeach
</div>
@if($totals['fees'])<p class="body-s kp-muted">{{ sp_t('kpi_fees') }} : {{ fcfa($totals['fees']) }}</p>@endif

@if($investments->isEmpty())
  <div class="kp-catalog__empty"><p class="body">{{ sp_t('no_investment') }}</p></div>
@else
<div class="kp-client__split">
  <section class="kp-client__panel">
    <h2 class="heading-s">{{ sp_t('chart') }}</h2>
    @include('spaces.client._chart', ['months' => $months])
  </section>
  <section class="kp-client__panel">
    <h2 class="heading-s">{{ sp_t('my_investments') }}</h2>
    @foreach($investments as $investment)
      <a class="kp-client__investment" href="{{ route('client.gains', ['investment' => $investment->id]) }}" data-sp-my-inv>
        <span class="kp-client__investment-head"><strong>{{ $investment->label }}</strong><span class="kp-status kp-status--{{ $investment->status === 'actif' ? 'done' : 'neutral' }}">{{ sp_t('ist_'.$investment->status) }}</span></span>
        <span class="body-s kp-muted">{{ $investment->program ? sp_t('prog_'.$investment->program) : '' }}{{ $investment->start_date ? ' · '.$investment->start_date->format('d/m/Y') : '' }}</span>
        <span class="kp-client__investment-figures">
          <span><span class="kp-price__label">{{ sp_t('invested') }}</span>{{ $fmt($investment->amount) }}</span>
          <span><span class="kp-price__label">{{ sp_t('kpi_revenue') }}</span>{{ $fmt($investment->rev ?? 0) }}</span>
          <span><span class="kp-price__label">{{ sp_t('kpi_paid') }}</span>{{ $fmt($investment->paid ?? 0) }}</span>
        </span>
        @if($investment->notes)<span class="body-s kp-muted">{{ $investment->notes }}</span>@endif
      </a>
    @endforeach
  </section>
</div>
@endif

<section class="kp-client__panel" data-sp-latest>
  <div class="kp-section__head">
    <h2 class="heading-s">{{ sp_t('msg_latest') }}</h2>
    <div class="kp-home-hero__actions">
      <a class="kp-btn kp-btn--secondary kp-btn--sm" href="{{ route('client.messages', ['ecrire' => 1]) }}#ecrire">{{ sp_t('msg_compose') }}</a>
      <a class="kp-btn kp-btn--link" href="{{ route('client.messages') }}">{{ sp_t('see_all') }}</a>
    </div>
  </div>
  @forelse($threads as $t) @include('spaces.client._thread-row') @empty <p class="body kp-muted">{{ sp_t('msg_none') }}</p> @endforelse
</section>
@endsection
