{{-- Grille de tuiles éditoriales (piliers, étapes, leviers, programmes).
     Paramètres : id, eyebrow, title, text, items (liste [tag, title, text, url]), alt (fond alterné). --}}
@php($tileItems = collect($items ?? [])->filter(fn ($item) => filled($item['title'] ?? null))->values())
@if($tileItems->isNotEmpty())
<section class="kp-editorial-band{{ ($alt ?? false) ? ' kp-editorial-band--alt' : '' }}" @if(filled($id ?? null)) id="{{ $id }}" @endif>
  <div class="kp-wrap kp-section">
    <div class="kp-section__intro">
      @if(filled($eyebrow ?? null))<span class="kp-eyebrow">{{ $eyebrow }}</span>@endif
      @if(filled($title ?? null))<h2 class="heading-l kp-balance">{{ $title }}</h2>@endif
      @if(filled($text ?? null))<p class="body-l kp-muted kp-editorial__lead">{{ $text }}</p>@endif
    </div>
    <div class="kp-home-tiles">
      @foreach($tileItems as $item)
        @if(filled($item['url'] ?? null))
          <a class="kp-tile" href="{{ cms_url($item['url']) }}"><span class="kp-tile__arrow" aria-hidden="true">↗</span>@if(filled($item['tag'] ?? null))<span class="kp-eyebrow">{{ $item['tag'] }}</span>@endif<span class="kp-tile__title">{{ $item['title'] }}</span>@if(filled($item['text'] ?? null))<span class="kp-tile__text">{{ $item['text'] }}</span>@endif</a>
        @else
          <div class="kp-tile kp-editorial__tile">@if(filled($item['tag'] ?? null))<span class="kp-eyebrow">{{ $item['tag'] }}</span>@endif<span class="kp-tile__title">{{ $item['title'] }}</span>@if(filled($item['text'] ?? null))<span class="kp-tile__text">{{ $item['text'] }}</span>@endif</div>
        @endif
      @endforeach
    </div>
  </div>
</section>
@endif
