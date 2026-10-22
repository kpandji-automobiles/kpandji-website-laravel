@php $iv = $iv ?? null; @endphp
<div class="grid gap-3 sm:grid-cols-2">
  <div class="sm:col-span-2"><label class="sp-label">{{ sp_t('inv_label') }}</label><input name="label" required maxlength="160" value="{{ $iv->label ?? '' }}" class="sp-input"></div>
  <div><label class="sp-label">{{ sp_t('program') }}</label><select name="program" class="sp-input"><option value="">—</option>@foreach(\App\Models\Investment::PROGRAMS as $p)<option value="{{ $p }}" @selected(($iv->program ?? '')===$p)>{{ sp_t('prog_'.$p) }}</option>@endforeach</select></div>
  <div><label class="sp-label">{{ sp_t('invested') }} (FCFA)</label><input name="amount" required inputmode="numeric" value="{{ $iv->amount ?? '' }}" class="sp-input"></div>
  <div><label class="sp-label">{{ sp_t('start_date') }}</label><input name="start_date" type="date" value="{{ optional($iv?->start_date)->format('Y-m-d') }}" class="sp-input"></div>
  <div><label class="sp-label">{{ sp_t('status') }}</label><select name="status" class="sp-input">@foreach(\App\Models\Investment::STATUSES as $s)<option value="{{ $s }}" @selected(($iv->status ?? 'actif')===$s)>{{ sp_t('ist_'.$s) }}</option>@endforeach</select></div>
  <div class="sm:col-span-2"><label class="sp-label">{{ sp_t('notes') }}</label><textarea name="notes" rows="2" class="sp-input">{{ $iv->notes ?? '' }}</textarea></div>
</div>
