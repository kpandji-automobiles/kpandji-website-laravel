{{-- Contact (design system). Envoi JSON vers ContactController@store ; textes : Contenus › Page Contact (contactp.*). --}}
@extends('layouts.site')
@php
  $subjects = collect(content('contactp.subjects'))->pluck('fr')->filter()->values();
  $phones = collect(content('footer.phones'))->filter(fn ($phone) => filled($phone['number'] ?? null))->values();
@endphp
@section('seo_title', content('contactp.seo_title'))
@section('meta_description', content('contactp.seo_description'))
@push('meta')
<meta property="og:title" content="{{ content('contactp.og_title') }}">
<meta property="og:description" content="{{ content('contactp.og_description') }}">
@endpush

@section('content')
<section class="kp-catalog-head">
  <div class="kp-wrap kp-section__intro kp-catalog-head__inner">
    <span class="kp-eyebrow">Contact</span>
    <h1 class="display-l">{{ content('contactp.h1') }}</h1>
  </div>
</section>

<div class="kp-wrap kp-form-page" data-kp-contact>
  <div class="kp-section__intro">
    <span class="kp-eyebrow">{{ content('contactp.f_kicker') }}</span>
    <h2 class="heading-l">{{ content('contactp.f_title') }}</h2>
    <p class="body kp-muted">{{ content('contactp.f_intro') }}</p>
    <x-kp.ajax-form :action="route('contact.send')" :submit="content('contactp.f_submit')" :sending="content('contactp.f_sending')"
      :ok-title="content('contactp.f_ok_title')" :ok-text="content('contactp.f_ok_text')" :network="content('contactp.e_network')" :privacy="content('contactp.f_privacy')">
      <div class="kp-form__grid">
        <x-kp.field name="name" :label="content('contactp.f_name')" required max="120" />
        <x-kp.field name="email" type="email" :label="content('contactp.f_email')" required max="160" />
        <x-kp.field name="phone" type="tel" :label="content('contactp.f_phone')" max="40" />
        @if($subjects->isNotEmpty())
          <x-kp.field name="subject" type="select" :label="content('contactp.f_subject')" required :placeholder="content('contactp.f_subject_ph')" :options="$subjects->combine($subjects)->all()" />
        @else
          <x-kp.field name="subject" :label="content('contactp.f_subject')" required max="200" />
        @endif
        <x-kp.field class="kp-form__wide" name="message" type="textarea" :label="content('contactp.f_message')" required max="5000" :placeholder="content('contactp.f_message_ph')" />
      </div>
    </x-kp.ajax-form>
  </div>
  <aside class="kp-form-page__aside kp-budget" aria-label="{{ content('contactp.access_label') }}">
    <span class="kp-eyebrow">{{ content('contactp.access_label') }}</span>
    @foreach($phones as $phone)
      <a class="kp-form-page__contact" href="tel:{{ preg_replace('/[^\d+]/', '', $phone['number']) }}"><span class="kp-price__label">{{ $phone['label'] ?? '' }}</span>{{ $phone['number'] }}</a>
    @endforeach
    @if(filled(content('contactp.email')))
      <a class="kp-form-page__contact" href="{{ content('contactp.email_link') }}"><span class="kp-price__label">{{ content('contactp.email_label') }}</span>{{ content('contactp.email') }}</a>
    @endif
    <div class="kp-budget__result"><span class="kp-price__label">{{ content('contactp.hq_label') }}</span><strong>{{ content('contactp.hq_title') }}</strong>{{ content('contactp.hq_text') }}</div>
  </aside>
</div>
@endsection
