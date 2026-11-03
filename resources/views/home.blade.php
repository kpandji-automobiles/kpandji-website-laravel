@extends('layouts.site')
@section('title', 'Accueil')
@php
  $promoActive = (bool) config('promo.active', true);
  $pct = (float) config('promo.reduction_percent');
  $pctLabel = rtrim(rtrim(number_format($pct, 1, ',', ''), '0'), ',');
  $offer = $pct > 0 ? str_replace(':pct', $pctLabel, (string) content('hero.offer_pct')) : content('hero.offer_text');
  $endsAt = config('promo.ends_at') ? \Illuminate\Support\Carbon::parse(config('promo.ends_at'))->format('d/m/Y') : null;
  $heroPoster = optional($cars->first(fn ($car) => filled($car->hero_image)))->hero_image;
  $chips = collect(content('hero.chips'))->pluck('label')->filter()->values();
  $tiles = collect(content('home.tiles'))->filter(fn ($tile) => trim($tile['title'] ?? '') !== '')->values();
  $bannerItems = collect(content('home.banner_items'))->pluck('fr')->filter()->values();
  $bannerWhatsapp = preg_replace('/\D/', '', (string) content('home.banner_wa_number'));
  $cards = collect(content('home.cards'))->filter(fn ($card) => trim($card['title'] ?? '') !== '')->values();
  $offerCategories = $cars->pluck('category')->filter()->unique()->values();
  $budgetModels = $cars->map(fn ($car) => ['name' => $car->name, 'price' => $car->discountedPrice() ?? $car->price])->filter(fn ($model) => $model['price'])->values();
@endphp

@section('content')
<section class="kp-hero kp-home-hero" data-kp-hero>
  @if(filled(content('hero.video')))
    <video class="kp-hero__media" autoplay muted loop playsinline @if($heroPoster) poster="{{ asset($heroPoster) }}" @endif aria-hidden="true"><source src="{{ cms_asset(content('hero.video')) }}" type="video/mp4"></video>
  @elseif($heroPoster)
    <img class="kp-hero__media" src="{{ asset($heroPoster) }}" alt="">
  @endif
  <div class="kp-hero__scrim"></div>
  <div class="kp-hero__content kp-wrap">
    @if(filled(content('hero.badge')))<span class="kp-eyebrow kp-on-stage-gold">{{ content('hero.badge') }}</span>@endif
    <h1 class="kp-hero__title">{{ content('hero.title2') ?: content('hero.title1') }}</h1>
    @if(filled(content('hero.subline')))<p class="body-l kp-home-hero__subline">{{ content('hero.subline') }}</p>@endif
    @if($promoActive && filled($offer))
      <p class="kp-hero__offer">{{ $offer }} @if($endsAt || filled(content('hero.legal')))<span>· {{ $endsAt ? content('hero.offer_until').' '.$endsAt : '' }} {{ content('hero.legal') }}</span>@endif</p>
    @endif
    @if($chips->isNotEmpty())
      <ul class="kp-home-hero__chips">@foreach($chips as $chip)<li>{{ $chip }}</li>@endforeach</ul>
    @endif
    <form class="kp-search kp-home-hero__search" method="GET" action="{{ route('cars.index') }}" role="search">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" class="kp-muted"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
      <input type="search" name="q" placeholder="{{ content('header.search_placeholder') }}" aria-label="Rechercher un modèle">
      <button class="kp-search__go" aria-label="Lancer la recherche">→</button>
    </form>
    <div class="kp-home-hero__actions">
      @if(filled(content('hero.cta1_label')))<a class="kp-btn kp-btn--stage" href="{{ cms_url(content('hero.cta1_url')) }}">{{ content('hero.cta1_label') }}</a>@endif
      @if(filled(content('hero.cta2_label')))<a class="kp-btn kp-btn--gold" href="{{ cms_url(content('hero.cta2_url')) }}">{{ content('hero.cta2_label') }}</a>@endif
    </div>
  </div>
</section>

@if($types->isNotEmpty())
<section class="kp-wrap kp-section" aria-labelledby="kp-types-title" data-kp-types>
  <div class="kp-section__head">
    <h2 class="heading-m" id="kp-types-title">{{ content('home.types_title') }}</h2>
    <a class="kp-btn kp-btn--link" href="{{ route('cars.index') }}">Tous les modèles</a>
  </div>
  <div class="kp-home-types">
    @foreach($types as $car)
      <a class="kp-disc" href="{{ route('cars.index', ['type' => $car->category]) }}">
        <span class="kp-disc__img">@if($car->image)<img src="{{ asset($car->image) }}" alt="" loading="lazy">@endif</span>{{ $car->category }}
      </a>
    @endforeach
  </div>
</section>
@endif

@if($tiles->isNotEmpty())
<section class="kp-wrap kp-section" aria-labelledby="kp-tiles-title" data-kp-tiles>
  <div class="kp-section__intro">
    <span class="kp-eyebrow">{{ content('home.tiles_eyebrow') }}</span>
    <h2 class="display-l kp-balance" id="kp-tiles-title">{{ content('home.tiles_title') }}</h2>
  </div>
  <div class="kp-home-tiles">
    @foreach($tiles as $tile)
      <a class="kp-tile" href="{{ cms_url($tile['url'] ?? '') }}"><span class="kp-tile__arrow" aria-hidden="true">↗</span><span class="kp-tile__title">{{ $tile['title'] }}</span><span class="kp-tile__text">{{ $tile['text'] ?? '' }}</span></a>
    @endforeach
  </div>
</section>
@endif

