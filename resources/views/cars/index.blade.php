@extends('layouts.site')
@section('title', content('catalog.title'))
@section('meta_description', 'La gamme KPANDJI : pick-up, SUV, citadines et mini vans assemblés en Côte d’Ivoire. Prix affichés, essai sur rendez-vous.')
@use('App\Support\Catalogue')
@php
  $byType = Catalogue::apply($range, $filters, 'type')->countBy('category');
  $byEnergy = Catalogue::apply($range, $filters, 'energie')->flatMap(fn ($car) => Catalogue::energiesOf($car))->countBy();
  $byGearbox = Catalogue::apply($range, $filters, 'boite')->flatMap(fn ($car) => Catalogue::gearboxesOf($car))->countBy();
  $categories = $range->pluck('category')->filter()->unique()->values();
  $energies = array_intersect_key(Catalogue::ENERGIES, $range->flatMap(fn ($car) => Catalogue::energiesOf($car))->flip()->all());
  $gearboxes = array_intersect_key(Catalogue::GEARBOXES, $range->flatMap(fn ($car) => Catalogue::gearboxesOf($car))->flip()->all());
@endphp

@section('content')
<section class="kp-catalog-head">
  <div class="kp-wrap kp-section__intro kp-catalog-head__inner">
    <span class="kp-eyebrow">{{ content('catalog.eyebrow') }}</span>
    <h1 class="display-l">{{ content('catalog.title') }}</h1>
    @if(filled(content('catalog.intro')))<p class="body-l kp-muted">{{ content('catalog.intro') }}</p>@endif
  </div>
</section>

<form class="kp-wrap kp-catalog" method="GET" action="{{ route('cars.index') }}" x-data @change="$el.requestSubmit()" data-kp-catalog>
  @foreach(['type', 'energie', 'boite'] as $kept)
    @if($filters[$kept] !== '')<input type="hidden" name="{{ $kept }}" value="{{ $filters[$kept] }}">@endif
  @endforeach

  <aside class="kp-catalog__filters" aria-label="Filtres">
    <div class="kp-search">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" class="kp-muted"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
      <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="{{ content('catalog.search_placeholder') }}" aria-label="Rechercher un modèle" maxlength="100">
      <button class="kp-search__go" aria-label="Lancer la recherche">→</button>
    </div>

    @if($categories->count() > 1)
    <div class="kp-catalog__group">
      <span class="kp-price__label">Catégorie</span>
      <div class="kp-chips">
        <a class="kp-chip" href="{{ Catalogue::url($filters, ['type' => '']) }}" @if($filters['type'] === '') aria-current="true" @endif>Toutes <span class="kp-chip__count">{{ $byType->sum() }}</span></a>
        @foreach($categories as $category)
          <a class="kp-chip" href="{{ Catalogue::url($filters, ['type' => $filters['type'] === $category ? '' : $category]) }}" @if($filters['type'] === $category) aria-current="true" @endif>{{ $category }} <span class="kp-chip__count">{{ $byType[$category] ?? 0 }}</span></a>
        @endforeach
      </div>
    </div>
    @endif

    @foreach([['energie', 'Énergie', $energies, $byEnergy], ['boite', 'Boîte', $gearboxes, $byGearbox]] as [$key, $label, $options, $counts])
      @if(count($options) > 1)
      <div class="kp-catalog__group">
        <span class="kp-price__label">{{ $label }}</span>
        <div class="kp-chips">
          @foreach($options as $value => $optionLabel)
            <a class="kp-chip" href="{{ Catalogue::url($filters, [$key => $filters[$key] === $value ? '' : $value]) }}" @if($filters[$key] === $value) aria-current="true" @endif>{{ $optionLabel }} <span class="kp-chip__count">{{ $counts[$value] ?? 0 }}</span></a>
          @endforeach
        </div>
      </div>
      @endif
    @endforeach

    <div class="kp-catalog__group">
      <span class="kp-price__label">Budget maximum</span>
      <div class="kp-field">
        <input class="kp-field__control" id="kp-catalog-budget" name="budget" inputmode="numeric" value="{{ $filters['budget'] ? number_format($filters['budget'], 0, ',', ' ') : '' }}" placeholder="30 000 000">
        <label class="kp-field__label" for="kp-catalog-budget">FCFA</label>
      </div>
    </div>

    <a class="kp-tile kp-catalog__tile" href="{{ route('home') }}#budget"><span class="kp-tile__arrow" aria-hidden="true">↗</span><span class="kp-tile__title">Simuler mon budget</span><span class="kp-tile__text">Apport et mensualité.</span></a>
  </aside>

  <div class="kp-catalog__results">
    <div class="kp-catalog__bar">
      <p class="body-s kp-muted" aria-live="polite" data-kp-count>
        {{ $cars->count() }} {{ $cars->count() > 1 ? 'modèles' : 'modèle' }} · {{ content('catalog.legal') }}
        @if(Catalogue::isFiltered($filters)) · <a href="{{ route('cars.index') }}">Effacer les filtres</a>@endif
      </p>
      <div class="kp-field kp-catalog__sort">
        <select class="kp-field__control" id="kp-catalog-sort" name="tri">
          @foreach(Catalogue::SORTS as $value => $sortLabel)<option value="{{ $value }}" @selected($filters['tri'] === $value)>{{ $sortLabel }}</option>@endforeach
        </select>
        <label class="kp-field__label" for="kp-catalog-sort">Trier par</label>
      </div>
      <noscript><button class="kp-btn kp-btn--secondary kp-btn--sm">Appliquer</button></noscript>
    </div>

    @if($cars->isEmpty())
      <div class="kp-catalog__empty">
        <p class="heading-s">{{ content('catalog.empty') }}</p>
        <a class="kp-btn kp-btn--secondary" href="{{ route('cars.index') }}">Voir toute la gamme</a>
      </div>
    @else
      <div class="kp-catalog__grid">
        @foreach($cars as $car)
          @include('site.vehicle-card', ['car' => $car])
        @endforeach
      </div>
    @endif
  </div>
</form>
@endsection
