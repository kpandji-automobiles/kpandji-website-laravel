{{-- Kpandji Sira (design system). Envoi JSON vers SiraController@store ; textes : Contenus › Kpandji Sira (sira.*). --}}
@extends('layouts.site')
@php
  $tiers = collect(content('sira.tiers'))->filter(fn ($tier) => ($tier['visible'] ?? '1') !== '0' && filled($tier['name'] ?? null))->values();
  $brief = collect(content('sira.brief'))->filter(fn ($item) => filled($item['q'] ?? null))->values();
@endphp
@section('seo_title', content('sira.seo_title'))
@section('meta_description', content('sira.seo_description'))
@push('meta')
<meta property="og:title" content="{{ content('sira.og_title') }}">
<meta property="og:description" content="{{ content('sira.og_description') }}">
@endpush

@section('content')
@include('site.editorial.hero', [
  'image' => content('sira.hero_image'), 'crumbs' => trim(content('sira.hero_brand').' '.content('sira.hero_name')),
  'kicker' => content('sira.hero_eyebrow'), 'title' => trim(content('sira.hero_brand').' '.content('sira.hero_name')), 'text' => content('sira.hero_text'),
  'actions' => [[content('sira.cta'), '#inscription', 'gold'], [content('sira.cta_tiers'), '#niveaux', 'stage']],
])

@if($brief->isNotEmpty())
<section class="kp-wrap kp-section" aria-labelledby="kp-sira-brief">
  <div class="kp-section__intro"><span class="kp-eyebrow">{{ content('sira.brief_eyebrow') }}</span><h2 class="heading-l" id="kp-sira-brief">{{ content('sira.brief_title') }}</h2></div>
  <dl class="kp-sira-brief">
    @foreach($brief as $item)<div class="kp-kpi"><dt class="kp-kpi__label">{{ $item['q'] }}</dt><dd class="body">{{ $item['a'] ?? '' }}</dd></div>@endforeach
  </dl>
</section>
@endif

@include('site.editorial.tiles', [
  'alt' => true, 'eyebrow' => content('sira.how_eyebrow'), 'title' => content('sira.how_title'),
  'items' => collect(content('sira.steps'))->map(fn ($step) => ['tag' => $step['n'] ?? '', 'title' => $step['title'] ?? '', 'text' => $step['body'] ?? ''])->all(),
])

@if($tiers->isNotEmpty())
<section class="kp-wrap kp-section" id="niveaux" aria-labelledby="kp-sira-tiers" data-kp-tiers>
  <div class="kp-section__intro">
    <span class="kp-eyebrow">{{ content('sira.tiers_eyebrow') }}</span>
    <h2 class="heading-l" id="kp-sira-tiers">{{ content('sira.tiers_title') }}</h2>
    @if(filled(content('sira.tiers_text')))<p class="body-l kp-muted kp-editorial__lead">{{ content('sira.tiers_text') }}</p>@endif
  </div>
  <div class="kp-home-offers__grid">
    @foreach($tiers as $tier)
      <article class="kp-vcard">
        <div class="kp-vcard__media">@if(filled($tier['image'] ?? null))<img src="{{ cms_asset($tier['image']) }}" alt="{{ content('sira.model_alt') }} {{ $tier['model'] ?? '' }}" loading="lazy">@endif<span class="kp-vcard__badge">{{ $tier['name'] }}</span></div>
        <div class="kp-vcard__body">
          <span class="kp-eyebrow">{{ $tier['model'] ?? '' }}</span>
          <h3 class="kp-vcard__name">{{ $tier['full'] ?? $tier['name'] }}</h3>
          @if(filled($tier['blurb'] ?? null))<p class="body-s kp-muted">{{ $tier['blurb'] }}</p>@endif
          @if(filled($tier['min'] ?? null))<div class="kp-price"><span class="kp-price__label">{{ content('sira.minimum') }}</span><span class="kp-price__value">{{ $tier['min'] }} {{ content('sira.vehicles') }}</span></div>@endif
          <div class="kp-vcard__actions">
            <a class="kp-btn kp-btn--primary kp-btn--sm" href="#inscription">{{ content('sira.cta') }}</a>
            @if(filled($tier['brochure'] ?? null))<a class="kp-btn kp-btn--secondary kp-btn--sm" href="{{ cms_asset($tier['brochure']) }}" target="_blank" rel="noopener">{{ content('sira.brochure_btn') }}</a>@endif
          </div>
        </div>
      </article>
    @endforeach
  </div>
</section>
@endif

@include('site.editorial.tiles', [
  'alt' => true, 'eyebrow' => content('sira.inc_eyebrow'), 'title' => content('sira.inc_title'),
  'items' => collect(content('sira.included'))->map(fn ($item) => ['title' => $item['title'] ?? '', 'text' => $item['body'] ?? ''])->all(),
])

<div class="kp-wrap kp-form-page" id="inscription" data-kp-sira-form>
  <div class="kp-section__intro">
    <span class="kp-eyebrow">{{ content('sira.f_kicker') }}</span>
    <h2 class="heading-l">{{ content('sira.final_title') }}</h2>
    <p class="body kp-muted">{{ content('sira.f_intro') ?: content('sira.final_text') }}</p>
    <x-kp.ajax-form :action="route('sira.store')" :submit="content('sira.f_submit')" :sending="content('sira.f_sending')"
      :ok-title="content('sira.f_ok_title')" :ok-text="content('sira.f_ok_text')" :network="content('sira.e_network')" :privacy="content('sira.f_privacy')">
      @include('site.registration-fields', ['prefix' => 'sira'])
    </x-kp.ajax-form>
  </div>
  <aside class="kp-form-page__aside kp-budget">
    <p class="body">{{ content('sira.final_text') }}</p>
    <a class="kp-btn kp-btn--secondary" href="{{ cms_url(content('sira.advisor_url')) }}">{{ content('sira.advisor') }}</a>
  </aside>
</div>
@endsection
