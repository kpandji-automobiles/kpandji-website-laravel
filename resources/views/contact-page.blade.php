{{-- Contact — port de app/contact/page.tsx + components/kp/ContactPageContent.tsx (+ ContactPageDecor, ContactForm, animations de globals.css).
     Contenu : Admin › Contact (/admin/contact) — clés CMS « contactp.* » ; messages : table test_drive_requests (type « contact ») --}}
@extends('layouts.app')
@php($en = app()->getLocale() === 'en')
@php($c = fn ($k) => $en ? (content('contactp.'.$k.'_en') ?: content('contactp.'.$k)) : content('contactp.'.$k))
@php($subjects = collect(content('contactp.subjects'))->map(fn ($r) => $en ? (($r['en'] ?? '') ?: ($r['fr'] ?? '')) : ($r['fr'] ?? ''))->filter()->values())
@php($card = 'group relative block overflow-hidden rounded-2xl border border-white/[0.07] bg-kp-elevated/30 p-6 shadow-[0_24px_80px_-40px_rgba(0,0,0,0.9)] backdrop-blur-md transition-colors duration-500 hover:border-white/[0.12] hover:bg-kp-elevated/40 md:p-7')
@php($glow = 'pointer-events-none absolute -right-16 -top-16 h-40 w-40 rounded-full bg-kp-gold/10 blur-3xl opacity-0 transition-opacity duration-500 group-hover:opacity-100')
@php($input = 'w-full rounded-xl border border-white/[0.11] bg-black/40 px-4 py-3.5 font-sans text-[15px] text-kp-accent shadow-[inset_0_1px_0_rgba(255,255,255,0.04)] placeholder:text-white/[0.28] transition-[border-color,box-shadow] duration-200 focus:border-kp-gold/45 focus:outline-none focus:shadow-[inset_0_1px_0_rgba(255,255,255,0.05),0_0_0_1px_rgba(201,169,98,0.12)]')
@php($label = 'mb-2 block text-left text-[10px] font-semibold uppercase tracking-[0.22em] text-white/[0.38]')
@php($secTitle = fn ($t) => '<div class="mb-5 flex items-center gap-3"><span class="h-px w-8 shrink-0 bg-gradient-to-r from-kp-gold/80 to-kp-gold/15" aria-hidden="true"></span><h2 class="font-sans text-[10px] font-semibold uppercase tracking-[0.3em] text-white/45">'.e($t).'</h2></div>')
@section('seo_title', content('contactp.seo_title'))
@section('meta_description', content('contactp.seo_description'))
@push('meta')
<link rel="canonical" href="{{ url('/contact') }}">
<meta property="og:title" content="{{ content('contactp.og_title') }}">
<meta property="og:description" content="{{ content('contactp.og_description') }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="fr_FR">
<meta property="og:url" content="{{ url('/contact') }}">
@endpush
@section('content')
<style>
@keyframes kp-ct-fade-up{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}
.kp-ct-up{opacity:0;animation:kp-ct-fade-up .9s cubic-bezier(.22,1,.36,1) forwards}.kp-ct-d1{animation-delay:.1s}.kp-ct-d2{animation-delay:.2s}
@keyframes kp-contact-orb-1{0%,100%{transform:translate3d(0,0,0);opacity:.55}50%{transform:translate3d(12px,20px,0);opacity:.75}}
@keyframes kp-contact-orb-2{0%,100%{transform:translate3d(0,0,0);opacity:.4}50%{transform:translate3d(-10px,-18px,0);opacity:.6}}
@keyframes kp-contact-orb-3{0%,100%{transform:translate3d(-50%,33.333%,0) scale(1);opacity:.35}50%{transform:translate3d(-50%,33.333%,0) scale(1.06);opacity:.5}}
.kp-contact-orb-1{animation:kp-contact-orb-1 11s cubic-bezier(.22,1,.36,1) infinite alternate}
.kp-contact-orb-2{animation:kp-contact-orb-2 12.5s cubic-bezier(.22,1,.36,1) infinite alternate}
.kp-contact-orb-3{transform:translate3d(-50%,33.333%,0);animation:kp-contact-orb-3 14s cubic-bezier(.22,1,.36,1) infinite alternate}
.kp-ct-submit:hover{transform:scale(1.02)}.kp-ct-submit:active{transform:scale(.98)}
@media (prefers-reduced-motion:reduce){.kp-ct-up{animation:none!important;opacity:1!important;transform:none!important}.kp-contact-orb-1,.kp-contact-orb-2,.kp-contact-orb-3{animation:none!important}.kp-ct-submit:hover,.kp-ct-submit:active{transform:none}}
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

  <div class="relative z-10 mx-auto max-w-[1600px] px-5 pb-24 md:px-10 md:pb-32">
    <header class="kp-ct-up relative mx-auto max-w-3xl text-center">
      {!! cms_edit(route('admin.contactp').'#entete', 'Modifier cette section', '-2.5rem') !!}
      <div class="flex flex-col items-center">
        <span class="h-px w-32 bg-gradient-to-r from-transparent via-kp-gold/90 to-transparent md:w-48" aria-hidden="true"></span>
        <h1 class="mt-5 max-w-[18ch] font-serif text-[clamp(2.15rem,5.4vw,3.5rem)] font-medium leading-[1.05] tracking-[-0.025em] text-kp-accent">{{ $c('h1') }}</h1>
      </div>
    </header>

    <div class="mt-14 grid gap-12 lg:mt-20 lg:grid-cols-12 lg:items-start lg:gap-14 xl:gap-20">
      <div class="lg:col-span-5">
        <div class="kp-ct-up kp-ct-d1 relative lg:pl-8">
          {!! cms_edit(route('admin.contactp').'#acces', 'Modifier cette section', '-2.5rem') !!}
          <div aria-hidden="true" class="absolute left-0 top-2 hidden h-[calc(100%-0.5rem)] w-px bg-gradient-to-b from-kp-gold/70 via-white/[0.12] to-transparent lg:block"></div>
          <p class="text-center font-sans text-[10px] font-semibold uppercase tracking-[0.32em] text-white/40 lg:text-left">{{ $c('access_label') }}</p>
          <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
            <a href="{{ cms_url(content('contactp.email_link')) }}" class="{{ $card }}">
              <span class="{{ $glow }}" aria-hidden="true"></span>
              <div class="relative flex items-start gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-kp-gold/25 bg-kp-gold/10 text-kp-gold"><svg class="opacity-90" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="m22 6-10 7L2 6"/></svg></span>
                <div class="min-w-0 text-left">
                  <p class="font-sans text-[10px] font-semibold uppercase tracking-[0.28em] text-white/[0.38]">{{ $c('email_label') }}</p>
                  <p class="mt-2 break-all font-sans text-[15px] font-medium text-kp-accent transition-colors group-hover:text-white">{{ content('contactp.email') }}</p>
                </div>
              </div>
            </a>
            <div class="{{ $card }}">
              <span class="{{ $glow }}" aria-hidden="true"></span>
              <div class="relative flex items-start gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-kp-gold/90"><svg class="opacity-90" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></span>
                <div class="min-w-0 text-left">
                  <p class="font-sans text-[10px] font-semibold uppercase tracking-[0.28em] text-white/[0.38]">{{ $c('hq_label') }}</p>
                  <p class="mt-2 font-sans text-[15px] font-medium text-kp-accent">{{ $c('hq_title') }}</p>
                  <p class="mt-1.5 text-[13px] leading-relaxed text-white/[0.42]">{{ $c('hq_text') }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-6 rounded-2xl border border-white/[0.06] bg-black/25 p-5 backdrop-blur-sm md:p-6"></div>
        </div>
      </div>

      {{-- ContactForm --}}
      <div class="kp-ct-up kp-ct-d2 lg:col-span-7">
        <div class="relative mx-auto max-w-xl lg:mx-0 lg:max-w-none" style="box-shadow:0 32px 100px rgba(0,0,0,0.58), inset 0 1px 0 rgba(255,255,255,0.07)"
             x-data="kpContact()" data-kp-contact>
          {!! cms_edit(route('admin.contactp').'#formulaire', 'Modifier le formulaire', '-2.5rem') !!}
          <div aria-hidden="true" class="pointer-events-none absolute -inset-px rounded-[32px] bg-gradient-to-br from-kp-gold/25 via-kp-gold/5 to-white/5 opacity-80"></div>
          <div class="relative overflow-hidden rounded-[32px] border border-white/10 bg-[linear-gradient(165deg,rgba(20,20,20,0.92)_0%,rgba(8,8,8,0.88)_45%,rgba(5,5,5,0.94)_100%)] p-6 backdrop-blur-xl md:p-9 lg:p-10">
            <div aria-hidden="true" class="pointer-events-none absolute -right-20 -top-20 h-[320px] w-[320px] rounded-full bg-kp-gold/[0.08] blur-3xl"></div>
            <div aria-hidden="true" class="pointer-events-none absolute -bottom-36 left-1/4 h-[280px] w-[280px] rounded-full bg-white/[0.06] blur-3xl"></div>
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 opacity-[0.15] bg-[linear-gradient(rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.04)_1px,transparent_1px)] bg-[length:48px_48px]"></div>
            <div class="relative text-center lg:text-left">
              <p class="font-sans text-[10px] font-semibold uppercase tracking-[0.32em] text-kp-gold/75 md:text-[11px]">{{ $c('f_kicker') }}</p>
              <p class="mt-3 font-serif text-2xl font-medium tracking-[-0.02em] text-kp-accent md:text-[1.75rem]">{{ $c('f_title') }}</p>
              <p class="mx-auto mt-3 max-w-md text-[13px] leading-relaxed text-white/40 lg:mx-0">{{ $c('f_intro') }}</p>
            </div>
            @php($okNoJs = session('contact_ok'))
            <div role="status" x-show="success" @if(! $okNoJs) x-cloak style="display:none" @endif class="relative mt-10 rounded-2xl border border-kp-gold/30 bg-kp-gold/[0.08] px-6 py-10 text-center">
              <p class="font-serif text-2xl font-medium text-kp-accent">{{ $c('f_ok_title') }}</p>
              <p class="mx-auto mt-4 max-w-sm text-[14px] leading-relaxed text-white/50">{{ $c('f_ok_text') }}</p>
            </div>
            {{-- Sans JavaScript : envoi classique (POST /contact, CSRF) ; avec JavaScript : fetch JSON --}}
            <form x-show="!success" @if($okNoJs) style="display:none" @endif method="POST" action="{{ route('contact.send') }}" @submit.prevent="submit()" class="relative mt-10 space-y-10" novalidate>
              @csrf
              <div>
                {!! $secTitle($c('f_sec1')) !!}
                <div class="space-y-5">
                  <div><label for="kp-contact-name" class="{{ $label }}">{{ $c('f_name') }} <span class="text-kp-gold/90">*</span></label>
                    <input id="kp-contact-name" name="name" type="text" autocomplete="name" maxlength="120" value="{{ old('name') }}" x-model="f.name" @input="error=null" placeholder="{{ content('contactp.f_name_ph') }}" class="{{ $input }}"></div>
                  <div class="grid gap-5 sm:grid-cols-2">
                    <div><label for="kp-contact-email-full" class="{{ $label }}">{{ $c('f_email') }} <span class="text-kp-gold/90">*</span></label>
                      <input id="kp-contact-email-full" name="email" type="email" autocomplete="email" inputmode="email" maxlength="160" value="{{ old('email') }}" x-model="f.email" @input="error=null" placeholder="{{ content('contactp.f_email_ph') }}" class="{{ $input }}"></div>
                    <div><label for="kp-contact-phone" class="{{ $label }}">{{ $c('f_phone') }}<span class="ml-1.5 font-normal normal-case tracking-normal text-white/[0.22]">{{ $c('f_optional') }}</span></label>
                      <input id="kp-contact-phone" name="phone" type="tel" autocomplete="tel" maxlength="40" value="{{ old('phone') }}" x-model="f.phone" placeholder="{{ content('contactp.f_phone_ph') }}" class="{{ $input }}"></div>
                  </div>
                </div>
              </div>
              <div>
                {!! $secTitle($c('f_sec2')) !!}
                <div class="space-y-4">
                  <div>
                    <label for="kp-contact-subject" class="{{ $label }}">{{ $c('f_subject') }} <span class="text-kp-gold/90">*</span></label>
                    <div class="flex flex-wrap gap-2">
                      @foreach($subjects as $s)
                      <button type="button" data-kp-subject @click="f.subject = @js($s); error = null" :aria-pressed="f.subject === @js($s)"
                        :class="f.subject === @js($s) ? 'border-kp-gold/50 bg-kp-gold/15 text-kp-accent' : 'border-white/[0.12] bg-white/[0.03] text-white/50 hover:border-white/[0.18] hover:bg-white/[0.06] hover:text-white/75'"
                        class="rounded-full border border-white/[0.12] px-3.5 py-2 font-sans text-[11px] font-medium tracking-wide text-white/50 transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/45">{{ $s }}</button>
                      @endforeach
                    </div>
                    <input id="kp-contact-subject" name="subject" type="text" maxlength="200" value="{{ old('subject') }}" x-model="f.subject" @input="error=null" placeholder="{{ $c('f_subject_ph') }}" class="{{ $input }} mt-3">
                  </div>
                  <div><label for="kp-contact-message" class="{{ $label }}">{{ $c('f_message') }} <span class="text-kp-gold/90">*</span></label>
                    <textarea id="kp-contact-message" name="message" rows="7" maxlength="5000" x-model="f.message" @input="error=null" placeholder="{{ $c('f_message_ph') }}" class="{{ $input }} min-h-[168px] resize-y">{{ old('message') }}</textarea></div>
                </div>
              </div>
              @if(session('contact_error'))<p role="alert" x-show="false" class="rounded-xl border border-red-400/25 bg-red-950/35 px-4 py-3 text-sm text-red-200/95">{{ session('contact_error') }}</p>@endif
              <p role="alert" x-show="error" x-cloak x-text="error" class="rounded-xl border border-red-400/25 bg-red-950/35 px-4 py-3 text-sm text-red-200/95"></p>
              <div class="flex flex-col gap-5 border-t border-white/[0.07] pt-8 sm:flex-row sm:items-center sm:justify-between">
                <button type="submit" :disabled="sending" class="kp-ct-submit order-2 w-full rounded-full bg-kp-gold px-10 py-4 font-sans text-[11px] font-semibold uppercase tracking-[0.22em] text-black shadow-[0_16px_40px_-12px_rgba(201,169,98,0.45)] transition-[transform,colors] duration-300 hover:bg-[#d4b56e] disabled:cursor-not-allowed disabled:opacity-60 sm:order-1 sm:w-auto"><span x-text="sending ? @js($c('f_sending')) : @js($c('f_submit'))">{{ $c('f_submit') }}</span></button>
                <p class="order-1 max-w-xs text-center text-[11px] leading-relaxed text-white/30 sm:order-2 sm:text-right">{{ $c('f_privacy') }}</p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
