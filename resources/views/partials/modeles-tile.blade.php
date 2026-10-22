{{-- MediaTile / tuile vidéo de Modeles.tsx --}}
<div class="h-full w-full rounded-[22px] bg-kp-bg/80 p-1.5 ring-1 ring-white/[0.06]">
  <div class="group relative h-full w-full overflow-hidden rounded-2xl border border-white/10 {{ isset($video) ? 'bg-black/30' : 'bg-white/5' }} shadow-[0_24px_70px_-30px_rgba(0,0,0,0.9)]">
    @isset($video)
      @if($video)<video data-src="{{ cms_asset($video) }}" class="h-full w-full object-cover" muted loop playsinline controls preload="none" aria-label="{{ $alt }}"></video>@endif
      <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-black/15 via-transparent to-black/40"></div>
    @else
      @if($src)<img src="{{ cms_asset($src) }}" alt="{{ $alt }}" loading="{{ ($eager ?? false) ? 'eager' : 'lazy' }}" decoding="async" class="absolute inset-0 h-full w-full object-cover object-center transition duration-700 ease-out group-hover:scale-[1.03]">@endif
      <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-black/10 via-transparent to-black/35"></div>
    @endisset
    <div class="pointer-events-none absolute inset-0 ring-1 ring-white/[0.06]"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-y-0 left-0 w-[40%] -translate-x-[130%] bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 transition duration-700 ease-out group-hover:translate-x-[260%] group-hover:opacity-100"></div>
  </div>
</div>
