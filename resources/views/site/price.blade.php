{{-- Prix d'un modèle (design system) : mêmes règles que partials/price — prix promo, prix catalogue, remise globale ou sur demande. --}}
@php($promo = $car->discountedPrice())
@php($pct = (float) config('promo.reduction_percent'))
<div class="kp-price{{ ($large ?? false) ? ' kp-price--l' : '' }}">
  @if($promo && $car->price)
    <span class="kp-price__label">{{ content('price.promo_ttc') }}</span>
    <span class="kp-price__value">{{ \App\Models\Car::fcfa($promo) }}</span>
    <span class="kp-price__save">{{ content('price.save') }} {{ \App\Models\Car::fcfa($car->price - $promo) }} · <s>{{ \App\Models\Car::fcfa($car->price) }}</s></span>
  @elseif($car->promo_price)
    <span class="kp-price__label">{{ $car->hasVersions() ? content('price.promo_ttc_from') : content('price.promo_ttc') }}</span>
    <span class="kp-price__value">{{ \App\Models\Car::fcfa((int) $car->promo_price) }}</span>
    @if($car->campaign_price)<span class="kp-price__legal">{{ content('price.pack') }} {{ \App\Models\Car::fcfa((int) $car->campaign_price) }}</span>@endif
  @elseif($car->versionPrices()->isNotEmpty())
    <span class="kp-price__label">{{ $car->hasPriceRange() ? content('price.promo_ttc_from') : content('price.promo_ttc') }}</span>
    <span class="kp-price__value">{{ \App\Models\Car::fcfa($car->startingPrice()) }}</span>
  @elseif($car->price)
    <span class="kp-price__label">{{ content('price.from') }}</span>
    <span class="kp-price__value">{{ \App\Models\Car::fcfa((int) $car->price) }}</span>
  @elseif($pct > 0)
    <span class="kp-price__label">{{ content('price.discount') }}</span>
    <span class="kp-price__value">-{{ rtrim(rtrim(number_format($pct, 1, ',', ''), '0'), ',') }} %</span>
  @else
    <span class="kp-price__label">{{ content('price.special') }}</span>
    <span class="kp-price__value">{{ content('price.on_request') }}</span>
  @endif
</div>
