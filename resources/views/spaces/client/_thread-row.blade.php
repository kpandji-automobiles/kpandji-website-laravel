{{-- Ligne d'un fil de l'espace client (message, avis, demande). Paramètre : $t (élément de ClientThreads). --}}
<a class="kp-client__thread{{ $t['unread'] ? ' is-unread' : '' }}" href="{{ route('client.thread', $t['key']) }}" data-sp-thread-row="{{ $t['key'] }}">
  <span class="kp-client__thread-main">
    <span class="kp-client__thread-subject">{{ $t['subject'] }}@if($t['unread']) <span class="kp-status kp-status--new" data-sp-unread>{{ $t['unread'] }} {{ sp_t('msg_new') }}</span>@endif</span>
    <span class="body-s kp-muted">{{ \Illuminate\Support\Str::limit($t['preview'], 140) }}</span>
  </span>
  <span class="body-s kp-muted">{{ \Illuminate\Support\Carbon::parse($t['date'])->format('d/m/Y') }}</span>
</a>
