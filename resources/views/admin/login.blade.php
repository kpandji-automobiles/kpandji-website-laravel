@extends('admin.layout')
@section('title','Connexion')
@section('content')
<div class="flex min-h-screen items-center justify-center bg-[radial-gradient(800px_400px_at_50%_0%,rgba(201,169,98,0.15),transparent_70%)] px-5">
  <form method="POST" action="{{ route('admin.login') }}" class="w-full max-w-sm rounded-2xl border border-white/10 bg-[#0f0f0f] p-8 shadow-2xl">@csrf
    <img src="{{ asset('images/logo.png') }}" class="mx-auto h-14 w-auto" alt="KPANDJI">
    <h1 class="mt-5 text-center font-serif text-3xl text-white">Administration</h1>
    <p class="mt-1 text-center text-[11px] uppercase tracking-[0.3em] text-kp-gold">Espace réservé</p>
    @if(session('status'))<p class="mt-6 rounded-lg bg-emerald-500/10 px-4 py-2 text-sm text-emerald-300">{{ session('status') }}</p>@endif
    @error('email')<p class="mt-6 rounded-lg bg-red-500/10 px-4 py-2 text-sm text-red-300">{{ $message }}</p>@enderror
    <label class="mt-6 block text-xs uppercase tracking-[0.2em] text-white/60">E-mail<input type="email" name="email" value="{{ old('email') }}" required autofocus class="mt-2 w-full rounded-lg border border-white/15 bg-black px-4 py-3 text-white outline-none focus:border-kp-gold"></label>
    <label class="mt-4 block text-xs uppercase tracking-[0.2em] text-white/60">Mot de passe<input type="password" name="password" required class="mt-2 w-full rounded-lg border border-white/15 bg-black px-4 py-3 text-white outline-none focus:border-kp-gold"></label>
    <label class="mt-4 flex items-center gap-2 text-sm text-white/60"><input type="checkbox" name="remember" class="accent-[#c9a962]"> Rester connecté</label>
    <button class="mt-6 w-full rounded-full bg-kp-gold py-3 text-xs font-bold uppercase tracking-[0.22em] text-black hover:bg-white">Se connecter</button>
    <a href="{{ route('home') }}" class="mt-4 block text-center text-xs text-white/40 hover:text-white">← Retour au site</a>
  </form>
</div>
@endsection
