<a href="{{ route('client.thread', $t['key']) }}" data-sp-thread-row="{{ $t['key'] }}" class="flex items-start gap-4 border-t border-white/[0.06] px-6 py-4 transition hover:bg-white/[0.03]">
  <span class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-full border {{ $t['unread'] ? 'border-kp-gold bg-kp-gold/15 text-kp-gold shadow-[0_0_14px_rgba(201,169,98,.45)]' : 'border-white/10 text-white/40' }}">
    @if($t['source']==='notice')<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3l7 3v6c0 4.2-3 7.5-7 9-4-1.5-7-4.8-7-9V6l7-3z"/></svg>
    @else<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16v10H8l-4 4V6z"/></svg>@endif
  </span>
  <div class="min-w-0 flex-1">
    <div class="flex items-center gap-2"><p class="truncate text-sm {{ $t['unread'] ? 'font-semibold text-white' : 'text-white/80' }}">{{ $t['subject'] }}</p>
      @if($t['unread'])<span data-sp-unread class="sp-badge sp-b-nouveau shrink-0">{{ $t['unread'] }} {{ sp_t('msg_new') }}</span>@endif</div>
    <p class="mt-1 line-clamp-1 text-xs text-white/45">{{ \Illuminate\Support\Str::limit($t['preview'], 140) }}</p>
  </div>
  <span class="shrink-0 text-xs text-white/35">{{ \Illuminate\Support\Carbon::parse($t['date'])->format('d/m/Y') }}</span>
</a>
