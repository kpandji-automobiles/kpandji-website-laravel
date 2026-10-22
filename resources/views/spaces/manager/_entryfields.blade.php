@php $en = $en ?? null; @endphp
<div class="grid gap-3 sm:grid-cols-[1fr_1fr_1fr_2fr]">
  <div><label class="sp-label">{{ sp_t('date') }}</label><input name="entry_date" type="date" required value="{{ optional($en?->entry_date)->format('Y-m-d') ?? now()->format('Y-m-d') }}" class="sp-input"></div>
  <div><label class="sp-label">{{ sp_t('type') }}</label><select name="type" class="sp-input">@foreach(\App\Models\RevenueEntry::TYPES as $t)<option value="{{ $t }}" @selected(($en->type ?? 'revenu')===$t)>{{ sp_t('type_'.$t) }}</option>@endforeach</select></div>
  <div><label class="sp-label">{{ sp_t('amount') }}</label><input name="amount" required inputmode="numeric" value="{{ $en->amount ?? '' }}" class="sp-input"></div>
  <div><label class="sp-label">{{ sp_t('description') }}</label><input name="description" maxlength="255" value="{{ $en->description ?? '' }}" class="sp-input"></div>
</div>
