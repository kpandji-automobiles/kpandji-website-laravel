{{-- Visionneuse photo plein écran (showroom, fiche modèle). Tout lien <a data-kp-lightbox="groupe"> l'ouvre sur
     les photos de son groupe, dans l'ordre de la page ; data-kp-lightbox-title (titre), data-caption (légende).
     Sans JavaScript, ou en Cmd/Ctrl-clic, le lien ouvre simplement la photo. --}}
<dialog class="kp-lightbox" x-data="kpLightbox()" x-ref="dialog" data-kp-lightbox-dialog
        :aria-label="title ? 'Galerie photo — ' + title : 'Galerie photo'"
        @close="closed()" @click.self="close()"
        @keydown.arrow-right.prevent="next()" @keydown.arrow-left.prevent="prev()"
        @keydown.home.prevent="go(0)" @keydown.end.prevent="go(items.length - 1)">
  <div class="kp-lightbox__bar">
    <p class="kp-lightbox__title" x-text="title"></p>
    <p class="kp-lightbox__counter" aria-live="polite" aria-atomic="true">
      <span class="kp-sr-only">Photo </span><span class="kp-lightbox__current" x-text="index + 1"></span><span aria-hidden="true"> / </span><span class="kp-sr-only"> sur </span><span x-text="items.length"></span>
    </p>
    <button type="button" class="kp-lightbox__btn kp-lightbox__close" @click="close()" aria-label="Fermer la galerie">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
    </button>
  </div>

  <div class="kp-lightbox__stage" @click.self="close()" @touchstart.passive="touchStart($event)" @touchend="touchEnd($event)">
    <button type="button" class="kp-lightbox__btn kp-lightbox__nav kp-lightbox__nav--prev" @click="prev()" x-show="items.length > 1" aria-label="Photo précédente">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
    </button>
    <figure class="kp-lightbox__figure" @click.self="close()">
      <img class="kp-lightbox__img" x-ref="img" :class="{ 'is-loading': loading }" :style="'--kp-dir:' + dir" :src="current.src" :alt="current.alt" x-on:load="loading = false" x-on:error="loading = false" draggable="false">
      <figcaption class="kp-lightbox__caption" x-show="current.caption" x-text="current.caption"></figcaption>
    </figure>
    <button type="button" class="kp-lightbox__btn kp-lightbox__nav kp-lightbox__nav--next" @click="next()" x-show="items.length > 1" aria-label="Photo suivante">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
    </button>
  </div>

  <div class="kp-lightbox__thumbs" x-ref="thumbs" x-show="items.length > 1">
    <template x-for="(item, i) in items" :key="i">
      <button type="button" class="kp-lightbox__thumb" :aria-current="i === index ? 'true' : 'false'" :aria-label="'Afficher la photo ' + (i + 1)" @click="go(i)">
        <img :src="item.src" alt="" loading="lazy" draggable="false">
      </button>
    </template>
  </div>
</dialog>

@once
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  window.Alpine.data('kpLightbox', () => ({
    items: [], index: 0, title: '', dir: 0, loading: false, opener: null, touchX: null, touchY: null,
    get current() { return this.items[this.index] || {} },
    init() {
      document.addEventListener('click', (event) => {
        const link = event.target.closest('a[data-kp-lightbox]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        const links = [...document.querySelectorAll('a[data-kp-lightbox="' + CSS.escape(link.dataset.kpLightbox) + '"]')];
        this.items = links.map((a) => ({ src: a.href, alt: a.dataset.alt || a.querySelector('img')?.alt || '', caption: a.dataset.caption || '' }));
        this.title = link.dataset.kpLightboxTitle || '';
        this.opener = link;
        this.show(Math.max(0, links.indexOf(link)));
      });
    },
    show(i) {
      this.dir = 0;
      this.index = -1;
      this.go(i);
      document.documentElement.classList.add('kp-lightbox-open');
      this.$refs.dialog.showModal();
    },
    close() { if (this.$refs.dialog.open) this.$refs.dialog.close() },
    closed() {
      document.documentElement.classList.remove('kp-lightbox-open');
      this.opener?.focus({ preventScroll: true });
    },
    go(i) {
      const n = this.items.length;
      if (!n) return;
      const target = ((i % n) + n) % n;
      if (target === this.index) return;
      this.loading = this.items[target].src !== this.current.src;
      this.index = target;
      [1, -1].forEach((d) => { const item = this.items[(target + d + n) % n]; if (item) new Image().src = item.src });
      this.$nextTick(() => {
        // Image déjà affichée ou en cache : pas d'événement load à attendre.
        if (this.$refs.img.complete && this.$refs.img.naturalWidth) this.loading = false;
        this.$refs.thumbs.querySelectorAll('button')[target]?.scrollIntoView({ block: 'nearest', inline: 'center' });
      });
    },
    next() { this.dir = 1; this.go(this.index + 1) },
    prev() { this.dir = -1; this.go(this.index - 1) },
    touchStart(event) { this.touchX = event.changedTouches[0].clientX; this.touchY = event.changedTouches[0].clientY },
    touchEnd(event) {
      if (this.touchX === null) return;
      const dx = event.changedTouches[0].clientX - this.touchX, dy = event.changedTouches[0].clientY - this.touchY;
      this.touchX = this.touchY = null;
      if (Math.abs(dx) > 48 && Math.abs(dx) > Math.abs(dy)) dx < 0 ? this.next() : this.prev();
    },
  }));
});
</script>
@endpush
@endonce
