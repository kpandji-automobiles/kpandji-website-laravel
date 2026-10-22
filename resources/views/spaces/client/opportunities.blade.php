@extends('spaces.layout')
@section('h1', sp_t('opp_title'))
@section('space')
<div class="sp-rise flex flex-wrap items-center justify-between gap-4"><p class="text-sm text-white/45">{{ sp_t('opp_intro') }}</p>
  <a href="{{ cms_url(sp_t('opp_url')) }}" data-sp-opp-new class="sp-btn">+ {{ sp_t('opp_new') }}</a></div>
<div class="mt-6 grid gap-4 md:grid-cols-2">
  @forelse($items as $n => $i)
    <article data-sp-opp="{{ $i['source'] }}-{{ $i['id'] }}" class="sp-card sp-rise relative overflow-hidden p-6" style="animation-delay:{{ min($n,8)*.05 }}s">
      <span aria-hidden="true" class="absolute -right-12 -top-12 h-32 w-32 rounded-full bg-kp-gold/10 blur-2xl"></span>
      <div class="flex items-center justify-between gap-3"><p class="font-serif text-2xl"><span class="text-white/60">Kpandji</span> <span class="sp-gold">{{ $i['source']==='sira' ? 'Sira' : 'Privilège' }}</span></p>
        <span class="sp-badge sp-b-{{ $i['status'] }}">{{ sp_t('st_'.$i['status']) }}</span></div>
      <dl class="mt-5 grid grid-cols-2 gap-4 text-sm">
        <div><dt class="sp-label">{{ sp_t('date') }}</dt><dd class="text-white">{{ \Illuminate\Support\Carbon::parse($i['date'])->format('d/m/Y H:i') }}</dd></div>
        <div><dt class="sp-label">{{ sp_t('opp_city') }}</dt><dd class="text-white">{{ $i['subject'] }}</dd></div>
        <div><dt class="sp-label">{{ sp_t('phone') }}</dt><dd class="text-white/80">{{ $i['phone'] }}</dd></div>
        <div class="min-w-0"><dt class="sp-label">{{ sp_t('email') }}</dt><dd class="truncate text-white/80">{{ $i['email'] }}</dd></div>
        @if($i['source']==='privilege')<div><dt class="sp-label">{{ sp_t('opp_channel') }}</dt><dd class="text-white/80">{{ sp_t('opp_ch_'.(($i['raw']['source'] ?? 'contact')==='advisor' ? 'advisor' : 'contact')) }}</dd></div>@endif
      </dl>
      @if($i['replies'])<a href="{{ route('client.thread', $i['source'].'-'.$i['id']) }}" class="sp-ghost mt-5">✉ {{ sp_t('replied') }} →</a>@endif
    </article>
  @empty <div class="sp-card p-8 text-center text-sm text-white/50 md:col-span-2">{{ sp_t('opp_none') }}</div> @endforelse
</div>
@endsection
