{{-- Emplois / Candidature — port de app/emplois/page.tsx + components/kp/EmploisPageContent.tsx (+ EmploisPageHero, JobApplicationForm, ContactPageDecor, Reveal).
     Contenu : Admin › Emplois (/admin/emplois) — clés CMS « emp.* » ; candidatures : table job_applications (/admin/emplois/candidatures).
     La source ouvrait seulement un brouillon mailto : ici la candidature (et le CV) est enregistrée en base, le brouillon e-mail reste proposé ensuite. --}}
@extends('layouts.app')
@php($en = app()->getLocale() === 'en')
@php($c = fn ($k) => $en ? (content('emp.'.$k.'_en') ?: content('emp.'.$k)) : content('emp.'.$k))
@php($li = fn ($row, $f) => $en ? (($row[$f.'_en'] ?? '') ?: ($row[$f] ?? '')) : ($row[$f] ?? ''))
@php($fe = fn ($row) => $en ? (($row['en'] ?? '') ?: ($row['fr'] ?? '')) : ($row['fr'] ?? ''))
@php($chips = collect(content('emp.chips'))->map($fe)->filter()->values())
@php($tips = collect(content('emp.tips'))->filter(fn ($t) => filled($t['title'] ?? null) || filled($t['body'] ?? null))->values())
@php($okNoJs = session('emp_ok'))
@php($input = 'w-full rounded-xl border border-white/[0.11] bg-black/40 px-4 py-3.5 font-sans text-[15px] text-kp-accent shadow-[inset_0_1px_0_rgba(255,255,255,0.04)] placeholder:text-white/[0.28] transition-[border-color,box-shadow] duration-200 focus:border-kp-gold/45 focus:outline-none focus:shadow-[inset_0_1px_0_rgba(255,255,255,0.05),0_0_0_1px_rgba(201,169,98,0.12)]')
@php($label = 'mb-2 block text-left text-[10px] font-semibold uppercase tracking-[0.22em] text-white/[0.38]')
@php($opt = '<span class="ml-1.5 font-normal normal-case tracking-normal text-white/[0.22]">'.e($c('f_optional')).'</span>')
@php($req = ' <span class="text-kp-gold/90">*</span>')
@php($sect = fn ($t) => '<div class="mb-5 flex items-center gap-3"><span class="h-px w-8 shrink-0 bg-gradient-to-r from-kp-gold/80 to-kp-gold/15" aria-hidden="true"></span><h2 class="font-sans text-[10px] font-semibold uppercase tracking-[0.3em] text-white/45">'.e($t).'</h2></div>')
@php($card = 'group relative overflow-hidden rounded-2xl border border-white/[0.07] bg-kp-elevated/30 p-5 shadow-[0_24px_80px_-40px_rgba(0,0,0,0.9)] backdrop-blur-md transition-colors duration-500 hover:border-white/[0.12] hover:bg-kp-elevated/40 sm:p-6')
@php($icons = [
  'paper' => '<svg class="opacity-90" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>',
  'spark' => '<svg class="opacity-90" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3-1.9 5.9H4l5 3.8L7.1 21 12 16.9 16.9 21 15 12.7l5-3.8h-6.1L12 3Z"/></svg>',
])
@section('seo_title', content('emp.seo_title'))
@section('meta_description', content('emp.seo_description'))
@push('meta')
<link rel="canonical" href="{{ url('/emplois') }}">
<meta property="og:title" content="{{ content('emp.og_title') }}">
<meta property="og:description" content="{{ content('emp.og_description') }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="fr_FR">
<meta property="og:url" content="{{ url('/emplois') }}">
<link rel="preload" as="image" href="{{ cms_asset(content('emp.hero_image')) }}" fetchpriority="high">
@endpush
@section('content')
<style>
[data-kp-emp-rv]{opacity:0;transform:translateY(28px);transition:opacity .8s cubic-bezier(.22,1,.36,1),transform .8s cubic-bezier(.22,1,.36,1)}
[data-kp-emp-rv].is-in{opacity:1;transform:none}
@keyframes kp-contact-orb-1{0%,100%{transform:translate3d(0,0,0);opacity:.55}50%{transform:translate3d(12px,20px,0);opacity:.75}}
@keyframes kp-contact-orb-2{0%,100%{transform:translate3d(0,0,0);opacity:.4}50%{transform:translate3d(-10px,-18px,0);opacity:.6}}
@keyframes kp-contact-orb-3{0%,100%{transform:translate3d(-50%,33.333%,0) scale(1);opacity:.35}50%{transform:translate3d(-50%,33.333%,0) scale(1.06);opacity:.5}}
.kp-contact-orb-1{animation:kp-contact-orb-1 11s cubic-bezier(.22,1,.36,1) infinite alternate}
.kp-contact-orb-2{animation:kp-contact-orb-2 12.5s cubic-bezier(.22,1,.36,1) infinite alternate}
.kp-contact-orb-3{transform:translate3d(-50%,33.333%,0);animation:kp-contact-orb-3 14s cubic-bezier(.22,1,.36,1) infinite alternate}
.kp-emp-submit:hover{transform:scale(1.02)}.kp-emp-submit:active{transform:scale(.98)}
.kp-emp-file::file-selector-button{margin-right:.9rem;border:1px solid rgba(201,169,98,.35);border-radius:9999px;background:rgba(201,169,98,.1);color:#e8d9b0;padding:.45rem 1rem;font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;cursor:pointer}
@media (prefers-reduced-motion:reduce){[data-kp-emp-rv]{opacity:1!important;transform:none!important;transition:none!important}.kp-contact-orb-1,.kp-contact-orb-2,.kp-contact-orb-3{animation:none!important}.kp-emp-submit:hover,.kp-emp-submit:active{transform:none}}
</style>
<div class="min-h-screen bg-kp-bg">
<main class="relative overflow-hidden pt-[110px] md:pt-[132px]">
  <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-kp-surface"></div>
  <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(900px_480px_at_12%_0%,rgba(201,169,98,0.16),transparent_58%),radial-gradient(820px_520px_at_88%_45%,rgba(255,255,255,0.06),transparent_55%),radial-gradient(700px_400px_at_50%_100%,rgba(201,169,98,0.05),transparent_50%)]"></div>
  <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 opacity-[0.28] bg-[linear-gradient(rgba(255,255,255,0.028)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.028)_1px,transparent_1px)] bg-[length:72px_72px]"></div>
  <div class="kp-grain pointer-events-none absolute inset-0 -z-10 opacity-[0.11] mix-blend-overlay" aria-hidden="true"></div>
  <div aria-hidden="true" class="kp-contact-orb-1 pointer-events-none absolute -left-32 top-24 h-[min(100vw,420px)] w-[min(100vw,420px)] rounded-full bg-kp-gold/10 blur-3xl md:-left-24 md:top-32 md:h-[480px] md:w-[480px]"></div>
  <div aria-hidden="true" class="kp-contact-orb-2 pointer-events-none absolute -right-40 top-[38%] h-[min(110vw,520px)] w-[min(110vw,520px)] rounded-full bg-white/[0.07] blur-3xl md:-right-28 md:h-[540px] md:w-[540px]"></div>
  <div aria-hidden="true" class="kp-contact-orb-3 pointer-events-none absolute bottom-0 left-1/2 h-[min(50vh,380px)] w-[min(100%,720px)] rounded-full bg-kp-gold/[0.06] blur-3xl"></div>
  <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 z-0 h-px bg-gradient-to-r from-transparent via-kp-gold/55 to-transparent"></div>

  <div class="relative z-10 mx-auto max-w-[1600px] px-4 pb-24 sm:px-5 md:px-10 md:pb-28 lg:pb-36">
    {{-- HERO (EmploisPageHero) --}}
    <div data-kp-emp-rv>
    <header class="relative mx-auto w-full max-w-6xl">
      {!! cms_edit(route('admin.emplois').'#entete', 'Modifier cette section', '.75rem') !!}
      <div class="relative isolate -mx-4 flex min-h-[min(78svh,640px)] w-[calc(100%+2rem)] max-w-none flex-col items-center justify-center overflow-hidden rounded-b-[28px] border border-white/10 border-t-transparent shadow-[0_36px_100px_-40px_rgba(0,0,0,0.92)] sm:mx-0 sm:min-h-[min(70svh,600px)] sm:w-full sm:rounded-3xl sm:border-t sm:border-white/[0.11] md:min-h-[min(62svh,560px)]" data-kp-emp-hero>
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
          <img src="{{ cms_asset(content('emp.hero_image')) }}" alt="" loading="eager" fetchpriority="high" decoding="async" class="absolute inset-0 h-full w-full object-cover object-[center_42%]">
          <div class="absolute inset-0 bg-kp-bg/65"></div>
          <div class="absolute inset-0 bg-gradient-to-br from-black/65 via-black/35 to-black/[0.78]"></div>
          <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-kp-gold/[0.12] to-transparent opacity-90"></div>
          <div class="absolute inset-0 opacity-[0.12] bg-[linear-gradient(rgba(255,255,255,0.05)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.05)_1px,transparent_1px)] bg-[length:56px_56px]"></div>
        </div>
        <div class="relative z-10 flex w-full max-w-3xl flex-col items-center px-6 py-14 text-center sm:px-8 sm:py-16 md:px-12 md:py-[4.25rem]">
          <span class="h-px w-24 shrink-0 bg-gradient-to-r from-transparent via-kp-gold to-transparent opacity-95 sm:w-32 md:w-40" aria-hidden="true"></span>
          <p class="mt-6 max-w-prose font-sans text-[10px] font-semibold uppercase leading-relaxed tracking-[0.38em] text-white/[0.58] sm:text-[11px] sm:tracking-[0.42em]">{{ $c('hero_kicker') }}</p>
          <h1 class="mt-4 max-w-[20ch] text-balance font-serif text-[clamp(2.05rem,4.8vw+0.5rem,3.65rem)] font-medium leading-[1.04] tracking-[-0.03em] text-kp-accent sm:mt-5">{{ $c('hero_title') }}</h1>
          <p class="mx-auto mt-5 max-w-2xl text-pretty text-sm leading-[1.7] text-white/[0.68] sm:mt-6 sm:text-[15px] md:leading-[1.72]">{{ $c('hero_text') }}</p>
          @if($chips->isNotEmpty())
          <div class="mt-9 flex max-w-xl flex-wrap items-center justify-center gap-2 sm:mt-10 sm:gap-2.5 md:max-w-none" role="list" aria-label="{{ content('emp.hero_chips_aria') }}">
            @foreach($chips as $chip)
            <span role="listitem" class="inline-flex items-center gap-2 rounded-full border border-white/[0.12] bg-black/35 px-3 py-2 font-sans text-[10px] font-semibold uppercase tracking-[0.18em] text-white/[0.72] shadow-[inset_0_1px_0_rgba(255,255,255,0.06)] backdrop-blur-md sm:px-3.5 sm:tracking-[0.2em]"><span class="h-1.5 w-1.5 shrink-0 rounded-full bg-kp-gold shadow-[0_0_12px_rgba(201,169,98,0.45)]" aria-hidden="true"></span>{{ $chip }}</span>
            @endforeach
          </div>
          @endif
        </div>
      </div>
    </header>
    </div>

    <div class="mt-14 grid gap-10 border-t border-white/20 pt-14 sm:mt-16 sm:gap-12 sm:pt-16 lg:mt-20 lg:grid-cols-12 lg:items-start lg:gap-14 lg:pt-20 xl:gap-20">
      {{-- CONSEILS --}}
      <div class="min-w-0 lg:col-span-5">
        <div data-kp-emp-rv>
        <aside class="relative lg:sticky lg:top-[calc(132px+1.25rem)] lg:self-start lg:pl-8 xl:top-[calc(132px+1.75rem)]">
          {!! cms_edit(route('admin.emplois').'#conseils', 'Modifier cette section', '-.5rem') !!}
          <div aria-hidden="true" class="absolute left-0 top-2 hidden h-[calc(100%-0.5rem)] w-px bg-gradient-to-b from-kp-gold/70 via-white/[0.12] to-transparent lg:block"></div>
          <p class="text-center font-sans text-[10px] font-semibold uppercase tracking-[0.32em] text-white/40 lg:text-left">{{ $c('tips_label') }}</p>
          <ul class="mt-5 grid gap-4 sm:grid-cols-2 lg:mt-6 lg:grid-cols-1" role="list">
            @foreach($tips as $i => $t)
            <li class="min-w-0"><div class="{{ $card }}" role="presentation">
              <span class="pointer-events-none absolute -right-16 -top-16 h-40 w-40 rounded-full bg-kp-gold/10 opacity-0 blur-3xl transition-opacity duration-500 group-hover:opacity-100" aria-hidden="true"></span>
              <div class="relative flex items-start gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ ($t['icon'] ?? '') === 'spark' ? 'border border-white/10 bg-white/5 text-kp-gold/90' : 'border border-kp-gold/25 bg-kp-gold/10 text-kp-gold' }}">{!! $icons[$t['icon'] ?? ''] ?? $icons[$i % 2 ? 'spark' : 'paper'] !!}</span>
                <div class="min-w-0 text-left">
                  <p class="font-sans text-[10px] font-semibold uppercase tracking-[0.28em] text-white/[0.38]">{{ $li($t, 'title') }}</p>
                  <p class="mt-2 text-[13px] leading-relaxed text-white/[0.42]">{{ $li($t, 'body') }}</p>
                </div>
              </div>
            </div></li>
            @endforeach
          </ul>
          <div class="mt-6 rounded-2xl border border-white/[0.06] bg-black/25 p-5 backdrop-blur-sm sm:mt-8 sm:p-6">
            <p class="text-center font-sans text-[10px] font-semibold uppercase tracking-[0.28em] text-white/35 lg:text-left">{{ $c('help_title') }}</p>
            <p class="mt-3 text-center text-[13px] leading-relaxed text-white/[0.42] lg:text-left">{{ $c('help_text') }}</p>
            <a href="{{ cms_url(content('emp.help_url')) }}" class="mt-5 flex min-h-[48px] w-full items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-3.5 text-center text-[13px] font-medium text-white/70 transition-all duration-300 hover:border-kp-gold/35 hover:bg-kp-gold/10 hover:text-kp-accent focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/45">{{ $c('help_btn') }}</a>
          </div>
        </aside>
        </div>
      </div>

      {{-- FORMULAIRE (JobApplicationForm) --}}
      <div class="min-w-0 lg:col-span-7">
        <div data-kp-emp-rv style="transition-delay:100ms">
        <div class="relative mx-auto max-w-xl lg:mx-0 lg:max-w-none" style="box-shadow:0 32px 100px rgba(0,0,0,0.58), inset 0 1px 0 rgba(255,255,255,0.07)" x-data="kpJobApply()" id="candidature">
          {!! cms_edit(route('admin.emplois').'#formulaire', 'Modifier le formulaire', '.75rem') !!}
          <div aria-hidden="true" class="pointer-events-none absolute -inset-px rounded-[32px] bg-gradient-to-br from-kp-gold/25 via-kp-gold/5 to-white/5 opacity-80"></div>
          <div class="relative overflow-hidden rounded-[32px] border border-white/10 bg-[linear-gradient(165deg,rgba(20,20,20,0.92)_0%,rgba(8,8,8,0.88)_45%,rgba(5,5,5,0.94)_100%)] p-6 backdrop-blur-xl md:p-9 lg:p-10">
            <div aria-hidden="true" class="pointer-events-none absolute -right-20 -top-20 h-[320px] w-[320px] rounded-full bg-kp-gold/[0.08] blur-3xl"></div>
            <div aria-hidden="true" class="pointer-events-none absolute -bottom-36 left-1/4 h-[280px] w-[280px] rounded-full bg-white/[0.06] blur-3xl"></div>
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 opacity-[0.15] bg-[linear-gradient(rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.04)_1px,transparent_1px)] bg-[length:48px_48px]"></div>

            <div class="relative text-center lg:text-left">
              <p class="font-sans text-[10px] font-semibold uppercase tracking-[0.32em] text-kp-gold/75 md:text-[11px]">{{ $c('f_kicker') }}</p>
              <p class="mt-3 font-serif text-2xl font-medium tracking-[-0.02em] text-kp-accent md:text-[1.75rem]">{{ $c('f_title') }}</p>
              <p class="mx-auto mt-3 max-w-lg text-[13px] leading-relaxed text-white/40 lg:mx-0">{{ $c('f_intro') }}</p>
            </div>

            {{-- Confirmation (ajout : la source n'avait pas d'enregistrement) --}}
            <div x-show="success" x-cloak role="status" data-kp-emp-success class="relative mt-10 rounded-2xl border border-kp-gold/30 bg-kp-gold/[0.07] p-6 text-center lg:text-left" @if(!$okNoJs) style="display:none" @endif>
              <p class="font-serif text-xl text-kp-accent">{{ $c('f_ok_title') }}</p>
              <p class="mt-2 text-[13px] leading-relaxed text-white/60">{{ $c('f_ok_text') }}</p>
              <a :href="mailto" x-show="mailto" class="mt-5 inline-flex items-center justify-center rounded-full border border-kp-gold/40 px-6 py-3 text-[11px] font-semibold uppercase tracking-[0.2em] text-kp-gold transition-colors hover:bg-kp-gold hover:text-black">{{ $c('f_ok_mail') }}</a>
            </div>

            <form x-show="!success" @submit.prevent="submit($el)" method="POST" action="{{ route('emplois.store') }}" enctype="multipart/form-data" class="relative mt-10 space-y-10" novalidate data-kp-emp-form @if($okNoJs) style="display:none" @endif>
              @csrf
              <div aria-hidden="true" style="position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off" value=""></label></div>
              <div>
                {!! $sect($c('f_sec_identity')) !!}
                <div class="space-y-5">
                  <div><label for="kp-cand-name" class="{{ $label }}">{{ $c('f_name') }}{!! $req !!}</label>
                    <input id="kp-cand-name" name="full_name" type="text" autocomplete="name" value="{{ old('full_name') }}" @input="error=null" placeholder="{{ $c('f_name_ph') }}" class="{{ $input }}"></div>
                  <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2"><label for="kp-cand-email" class="{{ $label }}">{{ $c('f_email') }}{!! $req !!}</label>
                      <input id="kp-cand-email" name="email" type="email" autocomplete="email" inputmode="email" value="{{ old('email') }}" @input="error=null" placeholder="{{ content('emp.f_email_ph') }}" class="{{ $input }}"></div>
                    <div><label for="kp-cand-phone" class="{{ $label }}">{{ $c('f_phone') }}{!! $opt !!}</label>
                      <input id="kp-cand-phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone') }}" placeholder="{{ content('emp.f_phone_ph') }}" class="{{ $input }}"></div>
                  </div>
                </div>
              </div>
              <div>
                {!! $sect($c('f_sec_position')) !!}
                <div><label for="kp-cand-position" class="{{ $label }}">{{ $c('f_position') }}{!! $req !!}</label>
                  <input id="kp-cand-position" name="position" type="text" value="{{ old('position') }}" @input="error=null" placeholder="{{ $c('f_position_ph') }}" class="{{ $input }}"></div>
              </div>
              <div>
                {!! $sect($c('f_sec_cv')) !!}
                <div class="space-y-4">
                  <div><label for="kp-cand-cv" class="{{ $label }}">{{ $c('f_cv') }}{!! $req !!}</label>
                    <textarea id="kp-cand-cv" name="cv_summary" rows="6" @input="error=null" placeholder="{{ $c('f_cv_ph') }}" class="{{ $input }} min-h-[144px] resize-y">{{ old('cv_summary') }}</textarea></div>
                  <div><label for="kp-cand-file" class="{{ $label }}">{{ $c('f_file') }}{!! $opt !!}</label>
                    <input id="kp-cand-file" name="cv" type="file" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" @change="error=null" class="kp-emp-file {{ $input }} py-3 text-[13px] text-white/60">
                    <p class="mt-2 text-left text-[11px] text-white/30">{{ $c('f_file_hint') }}</p></div>
                  <div><label for="kp-cand-education" class="{{ $label }}">{{ $c('f_education') }}{!! $opt !!}</label>
                    <textarea id="kp-cand-education" name="education" rows="3" placeholder="{{ $c('f_education_ph') }}" class="{{ $input }} min-h-[88px] resize-y">{{ old('education') }}</textarea></div>
                </div>
              </div>
              <div>
                {!! $sect($c('f_sec_skills')) !!}
                <div class="space-y-4">
                  <div><label for="kp-cand-skills" class="{{ $label }}">{{ $c('f_skills') }}{!! $req !!}</label>
                    <textarea id="kp-cand-skills" name="skills" rows="5" @input="error=null" placeholder="{{ $c('f_skills_ph') }}" class="{{ $input }} min-h-[120px] resize-y">{{ old('skills') }}</textarea></div>
                  <div><label for="kp-cand-lang" class="{{ $label }}">{{ $c('f_languages') }}{!! $opt !!}</label>
                    <input id="kp-cand-lang" name="languages" type="text" value="{{ old('languages') }}" placeholder="{{ $c('f_languages_ph') }}" class="{{ $input }}"></div>
                  <div><label for="kp-cand-extras" class="{{ $label }}">{{ $c('f_extras') }}{!! $opt !!}</label>
                    <textarea id="kp-cand-extras" name="extras" rows="3" placeholder="{{ $c('f_extras_ph') }}" class="{{ $input }} min-h-[88px] resize-y">{{ old('extras') }}</textarea></div>
                </div>
              </div>

              <p x-show="error" x-cloak x-text="error" role="alert" data-kp-emp-error class="rounded-xl border border-red-400/25 bg-red-950/35 px-4 py-3 text-sm text-red-200/95" @if(!session('emp_error')) style="display:none" @endif>{{ session('emp_error') }}</p>

              <div class="flex flex-col gap-5 border-t border-white/[0.07] pt-8 sm:flex-row sm:items-center sm:justify-between">
                <button type="submit" :disabled="sending" class="kp-emp-submit order-2 w-full rounded-full bg-kp-gold px-10 py-4 font-sans text-[11px] font-semibold uppercase tracking-[0.22em] text-black shadow-[0_16px_40px_-12px_rgba(201,169,98,0.45)] transition-[transform,colors] duration-300 hover:bg-[#d4b56e] disabled:cursor-not-allowed disabled:opacity-60 sm:order-1 sm:w-auto"><span x-text="sending ? @js($c('f_sending')) : @js($c('f_submit'))">{{ $c('f_submit') }}</span></button>
                <p class="order-1 max-w-xs text-center text-[11px] leading-relaxed text-white/30 sm:order-2 sm:text-right">{{ $c('f_dest_label') }} : <span class="text-white/55">{{ content('emp.f_dest_email') }}</span><br>{{ $c('f_dest_hint') }}</p>
              </div>
            </form>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</main>
