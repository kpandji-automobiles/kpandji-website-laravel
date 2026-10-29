{{-- Éditeur riche minimal : $name, $value --}}
<div x-data="{
    html: @js($value ?? ''),
    src: false,

    // Nettoyage du HTML.
    clean(value) {
        if (!window.DOMPurify?.isSupported) {
            throw new Error('Nettoyeur HTML indisponible.');
        }

        return window.DOMPurify.sanitize(value ?? '', {
            ALLOWED_TAGS: [
                'p', 'br', 'strong', 'b', 'em', 'i', 'u',
                'a', 'ul', 'ol', 'li', 'h2', 'h3', 'h4',
                'blockquote', 'img', 'hr', 'span'
            ],
            ALLOWED_ATTR: [
                'href', 'title', 'src', 'alt', 'width', 'height'
            ],
            ALLOW_DATA_ATTR: false,
            ALLOW_ARIA_ATTR: false
        });
    },

    // Affichage du contenu nettoyé.
    render() {
        this.html = this.clean(this.html);
        this.$refs.ed.innerHTML = this.html;
    },

    // Application de la mise en forme.
    cmd(command, value = null) {
        document.execCommand(command, false, value);
        this.html = this.$refs.ed.innerHTML;
        this.render();
    },

    // Collage en texte brut.
    paste(event) {
        const text = event.clipboardData?.getData('text/plain') ?? '';
        this.$refs.ed.focus();
        document.execCommand('insertText', false, text);
        this.html = this.$refs.ed.innerHTML;
    }
}" x-init="$nextTick(() => render())"
    class="mt-2 overflow-hidden rounded-lg border border-white/15 bg-black">
    <div class="flex flex-wrap gap-1 border-b border-white/10 bg-white/[0.03] p-2 text-xs">
        <button type="button" @click="cmd('bold')" class="rounded px-2 py-1 font-bold hover:bg-white/10">G</button>
        <button type="button" @click="cmd('italic')" class="rounded px-2 py-1 italic hover:bg-white/10">I</button>
        <button type="button" @click="cmd('formatBlock','h2')"
            class="rounded px-2 py-1 hover:bg-white/10">Titre</button>
        <button type="button" @click="cmd('formatBlock','h3')"
            class="rounded px-2 py-1 hover:bg-white/10">Sous-titre</button>
        <button type="button" @click="cmd('formatBlock','p')"
            class="rounded px-2 py-1 hover:bg-white/10">Paragraphe</button>
        <button type="button" @click="cmd('insertUnorderedList')" class="rounded px-2 py-1 hover:bg-white/10">•
            Liste</button>
        <button type="button" @click="const u = prompt('Lien (https://… ou /page)'); if(u) cmd('createLink', u)"
            class="rounded px-2 py-1 hover:bg-white/10">Lien</button>
        <button type="button" @click="cmd('removeFormat')" class="rounded px-2 py-1 hover:bg-whites/10">Effacer
            format</button>
        <button type="button" @click="src = !src; if(!src) $nextTick(() => render())"
            class="ml-auto rounded px-2 py-1 text-kp-gold hover:bg-white/10" x-text="src ? 'Éditeur' : 'HTML'"></button>
    </div>
    <div x-show="!src" x-ref="ed" contenteditable="true"
        @input="html = $el.innerHTML" @paste.prevent="paste($event)" @drop.prevent
        class="kp-prose min-h-[180px] px-4 py-3 text-sm leading-relaxed text-white outline-none"></div>
    <textarea x-show="src" x-model="html" rows="10"
        class="w-full bg-black px-4 py-3 font-mono text-xs text-white outline-none"></textarea>
    <input type="hidden" name="{{ $name }}" :value="html">
</div>
