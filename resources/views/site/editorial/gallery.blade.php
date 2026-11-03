{{-- Galerie photo des pages éditoriales. Paramètres : eyebrow, title, text, items (liste [image, alt, tag, title]). --}}
@php($galleryItems = collect($items ?? [])->filter(fn ($item) => filled($item['image'] ?? null))->values())
@if($galleryItems->isNotEmpty())
<section class="kp-wrap kp-section">
  <div class="kp-section__intro">
    @if(filled($eyebrow ?? null))<span class="kp-eyebrow">{{ $eyebrow }}</span>@endif
    @if(filled($title ?? null))<h2 class="heading-l kp-balance">{{ $title }}</h2>@endif
    @if(filled($text ?? null))<p class="body-l kp-muted kp-editorial__lead">{{ $text }}</p>@endif
  </div>
  <div class="kp-editorial-gallery">
    @foreach($galleryItems as $item)
      <figure>
        <img src="{{ cms_asset($item['image']) }}" alt="{{ $item['alt'] ?? '' }}" loading="lazy">
        @if(filled($item['tag'] ?? null) || filled($item['title'] ?? null))
          <figcaption>@if(filled($item['tag'] ?? null))<span class="kp-eyebrow">{{ $item['tag'] }}</span>@endif @if(filled($item['title'] ?? null))<span class="heading-s">{{ $item['title'] }}</span>@endif</figcaption>
        @endif
      </figure>
    @endforeach
  </div>
</section>
@endif
