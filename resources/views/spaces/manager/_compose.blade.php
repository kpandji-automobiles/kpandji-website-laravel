{{-- Fenêtre « Message » : écrire à un ou plusieurs utilisateurs validés (une conversation par destinataire) --}}
@php
  $dmUsers = $recipients->map(fn($u) => ['id'=>$u->id,'name'=>$u->name,'email'=>$u->email,'role'=>$u->role])->values();
  $dmL = ['sel' => sp_t('dm_selected'), 'allRole' => sp_t('dm_all_role'), 'rc' => sp_t('role_client'), 'rm' => sp_t('role_manager'), 'err' => sp_t('dm_e_server'), 'eRec' => sp_t('dm_e_recipients')];
@endphp
<div x-data="spDm(@js($dmUsers), @js($dmL))" @sp-dm-open.window="show()" @keydown.escape.window="open && hide()">
  <template x-teleport="body">
    <div x-show="open" x-cloak class="fixed inset-0 z-[90] flex items-end justify-center sm:items-center sm:p-6" role="dialog" aria-modal="true" aria-labelledby="sp-dm-title" data-sp-dm>
      <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="hide()" data-sp-dm-overlay></div>
      <form x-show="open" x-transition @submit.prevent="send()" x-ref="panel" tabindex="-1"
        class="relative flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-t-[28px] border border-kp-gold/30 bg-[#0a0a0a] shadow-[0_40px_120px_-30px_rgba(201,169,98,.35)] outline-none sm:rounded-[28px]">
        <span class="pointer-events-none absolute inset-x-10 top-0 h-px bg-gradient-to-r from-transparent via-kp-gold to-transparent"></span>
        <div class="flex items-center justify-between gap-3 border-b border-white/10 px-6 py-4">
          <h2 id="sp-dm-title" class="font-serif text-2xl text-white">{{ sp_t('dm_title') }}</h2>
          <button type="button" @click="hide()" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 text-white/60 hover:border-kp-gold hover:text-kp-gold" aria-label="{{ sp_t('dm_close') }}">✕</button>
        </div>
        <div class="flex-1 space-y-5 overflow-y-auto px-6 py-5">
          <div>
            <div class="flex flex-wrap items-center justify-between gap-2"><span class="sp-label !mb-0">{{ sp_t('dm_recipients') }}</span>
              <span class="text-xs text-kp-gold" data-sp-dm-count x-text="L.sel.replace(':n', sel.length)"></span></div>
            <div class="mt-2 flex flex-wrap gap-1.5" x-show="sel.length" data-sp-dm-chips>
              <template x-for="u in chips()" :key="u.id"><span class="inline-flex max-w-full items-center gap-1 rounded-full border border-kp-gold/35 bg-kp-gold/10 py-1 pl-3 pr-1 text-xs text-kp-gold"><span class="truncate" x-text="u.name"></span><button type="button" @click="toggle(u.id)" class="flex h-5 w-5 items-center justify-center rounded-full hover:bg-kp-gold/20" aria-label="×">×</button></span></template>
              <span x-show="sel.length > 8" class="self-center text-xs text-white/40" x-text="'+' + (sel.length - 8)"></span>
              <button type="button" @click="sel=[]" class="ml-1 text-[10px] uppercase tracking-[0.18em] text-white/40 hover:text-white">{{ sp_t('dm_clear') }}</button>
            </div>
            <div class="mt-3 grid gap-2 sm:grid-cols-[1fr_auto]">
              <input x-model="q" type="search" placeholder="{{ sp_t('dm_search') }}" class="sp-input" data-sp-dm-search>
              <select x-model="role" class="sp-input sm:w-44" data-sp-dm-role><option value="">{{ sp_t('dm_role_all') }}</option><option value="client">{{ sp_t('role_client') }}</option><option value="manager">{{ sp_t('role_manager') }}</option></select>
            </div>
            <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm">
              <label class="flex cursor-pointer items-center gap-2 text-white/80"><input type="checkbox" data-sp-dm-all :checked="allOn(users)" @change="setAll(users, $event.target.checked)" class="h-4 w-4 accent-[#c9a962]"><span class="font-semibold">{{ sp_t('dm_all_users') }}</span></label>
              <template x-for="r in ['client','manager']" :key="r"><label class="flex cursor-pointer items-center gap-2 text-white/60"><input type="checkbox" :data-sp-dm-allrole="r" :checked="allOn(byRole(r))" :disabled="!byRole(r).length" @change="setAll(byRole(r), $event.target.checked)" class="h-4 w-4 accent-[#c9a962]"><span x-text="L.allRole.replace(':role', r==='client' ? L.rc : L.rm)"></span></label></template>
            </div>
            <ul class="mt-3 max-h-56 divide-y divide-white/[0.06] overflow-y-auto rounded-2xl border border-white/10 bg-black/40" data-sp-dm-list>
              <template x-for="u in filtered()" :key="u.id">
                <li><label class="flex cursor-pointer items-center gap-3 px-4 py-3 transition hover:bg-white/[0.04]" :class="sel.includes(u.id) && 'bg-kp-gold/[0.06]'">
                  <input type="checkbox" :value="u.id" :checked="sel.includes(u.id)" @change="toggle(u.id)" class="h-4 w-4 shrink-0 accent-[#c9a962]" :data-sp-dm-user="u.email">
                  <span class="min-w-0 flex-1"><span class="block truncate text-sm text-white" x-text="u.name"></span><span class="block truncate text-xs text-white/40" x-text="u.email"></span></span>
                  <span class="sp-badge shrink-0" :class="u.role==='manager' ? 'sp-b-en_cours' : 'sp-b-pending'" x-text="u.role==='manager' ? L.rm : L.rc"></span>
                </label></li>
              </template>
              <li x-show="!filtered().length" class="px-4 py-4 text-sm text-white/40">{{ $recipients->count() ? sp_t('dm_no_match') : sp_t('dm_no_users') }}</li>
            </ul>
            <p class="mt-2 text-[11px] text-white/35">{{ sp_t('dm_private_note') }}</p>
          </div>
          <div><label class="sp-label" for="dm-s">{{ sp_t('req_subject') }}</label><input id="dm-s" x-model="subject" maxlength="160" class="sp-input" data-sp-dm-subject></div>
          <div><label class="sp-label" for="dm-b">{{ sp_t('req_message') }}</label><textarea id="dm-b" x-model="body" rows="5" maxlength="5000" class="sp-input" data-sp-dm-body></textarea></div>
          <div x-show="errors.length" class="rounded-xl border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-200" data-sp-dm-errors role="alert"><template x-for="e in errors"><p x-text="e"></p></template></div>
        </div>
        <div class="flex flex-col-reverse gap-2 border-t border-white/10 px-6 py-4 sm:flex-row sm:justify-end">
          <button type="button" @click="hide()" class="sp-ghost !min-h-12">{{ sp_t('cancel') }}</button>
          <button type="submit" :disabled="sending" data-sp-dm-send class="sp-btn !min-h-12 disabled:opacity-60">
            <svg x-show="sending" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 3a9 9 0 1 0 9 9"/></svg>
            <span x-text="sending ? @js(sp_t('dm_sending')) : @js(sp_t('dm_send'))"></span></button>
        </div>
      </form>
    </div>
  </template>
  <template x-teleport="body">
    <div x-show="toast" x-cloak x-transition data-sp-toast role="status" class="fixed bottom-6 left-1/2 z-[95] w-[calc(100%-2rem)] max-w-md -translate-x-1/2 rounded-2xl border border-kp-gold/40 bg-[#0b0b0b] px-5 py-4 text-center text-sm text-kp-gold shadow-[0_20px_60px_-20px_rgba(201,169,98,.6)]" x-text="toast"></div>
  </template>
