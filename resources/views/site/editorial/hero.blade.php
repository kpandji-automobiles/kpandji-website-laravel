{{-- Visuel plein cadre des pages éditoriales.
     Paramètres : image, alt, kicker, title, text, actions (liste [label, url, variante gold|stage]), crumbs (libellé de la page courante). --}}
<section class="kp-hero kp-editorial-hero" data-kp-editorial-hero>
  @if(filled($image ?? null))<img class="kp-hero__media" src="{{ cms_asset($image) }}" alt="{{ $alt ?? '' }}" fetchpriority="high">@endif
  <div class="kp-hero__scrim"></div>
  <div class="kp-hero__content kp-wrap">
    @if(filled($crumbs ?? null))<nav class="kp-fiche-hero__crumbs" aria-label="Fil d'Ariane"><a href="{{ route('home') }}">Accueil</a> / <span aria-current="page">{{ $crumbs }}</span></nav>@endif
    @if(filled($kicker ?? null))<span class="kp-eyebrow kp-on-stage-gold">{{ $kicker }}</span>@endif
    <h1 class="kp-hero__title">{{ $title }}</h1>
    @if(filled($text ?? null))<p class="body-l kp-fiche-hero__lead">{{ $text }}</p>@endif
    @php($heroActions = collect($actions ?? [])->filter(fn ($action) => filled($action[0] ?? null)))
    @if($heroActions->isNotEmpty())
      <div class="kp-home-hero__actions">
        @foreach($heroActions as [$label, $url, $variant])
          <a class="kp-btn kp-btn--{{ $variant }}" href="{{ cms_url($url) }}">{{ $label }}</a>
        @endforeach
      </div>
    @endif
  </div>
</section>
