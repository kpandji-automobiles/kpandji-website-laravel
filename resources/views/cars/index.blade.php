@extends('layouts.app')
@section('title', content('showroom.page_title'))
@section('content')
<section class="relative mx-auto max-w-[1400px] px-5 pt-16 md:px-10">
  {!! cms_edit('home', 'Modifier les textes', '1rem') !!}
  @if(cms_is_admin())<div class="relative">{!! cms_edit(route('admin.cars.index'), 'Modifier les voitures', '0') !!}</div>@endif
  <span class="inline-flex rounded-full bg-kp-gold px-4 py-1.5 text-[10px] font-bold uppercase tracking-[0.3em] text-black">@content('showroom.badge')</span>
  <h1 class="mt-5 font-serif text-5xl font-semibold text-white md:text-6xl">@content('showroom.title')</h1>
  <p class="mt-4 max-w-2xl text-lg font-light text-white/65">@content('showroom.intro')</p>
  <div class="mt-12 grid gap-10">@foreach($cars as $car) @include('partials.card', ['i' => $loop->index % 2]) @endforeach</div>
</section>
@endsection
