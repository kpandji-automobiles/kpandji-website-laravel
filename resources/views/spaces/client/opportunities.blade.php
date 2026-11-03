@extends('spaces.client.layout')
@section('h1', sp_t('opp_title'))
@section('space')
<div class="kp-section__head">
  <p class="body kp-muted">{{ sp_t('opp_intro') }}</p>
  <a class="kp-btn kp-btn--gold kp-btn--sm" href="{{ cms_url(sp_t('opp_url')) }}" data-sp-opp-new>{{ sp_t('opp_new') }}</a>
</div>
<div class="kp-home-offers__grid">
  @forelse($items as $item)
    <article class="kp-kpi" data-sp-opp="{{ $item['source'] }}-{{ $item['id'] }}">
      <div class="kp-section__head">
        <span class="heading-s">Kpandji {{ $item['source'] === 'sira' ? 'Sira' : 'Privilège' }}</span>
        <span class="kp-status kp-status--{{ $item['status'] === 'traite' ? 'done' : ($item['status'] === 'nouveau' ? 'new' : 'wait') }}">{{ sp_t('st_'.$item['status']) }}</span>
      </div>
      <dl class="kp-client__details">
        <div><dt>{{ sp_t('date') }}</dt><dd>{{ \Illuminate\Support\Carbon::parse($item['date'])->format('d/m/Y H:i') }}</dd></div>
        <div><dt>{{ sp_t('opp_city') }}</dt><dd>{{ $item['subject'] }}</dd></div>
        <div><dt>{{ sp_t('phone') }}</dt><dd>{{ $item['phone'] }}</dd></div>
        <div><dt>{{ sp_t('email') }}</dt><dd>{{ $item['email'] }}</dd></div>
        @if($item['source'] === 'privilege')<div><dt>{{ sp_t('opp_channel') }}</dt><dd>{{ sp_t('opp_ch_'.(($item['raw']['source'] ?? 'contact') === 'advisor' ? 'advisor' : 'contact')) }}</dd></div>@endif
      </dl>
      @if($item['replies'])<a class="kp-btn kp-btn--link" href="{{ route('client.thread', $item['source'].'-'.$item['id']) }}">{{ sp_t('replied') }}</a>@endif
    </article>
  @empty
    <div class="kp-catalog__empty"><p class="body">{{ sp_t('opp_none') }}</p></div>
  @endforelse
</div>
@endsection