</div>
<script>
(function(){var els=document.querySelectorAll('[data-kp-emp-rv]');
if(!('IntersectionObserver' in window)||matchMedia('(prefers-reduced-motion: reduce)').matches){els.forEach(function(e){e.classList.add('is-in')});return}
var io=new IntersectionObserver(function(en){en.forEach(function(x){if(x.isIntersecting){x.target.classList.add('is-in');io.unobserve(x.target)}})},{threshold:0.08,rootMargin:'0px 0px -6% 0px'});
els.forEach(function(e){io.observe(e)})})();
function kpJobApply(){return{success:@js((bool) $okNoJs),sending:false,error:@js(session('emp_error')),mailto:'',
 async submit(form){this.error=null;
  const M=@js(['name' => $c('e_name'), 'email' => $c('e_email'), 'emailBad' => $c('e_email_invalid'), 'position' => $c('e_position'), 'cv' => $c('e_cv'), 'skills' => $c('e_skills'), 'file' => $c('e_file'), 'server' => $c('e_server'), 'net' => $c('e_network')]);
  const fd=new FormData(form),v=k=>String(fd.get(k)||'').trim();
  if(!v('full_name')){this.error=M.name;return} if(!v('email')){this.error=M.email;return}
  if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v('email'))){this.error=M.emailBad;return}
  if(!v('position')){this.error=M.position;return} if(!v('cv_summary')){this.error=M.cv;return} if(!v('skills')){this.error=M.skills;return}
  const f=fd.get('cv');
  if(f&&f.size){if(f.size>2*1024*1024||!/\.(pdf|docx?)$/i.test(f.name)){this.error=M.file;return}}else{fd.delete('cv')}
  this.sending=true;
  try{const r=await fetch(form.action,{method:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':@js(csrf_token())},body:fd});
   const j=await r.json().catch(()=>null);if(!r.ok){this.error=(j&&j.error)||M.server;return}
   this.mailto=this.buildMailto(v);this.success=true;this.$nextTick(()=>this.$el.scrollIntoView({block:'start',behavior:matchMedia('(prefers-reduced-motion: reduce)').matches?'auto':'smooth'}))}
  catch(e){this.error=M.net}finally{this.sending=false}},
 buildMailto(v){const fr=@js(! $en),T=@js(['subject' => $c('mail_subject'), 'intro' => $c('mail_intro'), 'to' => content('emp.f_dest_email')]);
  const o=(lab,val)=>val?[lab,val,'']:[];
  const L=fr?['Bonjour,','',T.intro,'','--- Coordonnées ---','Nom complet : '+v('full_name'),'E-mail : '+v('email'),...(v('phone')?['Téléphone : '+v('phone')]:[]),'','--- Candidature ---','Poste visé : '+v('position'),'','Synthèse du CV (parcours & expériences) :',v('cv_summary'),'','Compétences :',v('skills'),'',...o('Langues :',v('languages')),...o('Formation & certifications :',v('education')),...o('Informations complémentaires :',v('extras')),'Cordialement,']
   :['Hello,','',T.intro,'','--- Contact details ---','Full name: '+v('full_name'),'Email: '+v('email'),...(v('phone')?['Phone: '+v('phone')]:[]),'','--- Application ---','Target position: '+v('position'),'','Résumé summary (background & experience):',v('cv_summary'),'','Skills:',v('skills'),'',...o('Languages:',v('languages')),...o('Education & certifications:',v('education')),...o('Additional information:',v('extras')),'Best regards,'];
  return 'mailto:'+T.to+'?subject='+encodeURIComponent(T.subject+' '+v('position')+' — '+v('full_name'))+'&body='+encodeURIComponent(L.join('\n'))}}}
</script>
@endsection
