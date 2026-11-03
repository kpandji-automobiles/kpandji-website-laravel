@extends('spaces.client.layout')
@section('h1', sp_t('gains_title'))
@section('space')
@php($fmt = fn ($value) => number_format($value, 0, ',', ' '))
<p class="body kp-muted">{{ sp_t('gains_intro') }}</p>
<div class="kp-client__kpis">
  @foreach([['gains_total', $total], ['type_revenu', $rev], ['type_versement', $paid], ['gains_net_note', $rev - $fees]] as [$label, $value])
    <div class="kp-kpi" data-sp-gkpi="{{ $label }}"><span class="kp-kpi__label">{{ sp_t($label) }}</span><span class="kp-kpi__value">{{ $fmt($value) }}</span><span class="body-s kp-muted">FCFA</span></div>
  @endforeach
</div>
@if($fees)<p class="body-s kp-muted">{{ sp_t('kpi_fees') }} : {{ fcfa($fees) }} ({{ sp_t('gains_net_note') }} = {{ sp_t('type_revenu') }} − {{ sp_t('kpi_fees') }})</p>@endif

<form class="kp-client__filters" method="GET" data-sp-gfilters>
  <x-kp.field name="from" type="date" :label="sp_t('from')" :value="request('from')" />
  <x-kp.field name="to" type="date" :label="sp_t('to')" :value="request('to')" />
  <x-kp.field name="type" type="select" :label="sp_t('type')" :placeholder="sp_t('all')" :value="request('type')" :options="['revenu' => sp_t('type_revenu'), 'versement' => sp_t('type_versement')]" />
  <x-kp.field name="investment" type="select" :label="sp_t('investment')" :placeholder="sp_t('all')" :value="request('investment')" :options="$investments->pluck('label', 'id')->all()" />
  <div class="kp-home-hero__actions">
    <button class="kp-btn kp-btn--primary kp-btn--sm">{{ sp_t('filter') }}</button>
    @if(request()->query())<a class="kp-btn kp-btn--link" href="{{ route('client.gains') }}">{{ sp_t('reset') }}</a>@endif
  </div>
</form>

<div class="kp-client__split">
  <section class="kp-client__panel">
    <div class="kp-section__head">
      <h2 class="heading-s">{{ sp_t('gains_chart') }}</h2>
      <a class="kp-btn kp-btn--secondary kp-btn--sm" href="{{ route('client.csv', request()->only('from', 'to', 'type', 'investment')) }}" data-sp-csv>{{ sp_t('export_csv') }}</a>
    </div>
    @include('spaces.client._chart', ['months' => $months])
  </section>
  <section class="kp-client__panel">
    <h2 class="heading-s">{{ sp_t('per_year') }}</h2>
    <table class="kp-table" data-sp-years>
      <thead><tr><th>{{ sp_t('year') }}</th><th class="kp-table__num">{{ sp_t('total') }}</th></tr></thead>
      <tbody>@forelse($byYear as $year => $row)<tr><td>{{ $year }}</td><td class="kp-table__num">{{ $fmt($row['total']) }}</td></tr>@empty<tr><td colspan="2">{{ sp_t('none') }}</td></tr>@endforelse</tbody>
    </table>
  </section>
</div>

<section class="kp-client__panel">
  <h2 class="heading-s">{{ sp_t('per_month') }}</h2>
  <div class="kp-table__scroll">
    <table class="kp-table" data-sp-months>
      <thead><tr><th>{{ sp_t('month') }}</th><th class="kp-table__num">{{ sp_t('type_revenu') }}</th><th class="kp-table__num">{{ sp_t('type_versement') }}</th><th class="kp-table__num">{{ sp_t('total') }}</th></tr></thead>
      <tbody>
        @forelse($byMonth as $month => $row)
          <tr><td>{{ \Illuminate\Support\Carbon::parse($month.'-01')->locale('fr')->isoFormat('MMMM YYYY') }}</td><td class="kp-table__num">{{ $fmt($row['revenu']) }}</td><td class="kp-table__num">{{ $fmt($row['versement']) }}</td><td class="kp-table__num">{{ $fmt($row['total']) }}</td></tr>
        @empty<tr><td colspan="4">{{ sp_t('none') }}</td></tr>@endforelse
      </tbody>
    </table>
  </div>
</section>

<section class="kp-client__panel">
  <h2 class="heading-s">{{ sp_t('history') }}</h2>
  <div class="kp-table__scroll">
    <table class="kp-table" data-sp-history>
      <thead><tr><th>{{ sp_t('date') }}</th><th>{{ sp_t('investment') }}</th><th>{{ sp_t('type') }}</th><th>{{ sp_t('description') }}</th><th class="kp-table__num">FCFA</th></tr></thead>
      <tbody>
        @forelse($entries as $entry)
          <tr><td>{{ $entry->entry_date->format('d/m/Y') }}</td><td>{{ $entry->investment->label }}</td><td><span class="kp-status kp-status--{{ $entry->type === 'revenu' ? 'done' : 'new' }}">{{ sp_t('type_'.$entry->type) }}</span></td><td>{{ $entry->description }}</td><td class="kp-table__num">{{ $fmt($entry->amount) }}</td></tr>
        @empty<tr><td colspan="5">{{ sp_t('none') }}</td></tr>@endforelse
      </tbody>
    </table>
  </div>
</section>
@endsection
