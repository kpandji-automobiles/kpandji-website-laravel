{{-- Page libre créée dans le back-office (design system). Le corps HTML est filtré à l'enregistrement (App\Models\Page). --}}
@extends('layouts.site')
@section('title', $page->title)
@if($page->seo_title)@section('seo_title', $page->seo_title)@endif
@if($page->seo_description)@section('meta_description', $page->seo_description)@endif

@section('content')
@if($page->hero_image)
  @include('site.editorial.hero', ['image' => $page->hero_image, 'crumbs' => $page->title, 'kicker' => $page->kicker, 'title' => $page->title, 'text' => $page->intro])
@else
  <section class="kp-catalog-head">
    <div class="kp-wrap kp-section__intro kp-catalog-head__inner">
      @if(filled($page->kicker))<span class="kp-eyebrow">{{ $page->kicker }}</span>@endif
      <h1 class="display-l">{{ $page->title }}</h1>
      @if(filled($page->intro))<p class="body-l kp-muted kp-editorial__lead">{{ $page->intro }}</p>@endif
    </div>
  </section>
@endif

<section class="kp-wrap kp-section" data-kp-page="{{ $page->slug }}">
  @if($page->body)
    <div class="kp-prose">{!! $page->body !!}</div>
  @else
    <p class="body-s kp-muted">{{ content('page.draft_note') }}</p>
  @endif
  <div class="kp-home-hero__actions">
    <a class="kp-btn kp-btn--secondary" href="{{ route('cars.index') }}">{{ content('page.btn_models') }}</a>
    <a class="kp-btn kp-btn--gold" href="{{ route('essai') }}">{{ content('page.btn_essai') }}</a>
  </div>
</section>
@endsection
