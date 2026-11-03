{{-- ECO KPANDJI (design system). Textes et images : Contenus › Page Eco-Kpandji (eco.*). --}}
@extends('layouts.site')
@section('seo_title', content('eco.seo_title'))
@section('meta_description', content('eco.seo_description'))
@push('meta')
<meta property="og:title" content="{{ content('eco.og_title') }}">
<meta property="og:description" content="{{ content('eco.og_description') }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ route('ecologie') }}">
@endpush

@section('content')
@include('site.editorial.hero', [
  'image' => content('eco.hero_image'), 'alt' => content('eco.hero_alt'), 'crumbs' => content('eco.hero_kicker'),
  'kicker' => content('eco.hero_kicker'), 'title' => trim(content('eco.hero_title1').' '.content('eco.hero_title2')), 'text' => content('eco.hero_text'),
  'actions' => [[content('eco.btn_commit'), content('eco.btn_commit_url'), 'gold'], [content('eco.btn_practices'), content('eco.btn_practices_url'), 'stage']],
])

@include('site.editorial.split', [
  'id' => 'engagements', 'eyebrow' => content('eco.prio_label'), 'title' => content('eco.prio_title'),
  'list' => collect(content('eco.commitments'))->pluck('fr')->all(), 'image' => content('eco.prio_image'), 'alt' => content('eco.prio_aria'),
])

@php($practices = collect(content('eco.marquee'))->pluck('fr')->filter()->values())
@if($practices->isNotEmpty())
<section class="kp-home-banner" id="pratiques" aria-label="{{ content('eco.mq_aria') }}" data-kp-practices>
  <div class="kp-wrap kp-home-banner__inner">
    <div class="kp-section__intro">
      <span class="kp-eyebrow kp-on-stage-gold">{{ content('eco.mq_aria') }}</span>
      <ul class="kp-home-banner__items">@foreach($practices as $practice)<li>{{ $practice }}</li>@endforeach</ul>
    </div>
  </div>
</section>
@endif

@include('site.editorial.tiles', [
  'id' => 'leviers', 'eyebrow' => content('eco.lev_aria'),
  'items' => collect(content('eco.levers'))->map(fn ($lever) => ['tag' => $lever['tag'] ?? '', 'title' => $lever['title'] ?? '', 'text' => filled($lever['url'] ?? null) ? content('eco.lev_more') : '', 'url' => $lever['url'] ?? ''])->all(),
])

@include('site.editorial.gallery', [
  'eyebrow' => content('eco.gal_label'), 'title' => content('eco.gal_title'), 'text' => content('eco.gal_text'), 'items' => content('eco.gallery'),
])

@include('site.editorial.cta', [
  'image' => content('eco.cta_image'), 'kicker' => content('eco.cta_kicker'),
  'title' => trim(content('eco.cta_title1').' '.content('eco.cta_title2')), 'text' => content('eco.cta_text'),
  'actions' => [[content('eco.cta_btn1'), content('eco.cta_btn1_url'), 'gold'], [content('eco.cta_btn2'), content('eco.cta_btn2_url'), 'stage'], [content('eco.cta_btn3'), content('eco.cta_btn3_url'), 'stage']],
  'badges' => collect(content('eco.cta_badges'))->pluck('fr')->all(),
])
@endsection
