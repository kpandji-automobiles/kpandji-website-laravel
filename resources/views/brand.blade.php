{{-- Kpandji Automobiles (design system). Textes et images : Contenus › Page Kpandji Automobiles (brand.*). --}}
@extends('layouts.site')
@php
  $stats = collect(content('brand.stats'))->filter(fn ($stat) => filled($stat['value'] ?? null))->values();
  $marquee = collect(content('brand.marquee'))->pluck('fr')->filter()->values();
@endphp
@section('seo_title', content('brand.seo_title'))
@section('meta_description', content('brand.seo_description'))
@push('meta')
<meta property="og:title" content="{{ content('brand.og_title') }}">
<meta property="og:description" content="{{ content('brand.og_description') }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ route('automobiles') }}">
@endpush

@section('content')
@include('site.editorial.hero', [
  'image' => content('brand.hero_image'), 'alt' => content('brand.hero_aria'), 'crumbs' => content('brand.hero_brand'),
  'kicker' => content('brand.hero_kicker'), 'title' => trim(content('brand.hero_title1').' '.content('brand.hero_title2')),
  'text' => trim(content('brand.hero_text1').' '.content('brand.hero_brand').' '.content('brand.hero_text2')),
  'actions' => [[content('brand.btn_discover'), content('brand.discover_url'), 'stage'], [content('brand.btn_contact'), content('brand.contact_url'), 'gold']],
])

@if($stats->isNotEmpty())
<section class="kp-wrap kp-editorial-stats" aria-label="Chiffres clés" data-kp-stats>
  @foreach($stats as $stat)
    <div class="kp-kpi"><span class="kp-kpi__value">{{ $stat['value'] }}</span><span class="kp-kpi__label">{{ $stat['fr'] ?? '' }}</span></div>
  @endforeach
</section>
@endif

@include('site.editorial.split', [
  'id' => 'histoire', 'eyebrow' => content('brand.story_label'), 'title' => trim(content('brand.story_title1').' '.content('brand.story_title2')),
  'blocks' => [[content('brand.origin_title'), content('brand.origin_text')], [content('brand.mission_title'), content('brand.mission_text')]],
  'image' => content('brand.founder_image'), 'alt' => content('brand.founder_alt'),
  'quote' => content('brand.quote'), 'quoteBy' => content('brand.quote_by'),
])

@if($marquee->isNotEmpty())
<section class="kp-home-banner" aria-label="Nos savoir-faire">
  <div class="kp-wrap kp-home-banner__inner"><ul class="kp-home-banner__items">@foreach($marquee as $item)<li>{{ $item }}</li>@endforeach</ul></div>
</section>
@endif

@include('site.editorial.tiles', [
  'id' => 'engagements', 'eyebrow' => content('brand.pil_label'), 'title' => content('brand.pil_title'), 'text' => content('brand.pil_text'),
  'items' => collect(content('brand.pillars'))->map(fn ($pillar) => ['title' => $pillar['title'] ?? '', 'text' => $pillar['body'] ?? ''])->all(),
])

@include('site.editorial.tiles', [
  'id' => 'trajectoire', 'alt' => true, 'eyebrow' => content('brand.jr_label'), 'title' => content('brand.jr_title'), 'text' => content('brand.jr_text'),
  'items' => collect(content('brand.journey'))->map(fn ($step) => ['tag' => $step['year'] ?? '', 'title' => $step['title'] ?? '', 'text' => $step['body'] ?? ''])->all(),
])

@include('site.editorial.gallery', [
  'eyebrow' => content('brand.gal_label'), 'title' => content('brand.gal_title'), 'text' => content('brand.gal_text'), 'items' => content('brand.gallery'),
])

@include('site.editorial.cta', [
  'image' => content('brand.cta_image'), 'kicker' => content('brand.cta_kicker'),
  'title' => trim(content('brand.cta_title1').' '.content('brand.cta_title2')), 'text' => content('brand.cta_text'),
  'actions' => [[content('brand.cta_btn_models'), content('brand.cta_models_url'), 'stage'], [content('brand.cta_btn_contact'), content('brand.cta_contact_url'), 'gold']],
  'badges' => collect(content('brand.cta_badges'))->pluck('fr')->all(),
])
@endsection
