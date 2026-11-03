{{-- Formulaire envoyé en JSON (fetch) vers un point d'entrée qui répond { error } en 422 ou un 201 en cas de succès.
     Les fichiers sont transmis (FormData). Le formulaire est remplacé par le message de succès une fois envoyé. --}}
@props(['action', 'submit', 'sending', 'okTitle', 'okText', 'network', 'privacy' => null, 'files' => false])
<div class="kp-form" x-data="kpForm(@js($network))" data-kp-form>
  <div class="kp-form__done" x-show="done" x-cloak role="status" tabindex="-1" x-ref="done">
    <p class="heading-s">{{ $okTitle }}</p>
    <p class="body kp-muted">{{ $okText }}</p>
  </div>
  <form method="POST" action="{{ $action }}" @if($files) enctype="multipart/form-data" @endif x-show="! done" @submit.prevent="submit($el)" {{ $attributes->except(['class']) }} novalidate>
    @csrf
    <p class="kp-login__error" role="alert" x-show="error" x-text="error" x-cloak></p>
    {{ $slot }}
    @if($privacy)<p class="body-s kp-muted">{{ $privacy }}</p>@endif
    <button class="kp-btn kp-btn--primary" :disabled="sending"><span x-show="! sending">{{ $submit }}</span><span x-show="sending" x-cloak>{{ $sending }}</span></button>
  </form>
</div>
@once
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  window.Alpine.data('kpForm', (networkError) => ({
    sending: false,
    done: false,
    error: '',
    async submit(form) {
      this.error = '';
      this.sending = true;
      try {
        const response = await fetch(form.action, {
          method: 'POST',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          body: new FormData(form),
        });
        const data = await response.json().catch(() => ({}));
        if (response.ok) {
          this.done = true;
          form.reset();
          this.$nextTick(() => this.$refs.done.focus());
        } else {
          const firstError = data.errors ? Object.values(data.errors)[0] : null;
          this.error = data.error || (Array.isArray(firstError) ? firstError[0] : null) || (response.status === 429 ? 'Trop de demandes. Réessayez dans une minute.' : networkError);
        }
      } catch (e) {
        this.error = networkError;
      } finally {
        this.sending = false;
      }
    },
  }));
});
</script>
@endpush
@endonce
