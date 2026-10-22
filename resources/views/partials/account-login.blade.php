{{-- Fenêtre de connexion ouverte par l'icône utilisateur du header. POST AJAX /connexion (JSON) ; sans JavaScript : lien /admin/login ; échec d'envoi classique -> retour avec la fenêtre rouverte. Textes : Admin › Contenu du site › Header (clés header.login_*). --}}
@php($kpLoginErr = $errors->getBag('kpLogin')->first('login'))
<div x-data="kpLogin()" x-init="init()" @keydown.escape.window="open && close()">
<template x-teleport="body">
<div x-show="open" x-cloak class="kp-login-modal fixed inset-0 z-[90] flex items-end justify-center overflow-y-auto sm:items-center sm:p-6" role="dialog" aria-modal="true" aria-labelledby="kp-login-title" @click.self="close()"
     x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
  <div class="absolute inset-0 bg-black/[0.82] backdrop-blur-md" aria-hidden="true" @click="close()"></div>
  <div class="relative z-10 my-auto w-full max-w-md overflow-hidden rounded-t-[28px] border border-white/[0.12] bg-[linear-gradient(165deg,rgba(20,20,20,0.96)_0%,rgba(8,8,8,0.95)_45%,rgba(5,5,5,0.98)_100%)] shadow-[0_32px_100px_rgba(0,0,0,0.65),inset_0_1px_0_rgba(255,255,255,0.07)] sm:rounded-[28px]">
    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold/60 to-transparent"></div>
    <div aria-hidden="true" class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-kp-gold/[0.09] blur-3xl"></div>
    <div class="relative flex items-start justify-between gap-4 px-7 pb-2 pt-7 sm:px-9 sm:pt-9">
      <div>
        <p class="font-sans text-[10px] font-semibold uppercase tracking-[0.32em] text-kp-gold/85">{{ $kpT('login_kicker') }}</p>
        <h2 id="kp-login-title" class="mt-2 font-serif text-[1.9rem] font-medium leading-tight tracking-[-0.02em] text-white">{{ $kpT('login_title') }}</h2>
        <p class="mt-2 text-[13px] leading-relaxed text-white/45">{{ $kpT('login_subtitle') }}</p>
      </div>
      <button type="button" @click="close()" class="-mr-2 shrink-0 rounded-full p-2 text-white/60 transition hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/55" aria-label="{{ $kpT('login_close') }}"><span class="block text-2xl leading-none" aria-hidden="true">×</span></button>
    </div>
    <form method="POST" action="{{ route('account.login') }}" @submit.prevent="submit($event)" class="relative space-y-5 px-7 pb-8 pt-5 sm:px-9 sm:pb-9" novalidate>
      @csrf
      <div><label for="kp-login-email" class="mb-2 block text-[10px] font-semibold uppercase tracking-[0.22em] text-white/[0.38]">{{ $kpT('login_email') }}</label>
        <input id="kp-login-email" name="email" type="email" autocomplete="username" inputmode="email" maxlength="190" x-model="email" @input="error=null" value="{{ old('email') }}" class="w-full rounded-xl border border-white/[0.11] bg-black/40 px-4 py-3.5 text-[15px] text-kp-accent placeholder:text-white/[0.28] transition focus:border-kp-gold/45 focus:outline-none focus:shadow-[0_0_0_1px_rgba(201,169,98,0.12)]"></div>
      <div><label for="kp-login-password" class="mb-2 block text-[10px] font-semibold uppercase tracking-[0.22em] text-white/[0.38]">{{ $kpT('login_password') }}</label>
        <input id="kp-login-password" name="password" type="password" autocomplete="current-password" x-model="password" @input="error=null" class="w-full rounded-xl border border-white/[0.11] bg-black/40 px-4 py-3.5 text-[15px] text-kp-accent transition focus:border-kp-gold/45 focus:outline-none focus:shadow-[0_0_0_1px_rgba(201,169,98,0.12)]"></div>
      <label class="flex cursor-pointer items-center gap-3 text-[13px] text-white/60"><input type="checkbox" name="remember" value="1" x-model="remember" class="h-4 w-4 rounded border-white/20 bg-black/40 accent-[#c9a962]"> {{ $kpT('login_remember') }}</label>
      <p x-show="error" x-text="error" x-cloak role="alert" data-kp-login-error class="rounded-xl border border-red-400/25 bg-red-950/35 px-4 py-3 text-sm text-red-200/95"></p>
      <button type="submit" :disabled="sending" data-kp-login-submit class="w-full rounded-full bg-kp-gold px-8 py-4 font-sans text-[11px] font-semibold uppercase tracking-[0.22em] text-black shadow-[0_16px_40px_-12px_rgba(201,169,98,0.45)] transition-colors duration-300 hover:bg-[#d4b56e] disabled:cursor-wait disabled:opacity-60"><span x-text="sending ? @js($kpT('login_sending')) : @js($kpT('login_submit'))">{{ $kpT('login_submit') }}</span></button>
    </form>
  </div>
</div>
</template>
</div>
<script>
function kpLogin(){return{open:@js((bool) session('kp_login_open')),sending:false,error:@js($kpLoginErr ?: null),email:@js(old('email','')),password:'',remember:false,
 init(){window.kpOpenLogin=()=>{try{this.error=null;this.open=true;setTimeout(()=>{const e=document.getElementById('kp-login-email');e&&e.focus()},60);return true}catch(e){return false}};
  this.$watch('open',v=>document.documentElement.style.overflow=v?'hidden':'')},
 close(){this.open=false},
 async submit(ev){this.error=null;if(!this.email.trim()||!this.password){this.error=@js($kpT('login_e_required'));return}
  this.sending=true;
  try{const r=await fetch(@js(route('account.login')),{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':@js(csrf_token())},body:JSON.stringify({email:this.email.trim(),password:this.password,remember:this.remember})});
   const j=await r.json().catch(()=>null);
   if(r.ok&&j&&j.redirect){window.location.href=j.redirect;return}
   if(j&&j.error){this.error=j.error;this.password='';return}
   ev.target.submit()} // réponse inattendue (ex. 419) : envoi classique
  catch(e){ev.target.submit()}finally{this.sending=false}}}}
</script>
