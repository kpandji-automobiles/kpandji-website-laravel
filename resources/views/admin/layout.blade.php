<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex">
<title>@yield('title', 'Administration') — KPANDJI Admin</title>
<link rel="icon" href="{{ asset('images/logo.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={theme:{extend:{colors:{kp:{black:'#0a0a0a',dark:'#141414',gold:'#c9a962',sand:'#e8e4dc'}},fontFamily:{sans:['Montserrat','sans-serif'],serif:['"Playfair Display"','Georgia','serif']}}}}</script>
<style>[x-cloak]{display:none!important}</style>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-kp-black font-sans text-kp-sand antialiased">
@auth
<div x-data="{ nav:false }" class="flex min-h-screen">
  <aside class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full border-r border-white/10 bg-[#070707] transition-transform lg:static lg:translate-x-0" :class="nav && 'translate-x-0'">
    <div class="flex items-center gap-3 border-b border-white/10 px-6 py-5"><img src="{{ asset('images/logo.png') }}" class="h-9 w-auto" alt=""><div><p class="font-serif text-lg text-white">KPANDJI</p><p class="text-[10px] uppercase tracking-[0.3em] text-kp-gold">Administration</p></div></div>
    <nav class="space-y-1 p-4 text-sm">
      @foreach([['Tableau de bord','admin.dashboard','admin.dashboard'],['Voitures & offres','admin.cars.index','admin.cars.*'],['Promotion','admin.promo','admin.promo*'],['Contenu du site','admin.content','admin.content*'],['Pages','admin.pages.index','admin.pages.*'],['Showroom','admin.showroom','admin.showroom*'],['Opportunités','admin.opps','admin.opps*'],['Kpandji Sira','admin.sira','admin.sira*'],['Kpandji Privilège','admin.privilege','admin.privilege*'],['S.A.V.','admin.sav','admin.sav*'],['Contact','admin.contactp','admin.contactp*'],['Kpandji Automobiles','admin.brand','admin.brand*'],['Eco-Kpandji','admin.eco','admin.eco*'],['Candidatures','admin.emplois','admin.emplois*'],['Modèles (accueil)','admin.modeles.index','admin.modeles.*'],['Demandes d\'essai','admin.requests','admin.requests*']] as [$l,$r,$p])
        <a href="{{ route($r) }}" class="flex items-center justify-between rounded-lg px-4 py-2.5 transition {{ request()->routeIs($p) ? 'bg-kp-gold text-black font-semibold' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">{{ $l }}
          @if($r==='admin.requests' && ($n = \App\Models\TestDriveRequest::whereNull('handled_at')->count()))<span class="rounded-full bg-red-600 px-2 text-[10px] font-bold text-white">{{ $n }}</span>@endif
        </a>
      @endforeach
    </nav>
    <div class="absolute inset-x-0 bottom-0 border-t border-white/10 p-4 text-xs">
      <p class="truncate text-white/50">{{ auth()->user()->email }}</p>
      <div class="mt-3 flex gap-2"><a href="{{ route('home') }}" target="_blank" class="flex-1 rounded-full border border-white/15 py-2 text-center hover:border-kp-gold">Voir le site ↗</a>
      <form method="POST" action="{{ route('admin.logout') }}" class="flex-1">@csrf<button class="w-full rounded-full bg-white/10 py-2 hover:bg-red-600">Déconnexion</button></form></div>
    </div>
  </aside>
  <div class="min-w-0 flex-1">
    <header class="sticky top-0 z-30 flex items-center justify-between border-b border-white/10 bg-kp-black/90 px-5 py-4 backdrop-blur lg:px-10">
      <button class="rounded-lg border border-white/15 px-3 py-1.5 lg:hidden" @click="nav=!nav">☰</button>
      <h1 class="font-serif text-2xl text-white">@yield('title')</h1>
      <div>@yield('actions')</div>
    </header>
    <main class="p-5 lg:p-10">
      @if(session('status'))<div class="mb-6 rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">{{ session('status') }}</div>@endif
      @if($errors->any())<div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 px-5 py-3 text-sm text-red-300"><ul class="list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
      @yield('content')
    </main>
  </div>
</div>
@else
  @yield('content')
@endauth
</body></html>
