{{-- Emplois / candidature (design system). Envoi JSON multipart vers EmploisController@store ; textes : Contenus › Page Emplois (emp.*). --}}
@extends('layouts.site')
@section('seo_title', content('emp.seo_title'))
@section('meta_description', content('emp.seo_description'))
@push('meta')
<meta property="og:title" content="{{ content('emp.og_title') }}">
<meta property="og:description" content="{{ content('emp.og_description') }}">
@endpush

@section('content')
@include('site.editorial.hero', [
  'image' => content('emp.hero_image'), 'crumbs' => 'Emplois', 'kicker' => content('emp.hero_kicker'),
  'title' => content('emp.hero_title'), 'text' => content('emp.hero_text'),
  'actions' => [[content('emp.f_title'), '#candidature', 'gold']],
])

@include('site.editorial.tiles', [
  'eyebrow' => content('emp.tips_label'),
  'items' => collect(content('emp.tips'))->map(fn ($tip) => ['title' => $tip['title'] ?? '', 'text' => $tip['body'] ?? ''])->all(),
])

<div class="kp-wrap kp-form-page" id="candidature" data-kp-emplois-form>
  <div class="kp-section__intro">
    <span class="kp-eyebrow">{{ content('emp.f_kicker') }}</span>
    <h2 class="heading-l">{{ content('emp.f_title') }}</h2>
    <p class="body kp-muted">{{ content('emp.f_intro') }}</p>
    <x-kp.ajax-form :action="route('emplois.store')" files :submit="content('emp.f_submit')" :sending="content('emp.f_sending')"
      :ok-title="content('emp.f_ok_title')" :ok-text="content('emp.f_ok_text')" :network="content('emp.e_network')">
      <div class="kp-form__honeypot" aria-hidden="true"><label>Site web <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
      <fieldset class="kp-form__grid">
        <legend class="kp-price__label">{{ content('emp.f_sec_identity') }}</legend>
        <x-kp.field name="full_name" :label="content('emp.f_name')" required max="120" :placeholder="content('emp.f_name_ph')" />
        <x-kp.field name="email" type="email" :label="content('emp.f_email')" required max="160" :placeholder="content('emp.f_email_ph')" />
        <x-kp.field name="phone" type="tel" :label="content('emp.f_phone')" max="40" :placeholder="content('emp.f_phone_ph')" />
      </fieldset>
      <fieldset class="kp-form__grid">
        <legend class="kp-price__label">{{ content('emp.f_sec_position') }}</legend>
        <x-kp.field class="kp-form__wide" name="position" :label="content('emp.f_position')" required max="200" :placeholder="content('emp.f_position_ph')" />
      </fieldset>
      <fieldset class="kp-form__grid">
        <legend class="kp-price__label">{{ content('emp.f_sec_cv') }}</legend>
        <x-kp.field class="kp-form__wide" name="cv_summary" type="textarea" :label="content('emp.f_cv')" required max="6000" :placeholder="content('emp.f_cv_ph')" />
        <x-kp.field class="kp-form__wide" name="cv" type="file" :label="content('emp.f_file')" accept=".pdf,.doc,.docx" :help="content('emp.f_file_hint')" />
        <x-kp.field class="kp-form__wide" name="education" type="textarea" :label="content('emp.f_education')" max="3000" :placeholder="content('emp.f_education_ph')" />
      </fieldset>
      <fieldset class="kp-form__grid">
        <legend class="kp-price__label">{{ content('emp.f_sec_skills') }}</legend>
        <x-kp.field class="kp-form__wide" name="skills" type="textarea" :label="content('emp.f_skills')" required max="4000" :placeholder="content('emp.f_skills_ph')" />
        <x-kp.field name="languages" :label="content('emp.f_languages')" max="300" :placeholder="content('emp.f_languages_ph')" />
        <x-kp.field name="extras" :label="content('emp.f_extras')" max="3000" :placeholder="content('emp.f_extras_ph')" />
      </fieldset>
    </x-kp.ajax-form>
  </div>
  <aside class="kp-form-page__aside kp-budget">
    @if(filled(content('emp.f_dest_email')))
      <span class="kp-price__label">{{ content('emp.f_dest_label') }}</span>
      <a class="kp-form-page__contact" href="mailto:{{ content('emp.f_dest_email') }}">{{ content('emp.f_dest_email') }}</a>
      <p class="body-s kp-muted">{{ content('emp.f_dest_hint') }}</p>
    @endif
    <p class="heading-s">{{ content('emp.help_title') }}</p>
    <p class="body-s kp-muted">{{ content('emp.help_text') }}</p>
    @if(filled(content('emp.help_btn')))<a class="kp-btn kp-btn--secondary" href="{{ cms_url(content('emp.help_url')) }}">{{ content('emp.help_btn') }}</a>@endif
  </aside>
</div>
@endsection
