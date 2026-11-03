{{-- Pied de page du site refondu (design system KPANDJI). Textes et liens : Contenus › Footer. --}}
@php
  $footerCars = \App\Models\Car::visible()->get(['name', 'slug']);
  $links = fn (string $key) => collect(content($key))->filter(fn ($item) => trim($item['label'] ?? '') !== '')->values();
  $phones = collect(content('footer.phones'))->filter(fn ($item) => trim($item['number'] ?? '') !== '')->values();
@endphp
<footer class="kp-site-footer" data-kp-footer>
  <div class="kp-site-footer__grid">
    <div class="kp-site-footer__about">
      <a class="kp-site-footer__brand" href="{{ route('home') }}"><img src="{{ cms_asset(content('header.logo')) }}" alt="" width="40" height="40">KPANDJI</a>
      <p>{{ content('footer.blurb') }}</p>
      @if(filled(content('footer.seat_address')))
        <p><span class="kp-eyebrow">{{ content('footer.seat_label') }}</span><br>{{ content('footer.seat_address') }}</p>
      @endif
    </div>
    <nav aria-label="Modèles">
      <span class="kp-eyebrow">Modèles</span>
      @foreach($footerCars as $car)
        <a href="{{ route('cars.show', $car) }}">{{ $car->name }}</a>
      @endforeach
    </nav>
    <nav aria-label="Services">
      <span class="kp-eyebrow">Services</span>
      @foreach($links('footer.services') as $item)
        <a href="{{ cms_url($item['url'] ?? '') }}">{{ $item['label'] }}</a>
      @endforeach
    </nav>
    <nav aria-label="La marque">
      <span class="kp-eyebrow">La marque</span>
      @foreach($links('header.utility') as $item)
        <a href="{{ cms_url($item['url'] ?? '') }}">{{ $item['label'] }}</a>
      @endforeach
      <a href="{{ cms_url(content('footer.write_url')) }}">{{ content('footer.write_cta') }}</a>
    </nav>
    <div class="kp-site-footer__contact">
      <span class="kp-eyebrow">{{ content('footer.contact') }}</span>
      @foreach($phones as $phone)
        <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone['number']) }}"><span>{{ $phone['label'] ?? '' }}</span> {{ $phone['number'] }}</a>
      @endforeach
      @if(filled(content('footer.email')))
        <a href="mailto:{{ content('footer.email') }}"><span>{{ content('footer.email_label') }}</span> {{ content('footer.email') }}</a>
      @endif
    </div>
  </div>
  @if($links('footer.resource_links')->isNotEmpty())
    <nav class="kp-site-footer__resources" aria-label="{{ content('footer.resources') }}">
      <span class="kp-eyebrow">{{ content('footer.resources') }}</span>
      @foreach($links('footer.resource_links') as $item)
        <a href="{{ cms_url($item['url'] ?? '') }}" target="_blank" rel="noopener">{{ $item['label'] }}</a>
      @endforeach
    </nav>
  @endif
  <div class="kp-site-footer__legal">
    <span>© {{ now()->year }} KPANDJI AUTOMOBILES. {{ content('footer.rights') }}</span>
    @auth
      <a href="{{ auth()->user()->workspaceUrl() ?? route('home') }}">Mon espace</a>
    @else
      <a href="#kp-login" x-data @click.prevent="$dispatch('kp-login')">{{ content('header.account_label') }}</a>
    @endauth
  </div>
</footer>
