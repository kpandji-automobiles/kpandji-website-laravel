{{-- Bandeau d'appel à l'action en bas de page. Paramètres : image, kicker, title, text, actions ([label, url, variante]), badges. --}}
<section class="kp-editorial-cta">
  @if(filled($image ?? null))<img src="{{ cms_asset($image) }}" alt="" loading="lazy">@endif
  <div class="kp-wrap kp-editorial-cta__inner">
    @if(filled($kicker ?? null))<span class="kp-eyebrow kp-on-stage-gold">{{ $kicker }}</span>@endif
    <h2 class="heading-l kp-balance">{{ $title }}</h2>
    @if(filled($text ?? null))<p class="body-l kp-fiche-hero__lead">{{ $text }}</p>@endif
    @php($ctaActions = collect($actions ?? [])->filter(fn ($action) => filled($action[0] ?? null)))
    @if($ctaActions->isNotEmpty())
      <div class="kp-home-hero__actions">
        @foreach($ctaActions as [$label, $url, $variant])<a class="kp-btn kp-btn--{{ $variant }}" href="{{ cms_url($url) }}">{{ $label }}</a>@endforeach
      </div>
    @endif
    @php($ctaBadges = collect($badges ?? [])->filter()->values())
    @if($ctaBadges->isNotEmpty())<ul class="kp-home-hero__chips">@foreach($ctaBadges as $badge)<li>{{ $badge }}</li>@endforeach</ul>@endif
  </div>
</section>
