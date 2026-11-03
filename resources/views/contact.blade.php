{{-- Demande d'essai / d'information (design system). Envoi classique vers SiteController@store ; textes : Contenus › Formulaire (form.*). --}}
@extends('layouts.site')
@section('title', content('form.page_title'))
@php
  $chosen = $cars->first(fn ($car) => (string) old('car_id') === (string) $car->id || $selected === $car->slug);
@endphp

@section('content')
<section class="kp-catalog-head">
  <div class="kp-wrap kp-section__intro kp-catalog-head__inner">
    <span class="kp-eyebrow">{{ content('form.eyebrow') }}</span>
    <h1 class="display-l">{{ content('form.title') }}</h1>
    @if(filled(content('form.intro')))<p class="body-l kp-muted kp-editorial__lead">{{ content('form.intro') }}</p>@endif
  </div>
</section>

<div class="kp-wrap kp-form-page" data-kp-essai>
  <div class="kp-form">
    @if(session('success'))
      <div class="kp-form__done" role="status"><p class="heading-s">{{ session('success') }}</p></div>
    @endif
    @if($errors->any())
      <div class="kp-login__error" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('contact.store') }}" class="kp-form__grid">
      @csrf
      <x-kp.field class="kp-form__wide" name="type" type="select" :label="content('form.type')" required value="essai" :options="['essai' => content('form.type_essai'), 'information' => content('form.type_info')]" :error="$errors->first('type')" />
      <x-kp.field class="kp-form__wide" name="car_id" type="select" :label="content('form.model')" :placeholder="content('form.model_choose')" :value="$chosen?->id" :options="$cars->pluck('name', 'id')->all()" :error="$errors->first('car_id')" />
      <x-kp.field name="name" :label="content('form.name')" required max="120" :error="$errors->first('name')" />
      <x-kp.field name="phone" type="tel" :label="content('form.phone')" required max="40" :error="$errors->first('phone')" />
      <x-kp.field name="email" type="email" :label="content('form.email')" max="160" :error="$errors->first('email')" />
      <x-kp.field name="preferred_date" type="date" :label="content('form.date')" :error="$errors->first('preferred_date')" />
      <x-kp.field class="kp-form__wide" name="message" type="textarea" :label="content('form.message')" max="2000" :error="$errors->first('message')" />
      <button class="kp-btn kp-btn--gold kp-form__wide">{{ content('form.submit') }}</button>
    </form>
  </div>
  @if($chosen)
    <aside class="kp-form-page__aside" aria-label="Modèle choisi">@include('site.vehicle-card', ['car' => $chosen])</aside>
  @endif
</div>
@endsection
