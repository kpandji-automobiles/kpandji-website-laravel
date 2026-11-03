{{-- Fiche modèle /modeles/{slug} (design system KPANDJI).
     Données uniquement issues des tables cars / modeles (aucune donnée inventée) ; textes : Contenus › « Fiche modèle » (modelp.*). --}}
@extends('layouts.site')
@php
  $name = fn ($text) => str_replace(':name', $car->name, (string) $text);
  $desc = trim((string) $car->description);
  $lead = \Illuminate\Support\Str::limit(preg_split('/(?<=[.!?])\s/u', $desc)[0] ?? $desc, 180);
  $heroImg = $car->hero_image ?: $car->image;
  $versions = collect($car->versions ?? [])->filter(fn ($version) => filled($version['version'] ?? null))->values();
  $price = $car->startingPrice();
  $saving = $car->price && $price && $car->price > $price ? (int) $car->price - $price : null;
  $brochure = $car->brochure ?: ($m?->brochure && file_exists(public_path($m->brochure)) ? $m->brochure : null);
  $whatsapp = preg_replace('/\D/', '', (string) content('modelp.wa_number'));
  $specs = collect($car->specs ?? [])->map(fn ($rows) => collect($rows)->filter(fn ($value) => filled($value)))->filter(fn ($rows) => $rows->isNotEmpty());
  $flat = $specs->flatMap(fn ($rows) => $rows->map(fn ($value, $key) => ['k' => $key, 'v' => $value])->values());
  $keyFigures = collect(['/^(moteur|motorisation)$/iu', '/puissance/iu', '/couple/iu', '/(transmission|boîte)/iu', '/vitesse/iu', '/places/iu'])
    ->map(fn ($pattern) => $flat->first(fn ($row) => preg_match($pattern, $row['k'])))->filter()->unique('k')->take(4)->values();
  $highlights = collect($car->highlights ?? [])->filter()->values();
  $gallery = collect($m?->gallery ?? [])->filter(fn ($image) => filled($image['src'] ?? null) && file_exists(public_path($image['src'])))
    ->map(fn ($image) => ['src' => $image['src'], 'alt' => ($image['alt'] ?? '') ?: $car->name])
    ->concat(collect([$m?->top_image, $m?->bottom_left_image, $m?->bottom_right_image])->filter(fn ($src) => $src && file_exists(public_path($src)))->map(fn ($src) => ['src' => $src, 'alt' => $car->name]))
    ->unique('src')->values();
  $video = $m?->top_video && file_exists(public_path($m->top_video)) ? $m->top_video : null;
  $advantagesOn = ! in_array(mb_strtolower(trim((string) content('modelp.adv_show'))), ['non', 'no', '0', 'off', 'false'], true);
  $advantages = $advantagesOn ? collect(content('modelp.adv_items'))->pluck('fr')->filter()->values() : collect();
  $endsAt = config('promo.active', true) && config('promo.ends_at') ? \Illuminate\Support\Carbon::parse(config('promo.ends_at'))->format('d/m/Y') : null;
  $metaDescription = \Illuminate\Support\Str::limit($desc, 157);
@endphp
@section('title', $car->name)
@section('meta_description', $metaDescription)
@push('meta')
<link rel="canonical" href="{{ route('cars.show', $car) }}">
<meta property="og:title" content="{{ $car->name.content('modelp.seo_suffix') }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="fr_FR">
<meta property="og:url" content="{{ route('cars.show', $car) }}">
@if($heroImg)<meta property="og:image" content="{{ asset($heroImg) }}">@endif
@endpush