</div>
<script>
function spDm(users, L){return{users,L,open:false,q:'',role:'',sel:[],subject:'',body:'',errors:[],sending:false,toast:'',_ov:'',
  show(){this.errors=[];this.open=true;this._ov=document.body.style.overflow;document.body.style.overflow='hidden';this.$nextTick(()=>this.$refs.panel&&this.$refs.panel.focus())},
  hide(){if(this.sending)return;this.open=false;document.body.style.overflow=this._ov},
  byRole(r){return this.users.filter(u=>u.role===r)},
  filtered(){const q=this.q.trim().toLowerCase();return this.users.filter(u=>(!this.role||u.role===this.role)&&(!q||(u.name+' '+u.email).toLowerCase().includes(q)))},
  toggle(id){this.sel=this.sel.includes(id)?this.sel.filter(x=>x!==id):[...this.sel,id]},
  allOn(list){return list.length>0&&list.every(u=>this.sel.includes(u.id))},
  setAll(list,on){const ids=list.map(u=>u.id);this.sel=on?[...new Set([...this.sel,...ids])]:this.sel.filter(x=>!ids.includes(x))},
  chips(){return this.sel.slice(0,8).map(id=>this.users.find(u=>u.id===id)).filter(Boolean)},
  async send(){if(this.sending)return;this.errors=[];
    if(!this.sel.length){this.errors.push(L.eRec);return}
    this.sending=true;
    try{const r=await fetch(@js(route('manager.compose')),{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':@js(csrf_token())},body:JSON.stringify({recipients:this.sel,subject:this.subject,body:this.body})});
      const d=await r.json().catch(()=>({}));
      if(r.ok&&d.ok){this.sending=false;this.hide();this.toast=d.message;this.sel=[];this.subject='';this.body='';setTimeout(()=>{location.href=d.redirect},1400)}
      else{this.errors=d.errors?[...new Set(Object.values(d.errors).flat())]:[d.message||L.err];this.sending=false}
    }catch(e){this.errors=[L.err];this.sending=false}}
}}
</script>
