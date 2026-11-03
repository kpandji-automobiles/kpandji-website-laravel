{{-- Carte véhicule (design system) : accueil, catalogue. --}}
<article class="kp-vcard" data-kp-vcard="{{ $car->slug }}">
  <a class="kp-vcard__media" href="{{ route('cars.show', $car) }}" tabindex="-1" aria-hidden="true">
    @if($car->image)<img src="{{ asset($car->image) }}" alt="" loading="lazy">@endif
    @if($car->discountedPrice())<span class="kp-vcard__badge">Promo</span>@endif
  </a>
  <div class="kp-vcard__body">
    @if($car->category)<span class="kp-eyebrow">{{ $car->category }}</span>@endif
    <h3 class="kp-vcard__name"><a href="{{ route('cars.show', $car) }}">{{ $car->name }}</a></h3>
    @if(filled($car->highlights))
      <ul class="kp-vcard__specs">
        @foreach(array_slice($car->highlights, 0, 3) as $highlight)<li>{{ $highlight }}</li>@endforeach
      </ul>
    @endif
    @include('site.price', ['car' => $car])
    <div class="kp-vcard__actions">
      <a class="kp-btn kp-btn--primary kp-btn--sm" href="{{ route('cars.show', $car) }}">Voir le modèle</a>
      <a class="kp-btn kp-btn--secondary kp-btn--sm" href="{{ route('essai', ['modele' => $car->slug]) }}">Essayer</a>
    </div>
  </div>
</article>
