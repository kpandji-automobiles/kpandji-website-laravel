{{-- Fenêtre publicitaire (back-office › Fenêtre publicitaire). Fermée : plus affichée selon la fréquence choisie, jusqu'à la campagne suivante. --}}
@php($popup = \App\Support\AdPopup::current(request()))
@if($popup)
<div class="kp-popup" x-data="kpPopup(@js($popup['version']), @js($popup['frequency']))" x-show="open" x-cloak
     @keydown.escape.window="close()" @click.self="close()" role="dialog" aria-modal="true" aria-label="{{ $popup['title'] ?: 'Offre KPANDJI' }}" data-kp-popup="{{ $popup['version'] }}">
  <div class="kp-popup__panel">
    <button type="button" class="kp-popup__close" x-ref="close" @click="close()" aria-label="Fermer">✕</button>
    @if(filled($popup['link']))
      <a href="{{ cms_url($popup['link']) }}" @click="close()" tabindex="-1"><img class="kp-popup__image" src="{{ cms_asset($popup['image']) }}" alt="{{ $popup['title'] }}"></a>
    @else
      <img class="kp-popup__image" src="{{ cms_asset($popup['image']) }}" alt="{{ $popup['title'] }}">
    @endif
    @if(filled($popup['title']) || filled($popup['text']) || filled($popup['link']))
      <div class="kp-popup__body">
        @if(filled($popup['title']))<p class="heading-m">{{ $popup['title'] }}</p>@endif
        @if(filled($popup['text']))<p class="body kp-muted">{{ $popup['text'] }}</p>@endif
        @if(filled($popup['link']))<a class="kp-btn kp-btn--gold" href="{{ cms_url($popup['link']) }}" @click="close()">{{ $popup['cta'] }}</a>@endif
      </div>
    @endif
  </div>
</div>
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  window.Alpine.data('kpPopup', (version, frequency) => ({
    open: false,
    store() { return frequency === 'session' ? window.sessionStorage : window.localStorage },
    init() {
      const days = { day: 1, week: 7 }[frequency];
      try {
        const seen = JSON.parse(this.store().getItem('kp-popup') || 'null');
        const recent = seen && seen.v === version && (frequency === 'session' || (days && Date.now() - seen.at < days * 86400000));
        if (frequency !== 'always' && recent) return;
      } catch (e) {}
      setTimeout(() => { this.open = true; this.$nextTick(() => this.$refs.close.focus()) }, 1200);
    },
    close() {
      this.open = false;
      try { this.store().setItem('kp-popup', JSON.stringify({ v: version, at: Date.now() })) } catch (e) {}
    },
  }));
});
</script>
@endpush
@endif
