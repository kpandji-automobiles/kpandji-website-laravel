<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
@hasSection('seo_title')<title>@yield('seo_title')</title>@else<title>@yield('title', 'KPANDJI') — {{ config('promo.title') }}</title>@endif
@hasSection('meta_description')<meta name="description" content="@yield('meta_description')">@endif
@stack('meta')
<link rel="icon" href="{{ cms_asset(content('header.logo')) }}">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;800&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>[x-cloak]{display:none!important}
.kp-cms-edit{position:absolute;right:.75rem;z-index:45;display:inline-flex;align-items:center;gap:.4rem;background:linear-gradient(135deg,#e6cf8f,#c9a962);color:#0a0a0a!important;font:700 11px/1 Montserrat,system-ui,sans-serif;letter-spacing:.06em;text-transform:none;padding:.55rem .85rem;border-radius:999px;text-decoration:none!important;box-shadow:0 0 0 1px rgba(0,0,0,.55),0 0 0 3px rgba(201,169,98,.35),0 10px 26px -8px rgba(0,0,0,.8);transition:transform .2s,box-shadow .2s;white-space:nowrap}
.kp-cms-edit:hover{transform:translateY(-1px);box-shadow:0 0 0 1px rgba(0,0,0,.55),0 0 0 4px rgba(201,169,98,.55),0 14px 30px -8px rgba(0,0,0,.85)}
.kp-cms-edit:focus-visible{outline:2px solid #fff;outline-offset:2px}
@keyframes kp-marquee{from{transform:translate3d(0,0,0)}to{transform:translate3d(-50%,0,0)}}.kp-marquee-track{animation:kp-marquee 36s linear infinite}
@keyframes kp-scroll-bounce{0%,100%{transform:translateY(0);opacity:.85}50%{transform:translateY(10px);opacity:.35}}.kp-scroll-dot{animation:kp-scroll-bounce 1.8s ease-in-out infinite}
.kp-grain{background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.35'/%3E%3C/svg%3E");mix-blend-mode:overlay}
.kp-hide-scrollbar{-ms-overflow-style:none;scrollbar-width:none}.kp-hide-scrollbar::-webkit-scrollbar{display:none}
.kp-clamp-2{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2;overflow:hidden}
.kp-copy-visible .kp-line{opacity:1;transform:none;filter:blur(0);transition:opacity .85s cubic-bezier(.22,1,.36,1) calc(.15s + var(--i)* .12s),transform .85s cubic-bezier(.22,1,.36,1) calc(.15s + var(--i)* .12s),filter .85s cubic-bezier(.22,1,.36,1) calc(.15s + var(--i)* .12s)}
.kp-copy-hidden .kp-line{opacity:0;transform:translateY(28px);filter:blur(6px);transition:opacity .45s cubic-bezier(.4,0,1,1) calc((3 - var(--i))* .055s),transform .45s cubic-bezier(.4,0,1,1) calc((3 - var(--i))* .055s),filter .45s cubic-bezier(.4,0,1,1) calc((3 - var(--i))* .055s)}
.kp-pillars-on .kp-pillar{opacity:1;transform:none;transition:opacity .7s cubic-bezier(.22,1,.36,1) calc(.78s + var(--p)* .09s),transform .7s cubic-bezier(.22,1,.36,1) calc(.78s + var(--p)* .09s)}
.kp-pillars-off .kp-pillar{opacity:0;transform:translateY(20px)}
@media (prefers-reduced-motion:reduce){.kp-marquee-track,.kp-scroll-dot{animation:none!important}.kp-copy-hidden .kp-line,.kp-copy-visible .kp-line,.kp-pillars-off .kp-pillar,.kp-pillars-on .kp-pillar{opacity:1!important;transform:none!important;filter:none!important;transition:none!important}}
@keyframes kpSlideL{from{opacity:0;transform:translateX(56px)}to{opacity:1;transform:none}}@keyframes kpSlideR{from{opacity:0;transform:translateX(-56px)}to{opacity:1;transform:none}}@keyframes kpLine{from{opacity:0;transform:scaleY(0)}to{opacity:1;transform:scaleY(1)}}.kp-slide-l{animation:kpSlideL .85s cubic-bezier(.22,1,.36,1) .18s both}.kp-slide-r{animation:kpSlideR .85s cubic-bezier(.22,1,.36,1) .18s both}.kp-line{animation:kpLine .55s cubic-bezier(.22,1,.36,1) both}@keyframes kp-menu-stagger{from{opacity:0;transform:translate3d(14px,0,0)}to{opacity:1;transform:none}}.kp-menu-panel[data-open=true] .kp-mobile-link{opacity:0;animation:kp-menu-stagger .55s cubic-bezier(.22,1,.36,1) forwards}@media (prefers-reduced-motion:reduce){.kp-menu-panel[data-open=true] .kp-mobile-link,.kp-slide-l,.kp-slide-r,.kp-line{opacity:1;animation:none}}@keyframes kpUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:none}}.kp-up{animation:kpUp .8s cubic-bezier(.2,.7,.2,1) both}
@keyframes kp-sweep{0%{left:-35%}60%,100%{left:130%}}
.kp-badge-sweep{animation:kp-sweep 3.2s ease-in-out infinite}
@keyframes kp-shine{0%,100%{opacity:.4;transform:scaleX(.7)}50%{opacity:1;transform:scaleX(1)}}
.kp-shine{animation:kp-shine 2.8s ease-in-out infinite;transform-origin:left}
@media (prefers-reduced-motion:reduce){.kp-badge-sweep,.kp-promo-badge .animate-ping,.kp-shine{animation:none}}

.kp-gold-shimmer{background-image:linear-gradient(100deg,#8a6d2f 0%,#c9a962 25%,#f3e2b3 45%,#fff6d8 50%,#f3e2b3 55%,#c9a962 75%,#8a6d2f 100%);background-size:250% 100%;-webkit-background-clip:text;filter:drop-shadow(0 0 18px rgba(201,169,98,.35));animation:kp-gold-shimmer 5s ease-in-out infinite}
@keyframes kp-gold-shimmer{0%,100%{background-position:100% 50%}50%{background-position:0% 50%}}
@media (prefers-reduced-motion:reduce){.kp-gold-shimmer{animation:none;background-position:50% 50%}.kp-live{animation:none}}

.kp-ribbon{position:relative;filter:drop-shadow(0 10px 22px rgba(201,169,98,.45)) drop-shadow(0 2px 3px rgba(0,0,0,.5))}
.kp-ribbon-body{position:relative;overflow:hidden;padding:.7rem 2.1rem .75rem 1.4rem;background:linear-gradient(100deg,#7a5c1e 0%,#a8873f 45%,#c9a962 58%,#a8873f 70%,#7a5c1e 100%);box-shadow:inset 0 1px 0 rgba(201,169,98,.9),inset 0 -1px 0 rgba(0,0,0,.35);clip-path:polygon(0 0,100% 0,calc(100% - 16px) 50%,100% 100%,0 100%,12px 50%);text-shadow:0 1px 2px rgba(0,0,0,.55)}
.kp-ribbon-body::before{content:"";position:absolute;inset:3px 18px 3px 14px;border-top:1px dashed rgba(245,236,214,.6);border-bottom:1px dashed rgba(245,236,214,.6);pointer-events:none}
.kp-ribbon-shine{position:absolute;top:0;bottom:0;left:-40%;width:30%;transform:skewX(-20deg);background:linear-gradient(90deg,transparent,rgba(241,220,160,.55),transparent);animation:kp-sweep 3.6s ease-in-out infinite;pointer-events:none}
.kp-corner{position:absolute;top:0;left:0;z-index:20;width:120px;height:120px;overflow:hidden;pointer-events:none}
.kp-corner span{position:absolute;top:24px;left:-38px;width:160px;transform:rotate(-45deg);background:linear-gradient(90deg,#7a5c1e,#a8873f 40%,#c9a962 50%,#a8873f 60%,#7a5c1e);color:#fff;text-shadow:0 1px 2px rgba(0,0,0,.5);outline:1px dashed rgba(245,236,214,.55);outline-offset:-4px;text-align:center;font-size:11px;font-weight:800;letter-spacing:.22em;text-transform:uppercase;padding:6px 0;box-shadow:0 4px 14px rgba(0,0,0,.45),0 0 18px rgba(201,169,98,.45);border-top:1px solid rgba(201,169,98,.9);border-bottom:1px solid rgba(0,0,0,.25)}
@media (prefers-reduced-motion:reduce){.kp-ribbon-shine{animation:none;display:none}}
.kp-prose h2{font-family:inherit;font-size:1.6rem;color:#fff;margin:1.6em 0 .5em;font-weight:600}.kp-prose h3{font-size:1.2rem;color:#fff;margin:1.3em 0 .4em;font-weight:600}.kp-prose p{margin:.8em 0}.kp-prose ul{list-style:disc;padding-left:1.4em}.kp-prose ol{list-style:decimal;padding-left:1.4em}.kp-prose a{color:#c9a962;text-decoration:underline}.kp-prose img{border-radius:12px;margin:1em 0}

/* ===== Hero : animations de texte (transform / opacity / filter) ===== */
@property --kp-a{syntax:'<angle>';inherits:false;initial-value:0deg}
.kp-wm{display:inline-block;overflow:hidden;vertical-align:top;padding:0 .04em .08em;margin:0 -.04em -.08em}
.kp-w,.kp-ch,.kp-bw,.kp-up,.kp-pop,.kp-card-in{display:inline-block;will-change:transform,opacity}
.kp-up,.kp-card-in{display:flex}
li.kp-up,.kp-card-in.inline-flex{display:inline-flex}
.kp-flip{perspective:900px}
.kp-ch{transform-origin:50% 85%;background:linear-gradient(110deg,#a8863f 0%,#f1dca0 25%,#c9a962 45%,#fff3cf 55%,#c9a962 65%,#a8863f 100%);background-size:400% 100%;-webkit-background-clip:text;background-clip:text;color:transparent;animation:kpShimmerTxt 5s linear infinite;animation-delay:calc(var(--d) * -1.5);padding-right:.02em}
@keyframes kpShimmerTxt{from{background-position:100% 0}to{background-position:-300% 0}}
.kp-rule{background:linear-gradient(90deg,#c9a962,#f1dca0 40%,rgba(201,169,98,0));transform-origin:left center;box-shadow:0 0 14px rgba(201,169,98,.55)}
.kp-draw::before{content:"";position:absolute;inset:-1px;border-radius:inherit;padding:1px;background:conic-gradient(from var(--kp-a),rgba(201,169,98,.15) 0deg,rgba(201,169,98,.15) 220deg,#c9a962 300deg,#fff3cf 330deg,#c9a962 350deg,rgba(201,169,98,.15) 360deg);-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;animation:kpSpin 5s linear infinite;pointer-events:none}
@keyframes kpSpin{to{--kp-a:360deg}}
.kp-badge-glow{animation:kpGlow 2.8s ease-in-out infinite}
@keyframes kpGlow{0%,100%{box-shadow:0 0 30px rgba(201,169,98,.45)}50%{box-shadow:0 0 55px rgba(241,220,160,.75)}}
.kp-mag{transition:transform .35s cubic-bezier(.22,1,.36,1),background-color .3s,color .3s,border-color .3s,box-shadow .3s}
.kp-mag:hover .kp-nudge{animation:kpNudge .9s cubic-bezier(.22,1,.36,1) infinite}
@keyframes kpNudge{0%,100%{transform:translateX(0)}50%{transform:translateX(5px)}}
/* entrée */
.kp-copy-visible :is(.kp-w,.kp-ch,.kp-bw,.kp-up,.kp-pop,.kp-card-in,.kp-rule){transition:transform .9s cubic-bezier(.22,1,.36,1) var(--d),opacity .8s cubic-bezier(.22,1,.36,1) var(--d),filter .8s cubic-bezier(.22,1,.36,1) var(--d);opacity:1;transform:none;filter:blur(0)}
.kp-copy-visible .kp-ch{transition-duration:.75s}
.kp-copy-visible .kp-pop{transition:transform .7s cubic-bezier(.34,1.56,.64,1) var(--d),opacity .4s ease var(--d),filter .5s ease var(--d)}
.kp-copy-visible .kp-rule{transition:transform 1s cubic-bezier(.22,1,.36,1) var(--d),opacity .4s ease var(--d)}
.kp-draw::before{transition:clip-path 1.1s cubic-bezier(.22,1,.36,1) calc(var(--d) + .15s)}
.kp-copy-visible .kp-draw::before{clip-path:inset(0 0 0 0 round 1rem)}
/* sortie (ordre inverse, plus rapide) */
.kp-copy-hidden :is(.kp-w,.kp-ch,.kp-bw,.kp-up,.kp-pop,.kp-card-in,.kp-rule){transition:transform .4s cubic-bezier(.55,0,1,.45) max(0s,calc((1.9s - var(--d)) * .28)),opacity .35s ease-in max(0s,calc((1.9s - var(--d)) * .28)),filter .35s ease-in max(0s,calc((1.9s - var(--d)) * .28));opacity:0}
.kp-copy-hidden .kp-w{transform:translate3d(0,110%,0);filter:blur(10px)}
.kp-copy-hidden .kp-ch{transform:rotateX(-95deg) translate3d(0,.15em,0);filter:blur(6px)}
.kp-copy-hidden .kp-bw{transform:translate3d(0,.45em,0);filter:blur(8px)}
.kp-copy-hidden .kp-up{transform:translate3d(0,22px,0);filter:blur(4px)}
.kp-copy-hidden .kp-pop{transform:scale(.6);filter:blur(4px)}
.kp-copy-hidden .kp-card-in{transform:translate3d(-36px,0,0) scale(.97);filter:blur(6px)}
.kp-copy-hidden .kp-rule{transform:scaleX(0)}
.kp-copy-hidden .kp-draw::before{clip-path:inset(0 100% 0 0 round 1rem);transition-duration:.3s;transition-delay:0s}
@media (prefers-reduced-motion:reduce){:is(.kp-copy-hidden,.kp-copy-visible) :is(.kp-w,.kp-ch,.kp-bw,.kp-up,.kp-pop,.kp-card-in,.kp-rule){opacity:1!important;transform:none!important;filter:none!important;transition:none!important}.kp-ch,.kp-draw::before,.kp-badge-glow,.kp-nudge{animation:none!important}.kp-draw::before{clip-path:none!important}.kp-mag{transform:none!important}}

/* ===== Cartes showroom premium ===== */
.kp-card{background:linear-gradient(180deg,rgba(28,24,16,.72),rgba(10,10,10,.92));backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);box-shadow:0 30px 70px -35px rgba(0,0,0,.9);transform-style:preserve-3d;transition:transform .6s cubic-bezier(.22,1,.36,1),box-shadow .6s cubic-bezier(.22,1,.36,1);will-change:transform}
.kp-card::before{content:"";position:absolute;inset:0;border-radius:inherit;padding:1px;background:linear-gradient(140deg,rgba(241,220,160,.55),rgba(201,169,98,.12) 30%,rgba(255,255,255,.04) 55%,rgba(201,169,98,.35));-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;opacity:.55;transition:opacity .5s;pointer-events:none;z-index:30}
.kp-card:hover{box-shadow:0 40px 90px -30px rgba(201,169,98,.38),0 0 0 1px rgba(201,169,98,.15)}
.kp-card:hover::before{opacity:1;background:linear-gradient(140deg,#f1dca0,rgba(201,169,98,.45) 35%,rgba(201,169,98,.15) 60%,#c9a962)}
.kp-card-sweep{position:absolute;inset:0;z-index:25;pointer-events:none;background:linear-gradient(105deg,transparent 35%,rgba(241,220,160,.13) 48%,rgba(255,255,255,.08) 52%,transparent 65%);transform:translateX(-120%);transition:transform 1.1s cubic-bezier(.22,1,.36,1)}
.kp-card:hover .kp-card-sweep{transform:translateX(120%)}
.kp-spot{position:absolute;left:50%;top:6%;width:120%;height:95%;transform:translateX(-50%);background:radial-gradient(ellipse 50% 55% at 50% 45%,rgba(201,169,98,.30),rgba(201,169,98,.08) 45%,transparent 70%);transition:opacity .6s;opacity:.85}
.kp-card:hover .kp-spot{opacity:1}
.kp-floor{position:absolute;left:12%;right:12%;bottom:6%;height:22px;border-radius:50%;background:radial-gradient(ellipse at center,rgba(0,0,0,.85),rgba(0,0,0,0) 70%);filter:blur(4px)}
.kp-stage::after{content:"";position:absolute;left:0;right:0;bottom:0;height:34%;background:linear-gradient(180deg,transparent,rgba(201,169,98,.05) 60%,rgba(255,255,255,.03));border-top:1px solid rgba(255,255,255,.04);pointer-events:none}
.kp-car-slide{opacity:0;transform:translate3d(-140px,0,0) scale(.94);filter:blur(8px);transition:transform 1.2s cubic-bezier(.22,1,.36,1) .05s,opacity .8s ease .05s,filter 1s cubic-bezier(.22,1,.36,1) .05s}
.kp-card.is-in .kp-car-slide{opacity:1;transform:none;filter:blur(0)}
.kp-float{animation:kpFloat 6s ease-in-out infinite;animation-delay:calc(var(--ci,0) * -1.3s)}
@keyframes kpFloat{0%,100%{transform:translate3d(0,0,0)}50%{transform:translate3d(0,-7px,0)}}
.kp-car-img{transform:translate3d(var(--px,0px),var(--py,0px),0) scale(1.12);filter:drop-shadow(0 22px 18px rgba(0,0,0,.65));transition:transform .9s cubic-bezier(.22,1,.36,1)}
.kp-card:hover .kp-car-img{transform:translate3d(var(--px,0px),var(--py,0px),0) scale(1.19)}.kp-split .kp-car-img{transition:transform .5s cubic-bezier(.22,1,.36,1)}
.kp-spec{opacity:0;transform:translate3d(16px,0,0);transition:opacity .6s ease calc(1s + var(--si) * .07s),transform .6s cubic-bezier(.22,1,.36,1) calc(1s + var(--si) * .07s)}
.kp-card.is-in .kp-spec{opacity:1;transform:none}
.kp-rv{opacity:0;transform:translate3d(48px,0,0);filter:blur(6px);transition:transform .9s cubic-bezier(.22,1,.36,1) calc(.7s + var(--ri) * .11s),opacity .7s ease calc(.7s + var(--ri) * .11s),filter .8s ease calc(.7s + var(--ri) * .11s)}
.kp-card.is-in .kp-rv{opacity:1;transform:none;filter:blur(0)}
.kp-card:not(.is-in) :is(.kp-rv,.kp-car-slide,.kp-spec){transition-duration:.25s;transition-delay:0s}
.kp-split .kp-floor{bottom:10%;left:16%;right:16%;height:30px}
.kp-split .kp-spot{width:100%;height:100%;top:0;background:radial-gradient(ellipse 48% 50% at 50% 50%,rgba(201,169,98,.32),rgba(201,169,98,.08) 50%,transparent 72%)}
@media (min-width:768px){.kp-split .kp-stage::after{height:30%}.kp-split .kp-stage{border-right:1px solid rgba(201,169,98,.08)}}
/* Carte split — voiture bien visible (v26) : pas de recadrage, plus lumineuse, projecteur + sol */
.kp-split .kp-car-img{transform:translate3d(var(--px,0px),var(--py,0px),0) scale(1);filter:brightness(1.1) contrast(1.06) drop-shadow(0 0 28px rgba(255,238,205,.10)) drop-shadow(0 16px 14px rgba(0,0,0,.5));image-rendering:auto;backface-visibility:hidden}
.kp-split.kp-card:hover .kp-car-img{transform:translate3d(var(--px,0px),var(--py,0px),0) scale(1.04)}
.kp-card.is-in .kp-car-slide{filter:none}
.kp-split .kp-spot{z-index:0;opacity:1;background:radial-gradient(ellipse 52% 46% at 50% 52%,rgba(255,240,212,.20),rgba(201,169,98,.12) 45%,transparent 74%)}
.kp-split .kp-floor{z-index:0;left:8%;right:8%;bottom:9%;height:56px;filter:blur(6px);background:radial-gradient(ellipse at center,rgba(255,236,200,.16),rgba(201,169,98,.06) 45%,transparent 72%)}
.kp-split .kp-floor::after{content:"";position:absolute;left:14%;right:14%;top:42%;height:16px;border-radius:50%;background:radial-gradient(ellipse at center,rgba(0,0,0,.75),transparent 70%)}
.kp-split .kp-stage::after{background:linear-gradient(180deg,transparent,rgba(201,169,98,.035) 60%,rgba(255,255,255,.02));border-top-color:rgba(255,255,255,.025)}
.kp-split .kp-stage .kp-car-slide{z-index:2}

.kp-price-big{text-shadow:0 2px 12px rgba(0,0,0,.45)}
.kp-save{box-shadow:0 0 0 1px rgba(245,236,214,.35)}
.kp-ping{animation:kpPing 1.8s cubic-bezier(0,0,.2,1) infinite}@keyframes kpPing{75%,100%{transform:scale(2.4);opacity:0}}
.kp-cta-pulse{box-shadow:0 8px 24px -6px rgba(201,169,98,.6)}
.kp-cta-pulse::after{content:"";position:absolute;inset:0;border-radius:inherit;box-shadow:0 0 0 0 rgba(201,169,98,.55);animation:kpPulse 2.4s cubic-bezier(.22,1,.36,1) infinite;pointer-events:none}
@keyframes kpPulse{0%{box-shadow:0 0 0 0 rgba(201,169,98,.5)}70%,100%{box-shadow:0 0 0 12px rgba(201,169,98,0)}}
.kp-progress{transition:transform .25s linear}
@media (hover:none){.kp-card{transform:none!important}}
/* Modeles.tsx — kp-grain / kp-clamp-3 (app/globals.css) + Reveal */
.kp-grain{background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.35'/%3E%3C/svg%3E");mix-blend-mode:overlay}
.kp-clamp-3{display:-webkit-box;-webkit-box-orient:vertical;overflow:hidden;-webkit-line-clamp:3;line-clamp:3}
.kpm-wait{opacity:0;transform:translate3d(0,40px,0) scale(.992);filter:blur(10px);transition:opacity .85s cubic-bezier(.22,1,.36,1) var(--kpm-d,0ms),transform .85s cubic-bezier(.22,1,.36,1) var(--kpm-d,0ms),filter .85s cubic-bezier(.22,1,.36,1) var(--kpm-d,0ms)}
.kpm-wait.kpm-in{opacity:1;transform:none;filter:none}
@media (prefers-reduced-motion:reduce){.kpm-wait{opacity:1!important;transform:none!important;filter:none!important;transition:none!important}#modeles .group *{transition:none!important}}
@media (prefers-reduced-motion:reduce){.kp-car-slide,.kp-spec,.kp-rv,.kp-card .kp-ribbon{filter:none!important;opacity:1!important;transform:none!important;transition:none!important}.kp-float,.kp-ping,.kp-cta-pulse::after{animation:none!important}.kp-card,.kp-car-img,.kp-card-sweep{transition:none!important}.kp-card{transform:none!important}}


/* Footer — Reveal */
.kp-reveal-wait{opacity:0;transform:translate3d(0,40px,0);transition:opacity .7s cubic-bezier(.22,1,.36,1) var(--rd,0ms),transform .7s cubic-bezier(.22,1,.36,1) var(--rd,0ms)}
.kp-reveal-wait.kp-reveal-in{opacity:1;transform:none}
</style>
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>tailwind.config={theme:{extend:{colors:{kp:{black:'#0a0a0a',dark:'#141414',gold:'#c9a962',sand:'#e8e4dc',silver:'#d4d4d8',bg:'#050505',surface:'#0c0c0c',elevated:'#141414',muted:'#9ca3af',accent:'#e8e4dc'}},fontFamily:{sans:['Montserrat','sans-serif'],serif:['"Playfair Display"','Georgia','serif']}}}}</script>
</head>
<body class="selection:bg-kp-gold selection:text-black bg-kp-black text-kp-sand font-sans antialiased">
@include('partials.header')
<main>@yield('content')</main>
@include('partials.footer')

<script>
// Cartes : apparition au scroll + inclinaison 3D (desktop)
function kpCard() {
  return { inView: false, parStyle: '', fine: matchMedia('(hover: hover) and (pointer: fine)').matches && !matchMedia('(prefers-reduced-motion: reduce)').matches,
    init() { if (!('IntersectionObserver' in window) || matchMedia('(prefers-reduced-motion: reduce)').matches) { this.inView = true; return }
      // rejoue l'animation quand la carte / la slide redevient visible
      new IntersectionObserver(([e]) => { if (e.intersectionRatio >= 0.55) this.inView = true; else if (e.intersectionRatio < 0.08) this.inView = false }, { threshold: [0, 0.08, 0.55, 0.8] }).observe(this.$el) },
    par(e) { if (!this.fine) return; const r = this.$el.getBoundingClientRect(); const x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
      this.parStyle = `--px:${(x * -26).toFixed(1)}px;--py:${(y * -14).toFixed(1)}px` },
    unpar() { this.parStyle = '' } }
}
// Prix : compteur animé (valeur finale déjà rendue côté serveur)
function kpCount(target) {
  const fmt = v => new Intl.NumberFormat('fr-FR').format(v).replace(/[\u202f\u00a0]/g, ' ') + ' {{ config('promo.currency') }}';
  return { txt: fmt(target), init() {
      if (!target || /HeadlessChrome|bot|crawl|spider/i.test(navigator.userAgent) || matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) return;
      const io = new IntersectionObserver(([e]) => { if (!e.isIntersecting) return; io.disconnect();
        const t0 = performance.now(), dur = 1400; const from = Math.round(target * 0.6);
        const step = now => { const k = Math.min(1, (now - t0) / dur), ease = 1 - Math.pow(1 - k, 4);
          this.txt = fmt(k < 1 ? Math.round((from + (target - from) * ease) / 1000) * 1000 : target); if (k < 1) requestAnimationFrame(step) };
        requestAnimationFrame(step) }, { threshold: 0.6 }); io.observe(this.$el) } }
}
</script>
</body></html>
