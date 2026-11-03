{{-- Service après-vente (design system). Envoi JSON vers SavController@store ; textes : Contenus › Page S.A.V. (savp.*). --}}
@extends('layouts.site')
@php
  $stats = collect(content('savp.stats'))->filter(fn ($stat) => filled($stat['value'] ?? null))->values();
  $marquee = collect(content('savp.marquee'))->pluck('fr')->filter()->values();
  $modelOptions = $models->filter()->combine($models->filter())->put(content('savp.f_other'), content('savp.f_other'))->all();
@endphp
@section('seo_title', content('savp.seo_title'))
@section('meta_description', content('savp.seo_description'))
@push('meta')
<meta property="og:title" content="{{ content('savp.og_title') }}">
<meta property="og:description" content="{{ content('savp.og_description') }}">
@endpush

@section('content')
@include('site.editorial.hero', [
  'image' => content('savp.hero_image'), 'crumbs' => 'S.A.V.', 'kicker' => content('savp.hero_kicker'),
  'title' => content('savp.hero_title'), 'text' => content('savp.hero_text'),
  'actions' => [[content('savp.btn_call'), content('savp.phone_link'), 'gold'], [content('savp.btn_write'), '#ecrire', 'stage']],
])

@if($stats->isNotEmpty())
<section class="kp-wrap kp-editorial-stats" aria-label="Chiffres clés">
  @foreach($stats as $stat)<div class="kp-kpi"><span class="kp-kpi__value">{{ $stat['value'] }}</span><span class="kp-kpi__label">{{ $stat['fr'] ?? '' }}</span></div>@endforeach
</section>
@endif

@include('site.editorial.tiles', [
  'eyebrow' => content('savp.pil_label'), 'title' => trim(content('savp.pil_title1').' '.content('savp.pil_title2')), 'text' => content('savp.pil_text'),
  'items' => collect(content('savp.pillars'))->map(fn ($pillar) => ['title' => $pillar['title'] ?? '', 'text' => $pillar['body'] ?? ''])->all(),
])

@if($marquee->isNotEmpty())
<section class="kp-home-banner" aria-label="Prestations"><div class="kp-wrap kp-home-banner__inner"><ul class="kp-home-banner__items">@foreach($marquee as $item)<li>{{ $item }}</li>@endforeach</ul></div></section>
@endif

@include('site.editorial.tiles', [
  'id' => 'services', 'eyebrow' => content('savp.srv_label'), 'title' => content('savp.srv_title'), 'text' => content('savp.srv_text'),
  'items' => collect(content('savp.services'))->map(fn ($service) => ['title' => $service['title'] ?? '', 'text' => $service['body'] ?? ''])->all(),
])

@include('site.editorial.tiles', [
  'id' => 'processus', 'alt' => true, 'eyebrow' => content('savp.proc_label'), 'title' => content('savp.proc_title'), 'text' => content('savp.proc_text'),
  'items' => collect(content('savp.steps'))->map(fn ($step) => ['tag' => $step['n'] ?? '', 'title' => $step['title'] ?? '', 'text' => $step['body'] ?? ''])->all(),
])

@include('site.editorial.gallery', [
  'eyebrow' => content('savp.ws_label'), 'title' => content('savp.ws_title'), 'text' => content('savp.ws_text'), 'items' => content('savp.gallery'),
])

<div class="kp-wrap kp-form-page" id="ecrire" data-kp-sav-form>
  <div class="kp-section__intro">
    <span class="kp-eyebrow">{{ content('savp.f_kicker') }}</span>
    <h2 class="heading-l">{{ content('savp.btn_write') }}</h2>
    <p class="body kp-muted">{{ content('savp.f_intro') }}</p>
    <x-kp.ajax-form :action="route('sav.store')" :submit="content('savp.f_submit')" :sending="content('savp.f_sending')"
      :ok-title="content('savp.f_ok_title')" :ok-text="content('savp.f_ok_text')" :network="content('savp.e_network')" :privacy="content('savp.f_privacy')">
      <div class="kp-form__grid">
        <x-kp.field name="name" :label="content('savp.f_name')" required max="120" />
        <x-kp.field name="contact" :label="content('savp.f_contact')" required max="160" :placeholder="content('savp.f_contact_ph')" />
        <x-kp.field name="model" type="select" :label="content('savp.f_model')" required :placeholder="content('savp.f_model_ph')" :options="$modelOptions" />
        <x-kp.field name="location" :label="content('savp.f_loc')" required max="160" :placeholder="content('savp.f_loc_ph')" />
        <x-kp.field class="kp-form__wide" name="issue" type="textarea" :label="content('savp.f_issue')" required max="3000" :placeholder="content('savp.f_issue_ph')" />
      </div>
    </x-kp.ajax-form>
  </div>
  <aside class="kp-form-page__aside kp-budget" aria-label="{{ content('savp.aside_title') }}">
    <span class="kp-eyebrow">{{ content('savp.aside_kicker') }}</span>
    <p class="heading-s">{{ content('savp.aside_title') }}</p>
    @if(filled(content('savp.phone')))<a class="kp-form-page__contact" href="{{ content('savp.phone_link') }}"><span class="kp-price__label">{{ content('savp.phone_label') }}</span>{{ content('savp.phone') }}</a>@endif
    @if(filled(content('savp.email')))<a class="kp-form-page__contact" href="{{ content('savp.email_link') }}"><span class="kp-price__label">{{ content('savp.email_label') }}</span>{{ content('savp.email') }}</a>@endif
  </aside>
</div>

@include('site.editorial.cta', [
  'image' => content('savp.cta_image'), 'kicker' => content('savp.cta_kicker'),
  'title' => trim(content('savp.cta_title1').' '.content('savp.cta_title2')), 'text' => content('savp.cta_text'),
  'actions' => [[content('savp.btn_write'), '#ecrire', 'gold'], [content('savp.btn_contact'), content('savp.contact_url'), 'stage']],
  'badges' => collect(content('savp.badges'))->pluck('fr')->all(),
])
@endsection
