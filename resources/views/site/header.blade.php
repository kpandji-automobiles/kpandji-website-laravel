{{-- En-tête du site refondu (design system KPANDJI). Textes et liens : Contenus › Header. --}}
@php
  $nav = collect(content('header.nav'))
    ->filter(fn ($item) => ($item['visible'] ?? '1') !== '0' && trim($item['label'] ?? '') !== '')
    ->map(fn ($item) => ['label' => $item['label'], 'href' => cms_url($item['url'] ?? ''), 'current' => cms_active($item['url'] ?? '')])
    ->concat(\App\Models\Page::where('in_nav', true)->where('published', true)->orderBy('nav_order')->get()
      ->map(fn ($page) => ['label' => $page->title, 'href' => $page->url(), 'current' => request()->is($page->slug)]))
    ->values();
  $utility = collect(content('header.utility'))->filter(fn ($item) => trim($item['label'] ?? '') !== '')->values();
  $tagline = collect([content('header.tagline1'), content('header.tagline2')])->filter()->implode(', ');
  $workspace = auth()->user()?->workspaceUrl();
  $loginError = session('errors')?->getBag('kpLogin')->first('login');
  $loginOpen = ! auth()->check() && ! request()->routeIs('connexion') && (session('kp_login_open') || $loginError);
@endphp
<header class="kp-header kp-site-header" x-data="{ menu: false, search: false, login: @js($loginOpen) }" @keydown.escape.window="menu = false; search = false; login = false" @kp-login.window="login = true; menu = false" data-kp-header>
  <div class="kp-header__util">
    <span>{{ $tagline }}</span>
    <nav class="kp-site-header__util-links" aria-label="Liens KPANDJI">
      @foreach($utility as $item)
        <a href="{{ cms_url($item['url'] ?? '') }}">{{ $item['label'] }}</a>
      @endforeach
    </nav>
  </div>
  <div class="kp-header__bar">
    <a class="kp-header__brand" href="{{ route('home') }}" aria-label="{{ content('header.brand') }} — accueil">
      <span class="kp-header__mark"><img src="{{ cms_asset(content('header.logo')) }}" alt="" width="30" height="30"></span>
      <span class="kp-site-header__brand-name">{{ content('header.brand') }}</span>
    </a>
    <nav class="kp-header__nav kp-site-header__nav" id="kp-nav" :class="menu && 'is-open'" aria-label="Navigation principale">
      @foreach($nav as $item)
        <a href="{{ $item['href'] }}" @if($item['current']) aria-current="page" @endif>{{ $item['label'] }}</a>
      @endforeach
      @if($workspace)
        <a class="kp-site-header__nav-account" href="{{ $workspace }}">Mon espace</a>
      @else
        <a class="kp-site-header__nav-account" href="{{ route('connexion') }}" @click.prevent="login = true; menu = false">{{ content('header.account_label') }}</a>
      @endif
    </nav>
    <div class="kp-header__tools">
      <button type="button" class="kp-btn kp-btn--secondary kp-btn--sm kp-site-header__icon" aria-label="Rechercher" aria-controls="kp-search-panel" :aria-expanded="search" @click="search = !search; menu = false; search && $nextTick(() => $refs.q.focus())">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
      </button>
      @if($workspace)
        <a class="kp-btn kp-btn--secondary kp-btn--sm kp-site-header__account" href="{{ $workspace }}">Mon espace</a>
        <form method="POST" action="{{ route('account.logout') }}" class="kp-site-header__account">@csrf<button class="kp-btn kp-btn--link kp-site-header__logout">Déconnexion</button></form>
      @else
        <a class="kp-btn kp-btn--secondary kp-btn--sm kp-site-header__account" href="{{ route('connexion') }}" @click.prevent="login = true; menu = false">{{ content('header.account_label') }}</a>
      @endif
      <a class="kp-btn kp-btn--gold kp-btn--sm kp-site-header__cta" href="{{ cms_url(content('header.cta_url')) }}">{{ content('header.cta_label') }}</a>
      <button type="button" class="kp-btn kp-btn--secondary kp-btn--sm kp-site-header__icon kp-site-header__burger" aria-label="Menu" aria-controls="kp-nav" :aria-expanded="menu" @click="menu = !menu; search = false">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
    </div>
  </div>
  <div class="kp-site-header__search" id="kp-search-panel" x-show="search" x-cloak>
    <form class="kp-search" method="GET" action="{{ route('cars.index') }}" role="search">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" class="kp-muted"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
      <input type="search" name="q" x-ref="q" placeholder="{{ content('header.search_placeholder') }}" aria-label="Rechercher un modèle">
      <button class="kp-search__go" aria-label="Lancer la recherche">→</button>
    </form>
  </div>
  @guest
  <div class="kp-login" id="kp-login" x-show="login" x-cloak role="dialog" aria-modal="true" aria-labelledby="kp-login-title" @click.self="login = false" data-kp-login>
    <form class="kp-login__panel" method="POST" action="{{ route('account.login') }}" x-effect="login && $nextTick(() => $refs.loginEmail.focus())">
      @csrf
      <div class="kp-login__head">
        <div>
          <span class="kp-eyebrow">{{ content('header.login_kicker') }}</span>
          <h2 class="heading-m" id="kp-login-title">{{ content('header.login_title') }}</h2>
          <p class="body-s kp-muted">{{ content('header.login_subtitle') }}</p>
        </div>
        <button type="button" class="kp-btn kp-btn--secondary kp-btn--sm kp-site-header__icon" aria-label="{{ content('header.login_close') }}" @click="login = false">✕</button>
      </div>
      @if($loginError)<p class="kp-login__error" role="alert">{{ $loginError }}</p>@endif
      <div class="kp-field{{ $loginError ? ' kp-field--error' : '' }}">
        <input class="kp-field__control" id="kp-login-email" x-ref="loginEmail" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required maxlength="190">
        <label class="kp-field__label" for="kp-login-email">{{ content('header.login_email') }}</label>
      </div>
      <div class="kp-field{{ $loginError ? ' kp-field--error' : '' }}">
        <input class="kp-field__control" id="kp-login-password" type="password" name="password" autocomplete="current-password" required>
        <label class="kp-field__label" for="kp-login-password">{{ content('header.login_password') }}</label>
      </div>
      <label class="kp-login__remember body-s"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> {{ content('header.login_remember') }}</label>
      <button class="kp-btn kp-btn--primary">{{ content('header.login_submit') }}</button>
    </form>
  </div>
  @endguest
</header>