@section('content')
<section class="kp-hero kp-fiche-hero" data-kp-fiche>
  @if($heroImg)<img class="kp-hero__media" src="{{ asset($heroImg) }}" alt="{{ $car->name }}" fetchpriority="high">@endif
  <div class="kp-hero__scrim"></div>
  <div class="kp-hero__content kp-wrap">
    <nav class="kp-fiche-hero__crumbs" aria-label="Fil d'Ariane"><a href="{{ route('home') }}">Accueil</a> / <a href="{{ route('cars.index') }}">{{ content('modelp.back') }}</a> / <span aria-current="page">{{ $car->name }}</span></nav>
    @if($car->category)<span class="kp-eyebrow kp-on-stage-gold">{{ $car->category }}@if($car->discountedPrice()) · Promo @endif</span>@endif
    <h1 class="kp-hero__title">{{ $car->name }}</h1>
    @if($lead !== '')<p class="body-l kp-fiche-hero__lead">{{ $lead }}</p>@endif
  </div>
</section>

<div class="kp-wrap kp-fiche">
  <div class="kp-fiche__main">
    @if($keyFigures->isNotEmpty() || $highlights->isNotEmpty() || $desc !== '')
    <section class="kp-fiche__section" id="points-forts" aria-labelledby="kp-fiche-highlights">
      <h2 class="heading-m" id="kp-fiche-highlights">{{ content('modelp.hl_kicker') }}</h2>
      @if($keyFigures->isNotEmpty() || filled($car->warranty))
        <div class="kp-fiche__figures" aria-label="{{ content('modelp.kf_aria') }}">
          @foreach($keyFigures as $figure)
            <div class="kp-kpi"><span class="kp-kpi__label">{{ $figure['k'] }}</span><span class="heading-s">{{ $figure['v'] }}</span></div>
          @endforeach
          @if(filled($car->warranty))<div class="kp-kpi"><span class="kp-kpi__label">Garantie</span><span class="heading-s">{{ $car->warranty }}</span></div>@endif
        </div>
      @endif
      @if($highlights->isNotEmpty())
        <ul class="kp-vcard__specs kp-fiche__highlights">@foreach($highlights as $highlight)<li>{{ $highlight }}</li>@endforeach</ul>
      @endif
      @if($desc !== '')<p class="body kp-fiche__text">{{ $desc }}</p>@endif
    </section>
    @endif

    @if($gallery->isNotEmpty() || $video)
    <section class="kp-fiche__section" id="galerie" aria-labelledby="kp-fiche-gallery">
      <h2 class="heading-m" id="kp-fiche-gallery">{{ content('modelp.gal_title') }}</h2>
      @if($video)<video class="kp-fiche__video" controls preload="metadata" @if($heroImg) poster="{{ asset($heroImg) }}" @endif><source src="{{ asset($video) }}" type="video/mp4"></video>@endif
      @if($gallery->isNotEmpty())
        <div class="kp-fiche__gallery">
          @foreach($gallery as $image)
            <a href="{{ asset($image['src']) }}" target="_blank" rel="noopener"><img src="{{ asset($image['src']) }}" alt="{{ $image['alt'] }}" loading="lazy"></a>
          @endforeach
        </div>
      @endif
    </section>
    @endif

    @if($versions->isNotEmpty())
    <section class="kp-fiche__section" id="versions" aria-labelledby="kp-fiche-versions">
      <h2 class="heading-m" id="kp-fiche-versions">{{ content('modelp.versions_title') }}</h2>
      <div>
        @foreach($versions as $version)
          <div class="kp-fiche__version" data-kp-version>
            <div class="kp-fiche__version-name">
              <span class="heading-s">{{ $version['version'] }}</span>
              <span class="body-s kp-muted">{{ collect([$version['motorisation'] ?? null, $version['boite'] ?? null, $version['transmission'] ?? null])->filter()->implode(' · ') }}</span>
              @if(! empty($version['maintenance_cost']))<span class="body-s kp-muted">{{ content('modelp.th_maint') }} : {{ \App\Models\Car::fcfa((int) $version['maintenance_cost']) }}</span>@endif
            </div>
            <span class="kp-price__value">{{ \App\Models\Car::fcfa(! empty($version['promo_price']) ? (int) $version['promo_price'] : null) }}</span>
          </div>
        @endforeach
      </div>
    </section>
    @endif

    @if($specs->isNotEmpty())
    <section class="kp-fiche__section" id="caracteristiques" aria-labelledby="kp-fiche-specs">
      <h2 class="heading-m" id="kp-fiche-specs">{{ content('modelp.specs_title') }}</h2>
      @foreach($specs as $group => $rows)
        <div class="kp-fiche__specs">
          <h3 class="kp-price__label">{{ $group }}</h3>
          <dl>
            @foreach($rows as $key => $value)<div><dt class="kp-muted">{{ $key }}</dt><dd>{{ $value }}</dd></div>@endforeach
          </dl>
        </div>
      @endforeach
    </section>
    @endif

    @if($advantages->isNotEmpty())
    <section class="kp-fiche__section" id="garantie" aria-labelledby="kp-fiche-warranty">
      <h2 class="heading-m" id="kp-fiche-warranty">{{ content('modelp.adv_title') }}</h2>
      <ul class="kp-home-banner__items kp-fiche__advantages">@foreach($advantages as $advantage)<li>{{ $advantage }}</li>@endforeach</ul>
    </section>
    @endif
  </div>

  <aside class="kp-fiche__aside" aria-label="Prix et contact">
    <div class="kp-price kp-price--l" data-kp-fiche-price>
      @if($price)
        <span class="kp-price__label">{{ $car->hasPriceRange() ? content('modelp.price_from') : content('modelp.price_label') }}</span>
        <span class="kp-price__value">{{ \App\Models\Car::fcfa($price) }}</span>
        @if($saving)<span class="kp-price__save">{{ content('price.save') }} {{ \App\Models\Car::fcfa($saving) }} · <s>{{ \App\Models\Car::fcfa((int) $car->price) }}</s></span>@endif
        @if($car->campaign_price)<span class="kp-price__legal">{{ content('price.pack') }} {{ \App\Models\Car::fcfa((int) $car->campaign_price) }}</span>@endif
      @else
        <span class="kp-price__label">{{ content('price.special') }}</span>
        <span class="kp-price__value">{{ content('modelp.price_request') }}</span>
      @endif
      @if($endsAt || filled(content('hero.legal')))<span class="kp-price__legal">{{ $endsAt ? content('hero.offer_until').' '.$endsAt.'. ' : '' }}{{ content('hero.legal') }}</span>@endif
    </div>
    <a class="kp-btn kp-btn--gold" href="{{ route('essai', ['modele' => $car->slug]) }}">{{ content('modelp.cta_test') }}</a>
    @if($whatsapp !== '')<a class="kp-btn kp-btn--secondary" href="https://wa.me/{{ $whatsapp }}?text={{ rawurlencode($name(content('modelp.wa_message'))) }}" target="_blank" rel="noopener">{{ content('modelp.cta_wa') }}</a>@endif
    @if($brochure)<a class="kp-btn kp-btn--secondary" href="{{ asset($brochure) }}" target="_blank" rel="noopener">{{ content('modelp.cta_pdf') }}</a>@endif
    <a class="kp-btn kp-btn--link" href="{{ route('home') }}#budget">Simuler mon budget</a>
    @if(filled(content('modelp.end_text')))<p class="kp-budget__result">{{ $name(content('modelp.end_text')) }}</p>@endif
  </aside>
</div>

@if($others->isNotEmpty())
<section class="kp-home-offers" aria-labelledby="kp-fiche-related">
  <div class="kp-wrap kp-section">
    <div class="kp-section__intro">
      <span class="kp-eyebrow">{{ content('modelp.related_kicker') }}</span>
      <h2 class="heading-l" id="kp-fiche-related">{{ content('modelp.related_title') }}</h2>
    </div>
    <div class="kp-home-offers__grid">
      @foreach($others as $other)
        @include('site.vehicle-card', ['car' => $other])
      @endforeach
    </div>
  </div>
</section>
@endif
@endsection
