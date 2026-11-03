{{-- Kpandji Privilège (design system). Envoi JSON vers PrivilegeController@store (source advisor ou contact) ; textes : Contenus › Kpandji Privilège (priv.*). --}}
@extends('layouts.site')
@php
  $paths = collect(content('priv.paths'))->filter(fn ($path) => filled($path['title'] ?? null))->values();
  $points = [collect(content('priv.points1'))->pluck('fr')->filter()->values(), collect(content('priv.points2'))->pluck('fr')->filter()->values()];
  $whatsapp = preg_replace('/\D/', '', (string) content('priv.wa_number'));
@endphp
@section('seo_title', content('priv.seo_title'))
@section('meta_description', content('priv.seo_description'))
@push('meta')
<meta property="og:title" content="{{ content('priv.og_title') }}">
<meta property="og:description" content="{{ content('priv.og_description') }}">
@endpush

@section('content')
@include('site.editorial.hero', [
  'image' => content('priv.hero_image'), 'alt' => content('priv.hero_alt'), 'crumbs' => trim(content('priv.hero_brand').' '.content('priv.hero_name')),
  'kicker' => content('priv.hero_eyebrow'), 'title' => trim(content('priv.hero_brand').' '.content('priv.hero_name')), 'text' => content('priv.hero_text'),
  'actions' => [[content('priv.a_label'), '#demande', 'gold'], [content('priv.paths_eyebrow'), '#parcours', 'stage']],
])

@if($paths->isNotEmpty())
<section class="kp-wrap kp-section" id="parcours" aria-labelledby="kp-priv-paths" data-kp-paths>
  <div class="kp-section__intro">
    <span class="kp-eyebrow">{{ content('priv.paths_eyebrow') }}</span>
    <h2 class="heading-l" id="kp-priv-paths">{{ trim(content('priv.paths_title1').' '.content('priv.paths_title2')) }}</h2>
    @if(filled(content('priv.paths_text')))<p class="body-l kp-muted kp-editorial__lead">{{ content('priv.paths_text') }}</p>@endif
  </div>
  <div class="kp-home-offers__grid">
    @foreach($paths as $path)
      <article class="kp-vcard">
        <div class="kp-vcard__media">@if(filled($path['image'] ?? null))<img src="{{ cms_asset($path['image']) }}" alt="" loading="lazy">@endif<span class="kp-vcard__badge">{{ $path['option'] ?? '' }}</span></div>
        <div class="kp-vcard__body">
          <h3 class="kp-vcard__name">{{ $path['title'] }}</h3>
          @if(filled($path['body'] ?? null))<p class="body-s kp-muted">{{ $path['body'] }}</p>@endif
          @if(($points[$loop->index] ?? collect())->isNotEmpty())<ul class="kp-vcard__specs">@foreach($points[$loop->index] as $point)<li>{{ $point }}</li>@endforeach</ul>@endif
          <div class="kp-priv-figures">
            @if(filled($path['highlight'] ?? null))<div class="kp-price"><span class="kp-price__label">{{ $path['highlight_label'] ?? '' }}</span><span class="kp-price__value">{{ $path['highlight'] }}</span></div>@endif
            @if(filled($path['secondary'] ?? null))<div class="kp-price"><span class="kp-price__label">{{ $path['secondary_label'] ?? '' }}</span><span class="kp-price__value">{{ $path['secondary'] }}</span></div>@endif
          </div>
        </div>
      </article>
    @endforeach
  </div>
</section>
@endif

@include('site.editorial.tiles', [
  'alt' => true, 'eyebrow' => content('priv.steps_eyebrow'), 'title' => content('priv.steps_title'),
  'items' => collect(content('priv.steps'))->map(fn ($step) => ['tag' => $step['n'] ?? '', 'title' => $step['title'] ?? '', 'text' => $step['body'] ?? ''])->all(),
])

<div class="kp-wrap kp-form-page" id="demande" x-data="{ mode: 'advisor' }" data-kp-priv-form>
  <div class="kp-section__intro">
    <span class="kp-eyebrow">{{ content('priv.f_kicker') }}</span>
    <div class="kp-chips" role="group" aria-label="Type de demande">
      <button type="button" class="kp-chip" :aria-pressed="mode === 'advisor'" @click="mode = 'advisor'">{{ content('priv.a_label') }}</button>
      <button type="button" class="kp-chip" :aria-pressed="mode === 'contact'" @click="mode = 'contact'">{{ content('priv.c_label') }}</button>
    </div>
    @foreach(['advisor' => 'a', 'contact' => 'c'] as $source => $key)
      <div x-show="mode === @js($source)" @if($source !== 'advisor') x-cloak @endif data-kp-priv-source="{{ $source }}">
        <h2 class="heading-l">{{ content('priv.'.$key.'_title') }}</h2>
        <p class="body kp-muted">{{ content('priv.'.$key.'_sub') }}</p>
        <x-kp.ajax-form :action="route('privilege.store')" :submit="content('priv.'.$key.'_submit')" :sending="content('priv.f_sending')"
          :ok-title="content('priv.'.$key.'_ok_title')" :ok-text="content('priv.'.$key.'_ok_text')" :network="content('priv.e_network')" :privacy="content('priv.f_privacy')">
          <input type="hidden" name="source" value="{{ $source }}">
          @include('site.registration-fields', ['prefix' => 'priv'])
        </x-kp.ajax-form>
      </div>
    @endforeach
  </div>
  <aside class="kp-form-page__aside kp-budget" aria-label="{{ content('priv.contact_title') }}">
    <span class="kp-eyebrow">{{ content('priv.contact_eyebrow') }}</span>
    <p class="heading-s">{{ content('priv.contact_title') }}</p>
    @if(filled(content('priv.contact_text')))<p class="body-s kp-muted">{{ content('priv.contact_text') }}</p>@endif
    @if(filled(content('priv.phone')))<a class="kp-form-page__contact" href="{{ content('priv.phone_link') }}">{{ content('priv.phone') }}</a>@endif
    @if(filled(content('priv.email')))<a class="kp-form-page__contact" href="{{ content('priv.email_link') }}">{{ content('priv.email') }}</a>@endif
    @if($whatsapp !== '')<a class="kp-btn kp-btn--secondary" href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener">{{ content('priv.wa_title') }}</a>@endif
    @if(filled(content('priv.tagline')))<p class="body-s kp-muted">{{ content('priv.tagline') }}</p>@endif
  </aside>
</div>
@endsection
