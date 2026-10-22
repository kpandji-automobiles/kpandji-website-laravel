{{-- Port Blade du composant Sav (kpandji2026 final/components/kp/Sav.tsx) — textes éditables : Admin › Contenu › S.A.V. --}}
@php($savTel = preg_replace('/[^\d+]/', '', (string) content('sav.phone')))
<section id="sav" class="relative overflow-hidden bg-kp-black" aria-labelledby="sav-title">
  {!! cms_edit('sav', 'Modifier cette section', '5.5rem') !!}
  <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(900px_520px_at_15%_10%,rgba(201,169,98,0.10),transparent_60%)]"></div>

  <div class="relative mx-auto grid w-full max-w-[1680px] items-center gap-10 px-5 py-16 sm:px-8 sm:py-20 md:py-24 lg:grid-cols-2 lg:gap-16 lg:px-12 lg:py-28 xl:px-16">
    {{-- Image --}}
    <div class="relative overflow-hidden rounded-3xl border border-white/10 shadow-[0_40px_120px_-48px_rgba(0,0,0,0.95)] ring-1 ring-white/5">
      <div class="relative aspect-[4/3] w-full">
        <img src="{{ cms_asset(content('sav.image')) }}" alt="@content('sav.image_alt')" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/55 via-transparent to-transparent"></div>
      </div>
    </div>

    {{-- Contact --}}
    <div class="max-w-xl">
      <p class="inline-flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.28em] text-kp-gold/95">
        <span aria-hidden="true" class="h-px w-8 bg-kp-gold/60"></span>
        @content('sav.eyebrow')
      </p>

      <h2 id="sav-title" class="mt-5 font-serif text-[clamp(1.75rem,4.2vw,3rem)] font-normal leading-[1.1] tracking-tight text-white">@content('sav.title')</h2>

      <p class="mt-5 text-[14px] leading-relaxed text-white/55 [text-wrap:pretty] md:text-[15px]">@content('sav.text')</p>

      <ul class="mt-9 flex flex-col gap-5 border-t border-white/10 pt-8">
        <li class="flex items-start gap-4">
          <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-kp-gold/25 bg-kp-gold/10 text-kp-gold">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-[18px] w-[18px]" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0122 16.92z"/></svg>
          </span>
          <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-white/40">@content('sav.phone_label')</p>
            <a href="tel:{{ $savTel }}" class="mt-1 inline-block font-sans text-[15px] text-white/90 transition hover:text-kp-gold">@content('sav.phone')</a>
          </div>
        </li>
        <li class="flex items-start gap-4">
          <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-kp-gold/25 bg-kp-gold/10 text-kp-gold">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-[18px] w-[18px]" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
          </span>
          <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-white/40">@content('sav.email_label')</p>
            <a href="mailto:{{ content('sav.email') }}?subject={{ rawurlencode(content('sav.email_subject')) }}" class="mt-1 inline-block font-sans text-[15px] text-white/90 transition hover:text-kp-gold">@content('sav.email')</a>
          </div>
        </li>
      </ul>

      <div class="mt-9">
        <a href="tel:{{ $savTel }}" class="inline-flex min-h-11 items-center justify-center rounded-full bg-kp-gold px-8 py-3.5 font-sans text-[11px] font-semibold uppercase tracking-[0.18em] text-black shadow-[0_12px_40px_-12px_rgba(201,169,98,0.55)] transition duration-300 hover:bg-[#d4b56e] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-kp-gold/55">@content('sav.cta')</a>
      </div>
    </div>
  </div>
</section>
