@php($promo = $car->discountedPrice())
@php($pct = (float) config('promo.reduction_percent'))
{{-- Ruban rouge promo — prix issus de « TARIF CAMPAGNE V.xlsx », aucun prix inventé --}}
<div class="kp-ribbon">
  <div class="kp-ribbon-body">
    <span class="kp-ribbon-shine" aria-hidden="true"></span>
    @if($promo && $car->price)
      <p class="text-[11px] font-semibold text-white/75 line-through decoration-white/80">{{ \App\Models\Car::fcfa($car->price) }}</p>
      <p class="kp-price-big font-serif text-[1.7rem] font-bold leading-tight text-white"><span class="kp-count grid" x-data="kpCount({{ (int) $promo }})" x-init="init()"><span class="invisible col-start-1 row-start-1">{{ \App\Models\Car::fcfa($promo) }}</span><span class="col-start-1 row-start-1" x-text="txt">{{ \App\Models\Car::fcfa($promo) }}</span></span></p>
      <p class="mt-0.5 inline-block kp-save rounded-full bg-black/35 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-[0.18em] text-white">{{ content('price.save') }} {{ \App\Models\Car::fcfa($car->price - $promo) }}</p>
    @elseif($car->promo_price)
      <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-white/85">{{ $car->hasVersions() ? content('price.promo_ttc_from') : content('price.promo_ttc') }}</p>
      <p class="kp-price-big font-serif text-[1.7rem] font-bold leading-tight text-white"><span class="kp-count grid" x-data="kpCount({{ (int) $car->promo_price }})" x-init="init()"><span class="invisible col-start-1 row-start-1">{{ \App\Models\Car::fcfa($car->promo_price) }}</span><span class="col-start-1 row-start-1" x-text="txt">{{ \App\Models\Car::fcfa($car->promo_price) }}</span></span></p>
      @if($car->campaign_price)<p class="mt-0.5 text-[10px] font-semibold uppercase tracking-[0.14em] text-white/85">{{ content('price.pack') }} {{ \App\Models\Car::fcfa($car->campaign_price) }}</p>@endif
    @elseif($car->price)
      <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-white/80">@content('price.from')</p>
      <p class="kp-price-big font-serif text-[1.7rem] font-bold leading-tight text-white"><span class="kp-count grid" x-data="kpCount({{ (int) $car->price }})" x-init="init()"><span class="invisible col-start-1 row-start-1">{{ \App\Models\Car::fcfa($car->price) }}</span><span class="col-start-1 row-start-1" x-text="txt">{{ \App\Models\Car::fcfa($car->price) }}</span></span></p>
    @elseif($pct > 0)
      <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-white/80">@content('price.discount')</p>
      <p class="font-serif text-3xl font-bold leading-tight text-white">-{{ rtrim(rtrim(number_format($pct, 1, ',', ''), '0'), ',') }} %</p>
    @else
      <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-white/80">@content('price.special')</p>
      <p class="font-serif text-xl font-bold leading-tight text-white">@content('price.on_request')</p>
    @endif
  </div>
</div>
