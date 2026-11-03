{{-- Showroom (design system) : chaque modèle du showroom avec visuels, points forts, prix et brochure.
     Modèles : back-office › Modèles (accueil + showroom) ; textes : Contenus › Showroom (showroomv.*). --}}
@extends('layouts.site')
@section('seo_title', content('showroomv.seo_title'))
@section('meta_description', content('showroomv.seo_description'))
@push('meta')
<link rel="canonical" href="{{ route('showroom') }}">
<meta property="og:title" content="{{ content('showroomv.seo_title') }}">
<meta property="og:description" content="{{ content('showroomv.og_description') }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ route('showroom') }}">
@endpush

@section('content')
<section class="kp-catalog-head">
  <div class="kp-wrap kp-section__intro kp-catalog-head__inner">
    <span class="kp-eyebrow">{{ content('showroomv.sk_kicker') }}</span>
    <h1 class="display-l">{{ content('showroomv.title') }}</h1>
    @if(filled(content('showroomv.sk_intro')))<p class="body-l kp-muted kp-editorial__lead">{{ content('showroomv.sk_intro') }}</p>@endif
  </div>
</section>

<div class="kp-wrap kp-showroom">
  @forelse($models as $model)
    @php
      $car = $cars->get($model->car_slug);
      $images = collect($model->gallery ?? [])->filter(fn ($image) => filled($image['src'] ?? null))->values();
      $cover = $images->first()['src'] ?? $model->top_image ?? $car?->hero_image;
      $highlights = collect($model->highlights ?? [])->pluck('fr')->filter()->take(4);
      $price = $car?->startingPrice();
    @endphp
    <article class="kp-showroom__model" id="{{ $model->key ?: 'modele-'.$model->id }}" data-kp-showroom-model="{{ $model->id }}">
      <div class="kp-showroom__media">
        <div class="kp-vcard__media">
          @if($cover)<img src="{{ cms_asset($cover) }}" alt="{{ $images->first()['alt'] ?? $model->name }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}">@endif
          @if(filled($model->badge_fr))<span class="kp-vcard__badge">{{ $model->badge_fr }}</span>@endif
        </div>
        @if($images->count() > 1)
          <div class="kp-showroom__thumbs" aria-label="{{ content('showroomv.lbl_gallery') }}">
            @foreach($images->slice(1, 4) as $image)
              <a href="{{ cms_asset($image['src']) }}" target="_blank" rel="noopener"><img src="{{ cms_asset($image['src']) }}" alt="{{ $image['alt'] ?? $model->name }}" loading="lazy"></a>
            @endforeach
          </div>
        @endif
      </div>
      <div class="kp-showroom__body">
        @if($car?->category)<span class="kp-eyebrow">{{ $car->category }}</span>@endif
        <h2 class="heading-l">{{ $model->name }}</h2>
        @if(filled($model->tagline_fr))<p class="body-l">{{ $model->tagline_fr }}</p>@endif
        @if(filled($model->sr_description_fr))<p class="body kp-muted">{{ $model->sr_description_fr }}</p>@endif
        @if($highlights->isNotEmpty())
          <ul class="kp-vcard__specs">@foreach($highlights as $highlight)<li>{{ $highlight }}</li>@endforeach</ul>
        @endif
        @if($price)
          <div class="kp-price"><span class="kp-price__label">{{ content('price.from') }}</span><span class="kp-price__value">{{ \App\Models\Car::fcfa($price) }}</span></div>
        @endif
        <div class="kp-home-hero__actions">
          @if($car)<a class="kp-btn kp-btn--primary" href="{{ route('cars.show', $car) }}">Découvrir</a>@endif
          @if($model->brochure)<a class="kp-btn kp-btn--secondary" href="{{ cms_asset($model->brochure) }}" target="_blank" rel="noopener">{{ content('showroomv.btn_download') }}</a>@endif
          <a class="kp-btn kp-btn--link" href="{{ route('essai', array_filter(['modele' => $model->car_slug])) }}">{{ content('showroomv.btn_appointment') }}</a>
        </div>
      </div>
    </article>
  @empty
    <p class="body kp-muted">{{ content('showroomv.no_visual') }}</p>
  @endforelse
</div>

<section class="kp-home-banner">
  <div class="kp-wrap kp-home-banner__inner">
    <div class="kp-section__intro">
      <span class="kp-eyebrow kp-on-stage-gold">{{ content('showroomv.help_kicker') }}</span>
      <h2 class="heading-l">{{ content('showroomv.help_title') }}</h2>
      <p class="body kp-fiche-hero__lead">{{ content('showroomv.help_text') }}</p>
    </div>
    <div class="kp-home-banner__actions"><a class="kp-btn kp-btn--gold" href="{{ cms_url(content('showroomv.sav_url')) }}">{{ content('showroomv.btn_sav') }}</a></div>
  </div>
</section>
@endsection
