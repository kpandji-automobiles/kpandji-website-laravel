{{-- Histogramme des revenus par mois. Paramètre : months ([« AAAA-MM » => montant]). --}}
@php($chartMax = max(1, max($months ?: [0])))
<div class="kp-bars" data-sp-chart role="img" aria-label="{{ collect($months)->map(fn ($value, $month) => $month.' : '.fcfa($value))->implode(', ') }}">
  @foreach($months as $month => $value)
    <div class="kp-bars__col" title="{{ $month }} : {{ fcfa($value) }}">
      <span class="kp-bars__bar" style="--kp-bar: {{ round($value / $chartMax * 100) }}%"></span>
      <span class="kp-bars__label">{{ \Illuminate\Support\Carbon::parse($month.'-01')->locale('fr')->isoFormat('MMM') }}</span>
    </div>
  @endforeach
</div>
