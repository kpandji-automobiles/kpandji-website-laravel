@extends('layouts.app')
@section('title', content('form.page_title'))
@section('content')
<section class="relative max-w-3xl mx-auto px-6 pt-16">
  {!! cms_edit('form', 'Modifier cette section', '1rem') !!}
  <p class="text-kp-gold uppercase tracking-[0.3em] text-sm">@content('form.eyebrow')</p>
  <h1 class="text-5xl font-extrabold text-white mt-2">@content('form.title')</h1>
  <p class="mt-4 text-kp-silver/70">@content('form.intro')</p>
  @if(session('success'))<div class="mt-8 border border-green-500/50 bg-green-500/10 text-green-300 p-4">{{ session('success') }}</div>@endif
  @if($errors->any())<div class="mt-8 border border-red-500/50 bg-red-500/10 text-red-300 p-4"><ul>@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul></div>@endif
  @php($in='w-full bg-kp-dark border border-white/15 px-4 py-3 text-white focus:border-kp-gold outline-none')
  <form method="POST" action="{{ route('contact.store') }}" class="mt-10 grid sm:grid-cols-2 gap-5">@csrf
    <label class="sm:col-span-2">@content('form.type')
      <select name="type" class="{{ $in }} mt-1"><option value="essai" @selected(old('type')!='information')>@content('form.type_essai')</option><option value="information" @selected(old('type')=='information')>@content('form.type_info')</option></select></label>
    <label class="sm:col-span-2">@content('form.model')
      <select name="car_id" class="{{ $in }} mt-1"><option value="">@content('form.model_choose')</option>@foreach($cars as $c)<option value="{{ $c->id }}" @selected(old('car_id') == $c->id || $selected === $c->slug)>{{ $c->name }}</option>@endforeach</select></label>
    <label>@content('form.name')<input name="name" value="{{ old('name') }}" required class="{{ $in }} mt-1"></label>
    <label>@content('form.phone')<input name="phone" value="{{ old('phone') }}" required class="{{ $in }} mt-1"></label>
    <label>@content('form.email')<input type="email" name="email" value="{{ old('email') }}" class="{{ $in }} mt-1"></label>
    <label>@content('form.date')<input type="date" name="preferred_date" value="{{ old('preferred_date') }}" class="{{ $in }} mt-1"></label>
    <label class="sm:col-span-2">@content('form.message')<textarea name="message" rows="4" class="{{ $in }} mt-1">{{ old('message') }}</textarea></label>
    <button class="sm:col-span-2 bg-kp-gold text-kp-black font-bold uppercase tracking-wider py-4 hover:bg-white transition">@content('form.submit')</button>
  </form>
</section>
@endsection
