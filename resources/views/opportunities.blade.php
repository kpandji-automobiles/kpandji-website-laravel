{{-- Opportunités (design system) : programmes Kpandji Sira et Privilège. Textes et cartes : Contenus › Opportunités (opps.*). --}}
@extends('layouts.site')
@php($programmes = collect(content('opps.cards'))->filter(fn ($card) => ($card['visible'] ?? '1') !== '0' && filled($card['name'] ?? null))->values())
@section('seo_title', content('opps.seo_title'))
@section('meta_description', content('opps.seo_description'))
@push('meta')
<meta property="og:title" content="{{ content('opps.og_title') }}">
<meta property="og:description" content="{{ content('opps.og_description') }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ route('opportunities') }}">
@endpush

@section('content')
@include('site.editorial.hero', [
  'image' => $programmes->first()['image'] ?? null, 'crumbs' => content('opps.title'),
  'kicker' => content('opps.eyebrow'), 'title' => content('opps.title'), 'text' => content('opps.intro'),
])

@if($programmes->isNotEmpty())
<section class="kp-wrap kp-section" aria-label="Programmes" data-kp-programmes>
  <div class="kp-home-cards">
    @foreach($programmes as $programme)
      <a class="kp-home-card kp-opportunity" href="{{ cms_url($programme['url'] ?? '') }}">
        @if(filled($programme['image'] ?? null))<img src="{{ cms_asset($programme['image']) }}" alt="{{ $programme['alt'] ?? '' }}" loading="lazy" style="object-position: {{ preg_match('/^[\d.]+%\s+[\d.]+%$/', (string) ($programme['position'] ?? '')) ? $programme['position'] : 'center' }}">@endif
        <span class="kp-home-card__body">
          <span class="kp-eyebrow kp-on-stage-gold">{{ $programme['eyebrow'] ?? '' }}</span>
          <span class="heading-l">{{ trim(($programme['brand'] ?? '').' '.$programme['name']) }}</span>
          @if(filled($programme['description'] ?? null))<span class="body kp-fiche-hero__lead">{{ $programme['description'] }}</span>@endif
          <span class="kp-btn kp-btn--stage kp-btn--sm">{{ content('opps.cta') }}</span>
        </span>
      </a>
    @endforeach
  </div>
</section>
@endif
@endsection