@if(filled(content('home.banner_title')))
<section class="kp-home-banner" data-kp-banner>
  <div class="kp-wrap kp-home-banner__inner">
    <div class="kp-section__intro">
      <span class="kp-eyebrow kp-on-stage-gold">{{ content('home.banner_eyebrow') }}</span>
      <h2 class="heading-l">{{ content('home.banner_title') }}</h2>
      @if($bannerItems->isNotEmpty())
        <ul class="kp-home-banner__items">@foreach($bannerItems as $item)<li>{{ $item }}</li>@endforeach</ul>
      @endif
    </div>
    <div class="kp-home-banner__actions">
      @if(filled(content('home.banner_cta')))<a class="kp-btn kp-btn--gold" href="{{ cms_url(content('home.banner_url')) }}">{{ content('home.banner_cta') }}</a>@endif
      @if($bannerWhatsapp !== '' && filled(content('home.banner_wa')))<a class="kp-btn kp-btn--stage" href="https://wa.me/{{ $bannerWhatsapp }}" target="_blank" rel="noopener">{{ content('home.banner_wa') }}</a>@endif
    </div>
  </div>
</section>
@endif

@if($cars->isNotEmpty())
<section class="kp-home-offers" id="offres" aria-labelledby="kp-offers-title" x-data="{ type: '' }" data-kp-offers>
  <div class="kp-wrap kp-section">
    <div class="kp-section__head">
      <div class="kp-section__intro">
        <span class="kp-eyebrow">{{ content('home.offers_eyebrow') }}</span>
        <h2 class="heading-l" id="kp-offers-title">{{ content('home.offers_title') }}</h2>
      </div>
      @if($offerCategories->count() > 1)
        <div class="kp-chips" role="group" aria-label="Filtrer par type">
          <button type="button" class="kp-chip" :aria-pressed="type === ''" @click="type = ''">Tous</button>
          @foreach($offerCategories as $category)
            <button type="button" class="kp-chip" :aria-pressed="type === @js($category)" @click="type = @js($category)">{{ $category }}</button>
          @endforeach
        </div>
      @endif
    </div>
    <div class="kp-home-offers__grid">
      @foreach($cars as $car)
        <div x-show="type === '' || type === @js($car->category)">@include('site.vehicle-card', ['car' => $car])</div>
      @endforeach
    </div>
  </div>
</section>
@endif

<section class="kp-wrap kp-section kp-home-budget" aria-labelledby="kp-budget-title" data-kp-budget
  x-data="{ apport: '3 000 000', monthly: '450 000', months: 60, models: @js($budgetModels),
    num(value) { return parseInt(String(value).replace(/\D/g, ''), 10) || 0 },
    get budget() { return this.num(this.apport) + this.num(this.monthly) * this.months },
    get reachable() { return this.models.filter(model => model.price <= this.budget) },
    fcfa(value) { return new Intl.NumberFormat('fr-FR').format(value).replace(/ | /g, ' ') + ' FCFA' } }">
  <div class="kp-section__intro">
    <span class="kp-eyebrow">{{ content('home.budget_eyebrow') }}</span>
    <h2 class="heading-l" id="kp-budget-title">{{ content('home.budget_title') }}</h2>
    @if(filled(content('home.budget_image')))<img class="kp-home-budget__image" src="{{ cms_asset(content('home.budget_image')) }}" alt="" loading="lazy">@endif
  </div>
  <div class="kp-budget">
    <div class="kp-eyebrow">Simulez votre budget</div>
    <div class="kp-budget__row">
      <div class="kp-field"><input class="kp-field__control" id="kp-budget-apport" inputmode="numeric" x-model="apport"><label class="kp-field__label" for="kp-budget-apport">Apport (FCFA)</label></div>
      <div class="kp-field"><input class="kp-field__control" id="kp-budget-monthly" inputmode="numeric" x-model="monthly"><label class="kp-field__label" for="kp-budget-monthly">Mensualité (FCFA)</label></div>
    </div>
    <div class="kp-field">
      <select class="kp-field__control" id="kp-budget-months" x-model.number="months">
        @foreach([24, 36, 48, 60, 72] as $months)<option value="{{ $months }}">{{ $months }} mois</option>@endforeach
      </select>
      <label class="kp-field__label" for="kp-budget-months">Durée</label>
    </div>
    <div class="kp-budget__result" aria-live="polite">
      Budget estimé<strong x-text="fcfa(budget)"></strong>
      <span x-show="models.length" x-text="reachable.length ? reachable.length + ' modèle(s) à votre portée : ' + reachable.map(model => model.name).join(', ') + '.' : 'Aucun modèle à prix affiché dans ce budget.'"></span>
      {{ content('home.budget_note') }}
    </div>
    <a class="kp-btn kp-btn--primary" href="{{ route('cars.index') }}" :href="@js(route('cars.index')) + '?budget=' + budget">{{ content('home.budget_cta') }}</a>
  </div>
</section>

@if($cards->isNotEmpty())
<section class="kp-wrap kp-home-cards" data-kp-cards>
  @foreach($cards as $card)
    <a class="kp-home-card" href="{{ cms_url($card['url'] ?? '') }}">
      @if(filled($card['image'] ?? null))<img src="{{ cms_asset($card['image']) }}" alt="" loading="lazy">@endif
      <span class="kp-home-card__body">
        <span class="kp-eyebrow kp-on-stage-gold">{{ $card['eyebrow'] ?? '' }}</span>
        <span class="heading-m">{{ $card['title'] }}</span>
        @if(filled($card['cta'] ?? null))<span class="kp-btn kp-btn--stage kp-btn--sm">{{ $card['cta'] }}</span>@endif
      </span>
    </a>
  @endforeach
</section>
@endif
@endsection
