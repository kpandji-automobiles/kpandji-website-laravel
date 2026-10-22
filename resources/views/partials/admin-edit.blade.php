{{-- Bouton « Modifier cette section » — visible uniquement par un administrateur connecté (rendu via cms_edit()). --}}
<a href="{{ $href }}" class="kp-cms-edit" style="top:{{ $top }}" data-kp-admin-edit target="_blank" rel="noopener" title="Ouvrir l'administration (nouvel onglet)">
  <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
  <span>{{ $label }}</span>
</a>
