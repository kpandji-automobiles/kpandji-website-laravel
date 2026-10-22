{{-- Ligne de message (onglets Messages / Réservations / SAV / Autres) --}}
<a href="{{ route('manager.message', [$i['source'], $i['id']]) }}" data-sp-msg="{{ $i['source'] }}-{{ $i['id'] }}" class="group flex flex-col gap-2 border-t border-white/[0.06] px-5 py-4 transition hover:bg-white/[0.03] sm:flex-row sm:items-center sm:gap-5">
  <div class="flex items-center gap-2 sm:w-40 sm:shrink-0"><span class="h-2 w-2 shrink-0 rounded-full {{ $i['status'] === 'nouveau' ? 'bg-kp-gold shadow-[0_0_10px_#c9a962]' : 'bg-white/15' }}"></span>
    <span class="truncate text-[10px] font-semibold uppercase tracking-[0.18em] text-kp-gold/80">{{ $i['source']==='essai' ? sp_t('kind_'.($i['kind'] ?? 'essai')) : sp_t('src_'.$i['source']) }}</span></div>
  <div class="min-w-0 flex-1"><p class="truncate text-sm {{ $i['status'] === 'nouveau' ? 'font-semibold text-white' : 'text-white/80' }}">{{ $i['name'] }} <span class="font-normal text-white/40">· {{ $i['subject'] }}</span></p>
    @if($i['source']==='essai')
      <p class="mt-0.5 truncate text-xs text-white/50">{{ sp_t('model') }} : <span class="text-white/75">{{ $i['model'] ?: '—' }}</span> · {{ sp_t('wish_date') }} : <span class="text-white/75">{{ $i['wish_date'] ? \Illuminate\Support\Carbon::parse($i['wish_date'])->format('d/m/Y') : '—' }}</span> · {{ $i['phone'] }}</p>
    @elseif($i['body'])<p class="mt-0.5 truncate text-xs text-white/40">{{ \Illuminate\Support\Str::limit($i['body'], 110) }}</p>@endif</div>
  <div class="flex shrink-0 flex-wrap items-center gap-2">
    @if($i['replies'])<span class="sp-badge sp-b-replied">✓ {{ sp_t('replied') }}</span>@endif
    <span class="sp-badge sp-b-{{ $i['status'] }}">{{ sp_t('st_'.$i['status']) }}</span>
    <span class="w-28 text-right text-xs text-white/35">{{ \Illuminate\Support\Carbon::parse($i['date'])->format('d/m/Y H:i') }}</span>
  </div>
</a>
