@extends('layouts.app')
@section('title','Accueil')
@section('content')
@include('partials.hero')
@if(config('promo.active', true))
@include('partials.promo')
@endif
@include('partials.modeles')
@include('partials.parallax')
{{-- Bannière « avantages » — clés home.banner_* (Accueil) --}}
@php($bnEn = app()->getLocale() === 'en')
@php($bn = fn ($k) => $bnEn ? (content('home.'.$k.'_en') ?: content('home.'.$k)) : content('home.'.$k))
@php($bnItems = collect(content('home.banner_items'))->map(fn ($r) => ['icon' => $r['icon'] ?? '', 'txt' => $bnEn ? (($r['en'] ?? '') ?: ($r['fr'] ?? '')) : ($r['fr'] ?? '')])->filter(fn ($r) => filled($r['txt']))->values())
@php($bnIco = ['shield' => '<path d="M12 3 5 6v5.5c0 4.3 2.9 8.1 7 9.5 4.1-1.4 7-5.2 7-9.5V6l-7-3Z"/><path d="m9 12 2.2 2.2L15.5 10"/>', 'wrench' => '<path d="M14.7 6.3a4 4 0 0 0 5 5L21 13l-1.4 1.4a5 5 0 0 1-6-.8L7 20.2a1.8 1.8 0 0 1-2.6-2.6L10.8 11a5 5 0 0 1-.8-6L11.4 3.6l1.6 1.3a4 4 0 0 0 1.7 1.4Z"/>', 'document' => '<rect x="3" y="7" width="18" height="10" rx="2"/><path d="M7 11h4M7 13.5h7"/><circle cx="17" cy="12" r="1.2"/>'])
@php($bnImg = content('home.banner_image'))
@php($bnWa = 'https://wa.me/'.preg_replace('/\D/', '', (string) content('home.banner_wa_number')))
<style>
.kp-bn-rv{opacity:0;transform:translateY(28px);transition:opacity .9s cubic-bezier(.22,1,.36,1),transform .9s cubic-bezier(.22,1,.36,1)}.kp-bn-in .kp-bn-rv{opacity:1;transform:none}
@keyframes kp-bn-sheen{0%{transform:translateX(-120%) skewX(-18deg)}55%,100%{transform:translateX(320%) skewX(-18deg)}}
.kp-bn-sheen{animation:kp-bn-sheen 6.5s cubic-bezier(.4,0,.2,1) infinite}
@keyframes kp-bn-spin{to{transform:rotate(360deg)}}.kp-bn-ring{animation:kp-bn-spin 40s linear infinite}
.kp-bn-press:hover{transform:translateY(-1px)}.kp-bn-press:active{transform:scale(.98)}
@media (prefers-reduced-motion:reduce){.kp-bn-rv{opacity:1!important;transform:none!important;transition:none!important}.kp-bn-sheen,.kp-bn-ring{animation:none!important}.kp-bn-sheen{display:none}.kp-bn-press:hover,.kp-bn-press:active{transform:none}}
</style>
<section class="relative mx-auto mt-24 max-w-[1400px] px-4 sm:px-5 md:px-10" x-data="{ on: false }" x-init="if (!('IntersectionObserver' in window) || matchMedia('(prefers-reduced-motion: reduce)').matches) on = true; else { const io = new IntersectionObserver((e) => { if (e[0].isIntersecting) { on = true; io.disconnect() } }, { threshold: .15 }); io.observe($el) }" :class="on && 'kp-bn-in'" data-kp-banner>
  {!! cms_edit('home', 'Modifier cette section', '1rem') !!}
  <div class="relative isolate overflow-hidden rounded-[32px] border border-kp-gold/35 bg-[#070605] shadow-[0_40px_120px_-40px_rgba(201,169,98,0.45)]">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(900px_500px_at_0%_0%,rgba(201,169,98,0.22),transparent_60%),radial-gradient(700px_420px_at_100%_100%,rgba(201,169,98,0.14),transparent_60%),linear-gradient(120deg,#0b0906_0%,#050505_55%,#0d0a05_100%)]"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 opacity-[0.16] bg-[linear-gradient(rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.04)_1px,transparent_1px)] bg-[length:64px_64px] [mask-image:radial-gradient(ellipse_at_center,black_20%,transparent_75%)]"></div>
    <span aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-[#f1dfa8] to-transparent opacity-80"></span>
    <span aria-hidden="true" class="kp-bn-sheen pointer-events-none absolute inset-y-0 left-0 w-1/3 bg-gradient-to-r from-transparent via-white/[0.07] to-transparent"></span>
    <div class="grid items-center gap-10 p-7 sm:p-10 md:p-14 lg:grid-cols-12 lg:gap-12 lg:p-16">
      {{-- emblème --}}
      <div class="kp-bn-rv relative mx-auto flex h-[200px] w-[200px] shrink-0 items-center justify-center sm:h-[240px] sm:w-[240px] lg:col-span-4 lg:h-[300px] lg:w-[300px]" aria-hidden="true">
        <div class="absolute inset-0 rounded-full bg-[radial-gradient(circle,rgba(201,169,98,0.35),transparent_65%)] blur-2xl"></div>
        <svg class="kp-bn-ring absolute inset-0 h-full w-full text-kp-gold/45" viewBox="0 0 200 200" fill="none"><circle cx="100" cy="100" r="96" stroke="currentColor" stroke-width=".6" stroke-dasharray="2 6"/></svg>
        <div class="absolute inset-[10%] rounded-full border border-kp-gold/30 bg-gradient-to-br from-[#2a2112] via-[#0c0a06] to-black shadow-[inset_0_1px_0_rgba(255,255,255,0.08),0_30px_60px_-20px_rgba(0,0,0,0.9)]"></div>
        @if($bnImg)<img src="{{ cms_asset($bnImg) }}" alt="" loading="lazy" decoding="async" class="absolute bottom-[12%] left-1/2 z-10 w-[112%] max-w-none -translate-x-1/2 object-contain opacity-95 drop-shadow-[0_24px_30px_rgba(0,0,0,0.85)]">@endif
        <svg class="absolute left-1/2 top-[9%] z-20 h-[26%] w-[26%] -translate-x-1/2 text-kp-gold drop-shadow-[0_0_18px_rgba(201,169,98,0.7)]" viewBox="0 0 24 24" fill="none" stroke="url(#kpBnGold)" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round"><defs><linearGradient id="kpBnGold" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#f6e7b5"/><stop offset=".5" stop-color="#c9a962"/><stop offset="1" stop-color="#8a6a2c"/></linearGradient></defs>{!! $bnIco['shield'] !!}</svg>
      </div>
      {{-- texte --}}
      <div class="min-w-0 text-center lg:col-span-8 lg:text-left">
        <p class="kp-bn-rv flex items-center justify-center gap-3 text-[10px] font-bold uppercase tracking-[0.38em] text-kp-gold sm:text-[11px] lg:justify-start" style="transition-delay:80ms"><span class="h-px w-8 bg-kp-gold/70" aria-hidden="true"></span>{{ $bn('banner_eyebrow') }}</p>
        <h2 class="kp-bn-rv mt-4 bg-gradient-to-r from-[#fbf3dc] via-[#e9d29a] to-[#c9a962] bg-clip-text font-serif text-[clamp(2rem,4.6vw,3.9rem)] font-medium leading-[1.04] tracking-[-0.02em] text-transparent" style="transition-delay:160ms">{{ $bn('banner_title') }}</h2>
        @if($bnItems->isNotEmpty())
        <ul class="kp-bn-rv mt-8 grid gap-3 sm:grid-cols-2 lg:max-w-2xl" role="list" data-kp-banner-items style="transition-delay:240ms">
          @foreach($bnItems as $a)
          <li class="group flex items-center gap-4 rounded-2xl border border-white/10 bg-white/[0.035] px-4 py-3.5 text-left backdrop-blur-sm transition-colors duration-500 hover:border-kp-gold/40 hover:bg-kp-gold/[0.06]">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-kp-gold/45 bg-gradient-to-br from-kp-gold/25 to-kp-gold/5 text-kp-gold shadow-[0_0_20px_-6px_rgba(201,169,98,0.7)]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $bnIco[$a['icon']] ?? $bnIco['shield'] !!}</svg></span>
            <span class="min-w-0 font-serif text-[1.05rem] leading-snug text-white sm:text-[1.15rem]">{{ $a['txt'] }}</span>
          </li>
          @endforeach
        </ul>
        @endif
        <div class="kp-bn-rv mt-9 flex flex-col gap-3 sm:flex-row sm:justify-center lg:justify-start" style="transition-delay:320ms">
          <a href="{{ cms_url(content('home.banner_url')) }}" class="kp-bn-press inline-flex min-h-[54px] items-center justify-center gap-2 rounded-full bg-gradient-to-b from-[#e3c886] to-kp-gold px-8 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-black shadow-[0_18px_44px_-14px_rgba(201,169,98,0.65),inset_0_1px_0_rgba(255,255,255,0.45)] transition-[transform,filter] duration-300 hover:brightness-110" data-kp-banner-cta>{{ $bn('banner_cta') }}</a>
          @if(filled(content('home.banner_wa_number')) && filled($bn('banner_wa')))
          <a href="{{ $bnWa }}" target="_blank" rel="noopener" class="kp-bn-press inline-flex min-h-[54px] items-center justify-center gap-2.5 rounded-full border border-white/20 bg-white/[0.04] px-7 py-4 text-[11px] font-semibold uppercase tracking-[0.22em] text-white/90 transition-colors duration-300 hover:border-kp-gold/60 hover:text-white" data-kp-banner-wa><svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.8-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.4-.5c.2-.2.2-.3.3-.5.1-.2 0-.4 0-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5 2.5 1 3 .8 3.6.8.6-.1 1.8-.7 2-1.4.2-.7.2-1.3.2-1.4-.1-.2-.3-.3-.6-.4zM12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2c-1.5 0-3-.4-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2z"/></svg>{{ $bn('banner_wa') }}</a>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