</div>
<script>
function kpContact(){return{success:@js((bool) $okNoJs),sending:false,error:@js(session('contact_error')),
 f:{name:@js(old('name','')),email:@js(old('email','')),phone:@js(old('phone','')),subject:@js(old('subject','')),message:@js(old('message',''))},
 async submit(){this.error=null;const M=@js(['name' => $c('e_name'), 'email' => $c('e_email'), 'emailBad' => $c('e_email_invalid'), 'subject' => $c('e_subject'), 'message' => $c('e_message'), 'server' => $c('e_server'), 'net' => $c('e_network')]);
  const d={name:this.f.name.trim(),email:this.f.email.trim(),phone:this.f.phone.trim(),subject:this.f.subject.trim(),message:this.f.message.trim()};
  if(!d.name){this.error=M.name;return} if(!d.email){this.error=M.email;return} if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(d.email)){this.error=M.emailBad;return}
  if(!d.subject){this.error=M.subject;return} if(!d.message){this.error=M.message;return}
  this.sending=true;
  try{const r=await fetch(@js(route('contact.send')),{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':@js(csrf_token())},body:JSON.stringify(d)});
   const j=await r.json().catch(()=>null);if(!r.ok){this.error=(j&&j.error)||M.server;return}this.success=true}
  catch(e){this.error=M.net}finally{this.sending=false}}}}
</script>
@endsection
