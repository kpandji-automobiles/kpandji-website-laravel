@extends('layouts.app')
@section('title', $page->title)
@if($page->seo_title)@section('seo_title', $page->seo_title)@endif
@if($page->seo_description)@section('meta_description', $page->seo_description)@endif
@section('content')
@if($page->hero_image)
<div class="relative h-[38vh] min-h-[260px] w-full overflow-hidden"><img src="{{ cms_asset($page->hero_image) }}" alt="" class="h-full w-full object-cover"><div class="absolute inset-0 bg-gradient-to-b from-black/30 to-kp-black"></div></div>
@endif
<section class="max-w-4xl mx-auto px-6 pt-20 pb-10 text-center relative">
  {!! cms_edit(route('admin.pages.edit', $page), 'Modifier cette page', '1rem') !!}
  <p class="text-kp-gold uppercase tracking-[0.3em] text-sm">{{ $page->kicker }}</p>
  <h1 class="mt-3 font-serif text-5xl font-semibold text-white">{{ $page->title }}</h1>
  <p class="mt-6 text-lg text-kp-sand/80">{{ $page->intro }}</p>
  @if($page->body)<div class="kp-prose mt-10 text-left text-base leading-relaxed text-white/75">{!! $page->body !!}</div>@endif
  <div class="mt-10 flex flex-wrap justify-center gap-3">
    <a href="{{ route('cars.index') }}" class="rounded-full border border-white/20 px-6 py-3 text-xs font-semibold uppercase tracking-[0.22em] text-white hover:border-white/40">@content('page.btn_models')</a>
    <a href="{{ route('essai') }}" class="rounded-full bg-white px-6 py-3 text-xs font-semibold uppercase tracking-[0.22em] text-black hover:bg-white/90">@content('page.btn_essai')</a>
  </div>
  @unless($page->body)<p class="mt-12 text-xs text-white/30">@content('page.draft_note')</p>@endunless
</section>
@endsection
