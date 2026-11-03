{{-- Bloc texte + image des pages éditoriales.
     Paramètres : id, eyebrow, title, blocks (liste [titre, texte]), list (puces), image, alt, quote, quoteBy. --}}
<section class="kp-wrap kp-section kp-editorial-split" @if(filled($id ?? null)) id="{{ $id }}" @endif>
  <div class="kp-section__intro kp-editorial-split__text">
    @if(filled($eyebrow ?? null))<span class="kp-eyebrow">{{ $eyebrow }}</span>@endif
    @if(filled($title ?? null))<h2 class="heading-l kp-balance">{{ $title }}</h2>@endif
    @foreach(collect($blocks ?? [])->filter(fn ($block) => filled($block[1] ?? null)) as [$blockTitle, $blockText])
      <div class="kp-editorial-split__block">
        @if(filled($blockTitle))<h3 class="heading-s">{{ $blockTitle }}</h3>@endif
        <p class="body kp-muted">{{ $blockText }}</p>
      </div>
    @endforeach
    @php($splitList = collect($list ?? [])->filter()->values())
    @if($splitList->isNotEmpty())
      <ul class="kp-home-banner__items kp-editorial-split__list">@foreach($splitList as $entry)<li>{{ $entry }}</li>@endforeach</ul>
    @endif
    @if(filled($quote ?? null))
      <blockquote class="kp-editorial__quote"><p>« {{ $quote }} »</p>@if(filled($quoteBy ?? null))<footer class="body-s kp-muted">{{ $quoteBy }}</footer>@endif</blockquote>
    @endif
  </div>
  @if(filled($image ?? null))<img class="kp-editorial-split__image" src="{{ cms_asset($image) }}" alt="{{ $alt ?? '' }}" loading="lazy">@endif
</section>
