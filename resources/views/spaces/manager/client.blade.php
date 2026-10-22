@extends('spaces.layout')
@section('h1', $client->name)
@section('space')
<a href="{{ route('manager.clients') }}" class="text-xs uppercase tracking-[0.2em] text-white/45 hover:text-kp-gold">← {{ sp_t('back_clients') }}</a>
<p class="mt-3 text-sm text-white/50">{{ $client->email }}{{ $client->phone ? ' · '.$client->phone : '' }} · {{ sp_t('client_since') }} {{ $client->created_at->format('d/m/Y') }}</p>
<div class="mt-6 space-y-6">
  @foreach($investments as $iv)
    @php $rev=$iv->entries->where('type','revenu')->sum('amount'); $fee=$iv->entries->where('type','frais')->sum('amount'); @endphp
    <section data-sp-investment="{{ $iv->id }}" x-data="{edit:false}" class="sp-card sp-rise p-6 sm:p-8">
      <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0"><div class="flex flex-wrap items-center gap-2">@if($iv->program)<span class="sp-badge sp-b-pending">{{ sp_t('prog_'.$iv->program) }}</span>@endif<span class="sp-badge sp-b-{{ $iv->status }}">{{ sp_t('ist_'.$iv->status) }}</span></div>
          <h2 class="mt-3 font-serif text-2xl text-white">{{ $iv->label }}</h2>
          <p class="mt-1 text-xs text-white/40">{{ $iv->start_date ? sp_t('start_date').' : '.$iv->start_date->format('d/m/Y') : '' }}</p></div>
        <div class="flex gap-6 text-right"><div><p class="sp-label">{{ sp_t('invested') }}</p><p class="sp-gold font-serif text-xl">{{ fcfa($iv->amount) }}</p></div><div><p class="sp-label">{{ sp_t('kpi_net') }}</p><p class="font-serif text-xl text-white">{{ fcfa($rev-$fee) }}</p></div></div>
      </div>
      @if($iv->notes)<p class="mt-4 whitespace-pre-line text-sm text-white/55">{{ $iv->notes }}</p>@endif
      <div class="mt-4 flex flex-wrap gap-2"><button type="button" @click="edit=!edit" class="sp-ghost">{{ sp_t('edit') }}</button>
        <form method="POST" action="{{ route('manager.investments.destroy', $iv) }}" onsubmit="return confirm(@js(sp_t('confirm_delete')))">@csrf @method('DELETE')<button class="sp-ghost hover:!border-red-400 hover:!text-red-300">{{ sp_t('delete') }}</button></form></div>
      <form x-show="edit" x-cloak method="POST" action="{{ route('manager.investments.update', $iv) }}" class="mt-5 space-y-4 rounded-2xl border border-white/10 bg-black/30 p-5">@csrf @method('PUT')
        @include('spaces.manager._invfields', ['iv' => $iv])<div class="flex gap-2"><button class="sp-btn">{{ sp_t('save') }}</button><button type="button" @click="edit=false" class="sp-ghost">{{ sp_t('cancel') }}</button></div></form>
      <div class="mt-6 overflow-x-auto rounded-2xl border border-white/[0.07]">
        <table class="sp-table w-full min-w-[620px]"><thead><tr><th>{{ sp_t('date') }}</th><th>{{ sp_t('type') }}</th><th>{{ sp_t('description') }}</th><th class="!text-right">FCFA</th><th></th></tr></thead><tbody>
          @forelse($iv->entries as $en)
            <tr x-data="{e:false}" data-sp-entry>
              <td class="whitespace-nowrap text-white/70">{{ $en->entry_date->format('d/m/Y') }}</td><td><span class="text-xs uppercase tracking-[0.14em] {{ $en->type==='frais' ? 'text-red-300' : ($en->type==='versement' ? 'text-sky-300' : 'text-kp-gold') }}">{{ sp_t('type_'.$en->type) }}</span></td>
              <td class="text-white/65">{{ $en->description }}
                <form x-show="e" x-cloak method="POST" action="{{ route('manager.entries.update', $en) }}" class="mt-3 space-y-3">@csrf @method('PUT')@include('spaces.manager._entryfields', ['en' => $en])<button class="sp-btn">{{ sp_t('save') }}</button></form></td>
              <td class="whitespace-nowrap text-right font-semibold {{ $en->type==='frais' ? 'text-red-300' : 'text-white' }}">{{ $en->type==='frais' ? '−' : '' }}{{ number_format($en->amount,0,',',' ') }}</td>
              <td class="whitespace-nowrap text-right"><button type="button" @click="e=!e" class="text-xs text-white/45 hover:text-kp-gold">{{ sp_t('edit') }}</button>
                <form method="POST" action="{{ route('manager.entries.destroy', $en) }}" class="inline" onsubmit="return confirm(@js(sp_t('confirm_delete')))">@csrf @method('DELETE')<button class="ml-3 text-xs text-white/45 hover:text-red-300">✕</button></form></td>
            </tr>
          @empty <tr><td colspan="5" class="text-white/40">{{ sp_t('none') }}</td></tr> @endforelse
        </tbody></table>
      </div>
      <form method="POST" action="{{ route('manager.entries.store', $iv) }}" data-sp-entry-form class="mt-5 space-y-3 rounded-2xl border border-dashed border-kp-gold/25 p-5">@csrf
        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-kp-gold">+ {{ sp_t('entry_add') }}</p>@include('spaces.manager._entryfields', ['en' => null])<button class="sp-btn">{{ sp_t('save') }}</button></form>
    </section>
  @endforeach
  <form method="POST" action="{{ route('manager.investments.store', $client) }}" data-sp-investment-form class="sp-card sp-rise space-y-4 border-dashed p-6 sm:p-8">@csrf
    <h2 class="font-serif text-2xl text-white">+ {{ sp_t('inv_add') }}</h2>@include('spaces.manager._invfields', ['iv' => null])<button class="sp-btn">{{ sp_t('save') }}</button></form>
</div>
@endsection
