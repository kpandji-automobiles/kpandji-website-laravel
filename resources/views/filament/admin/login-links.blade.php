{{-- Sous le formulaire de /backoffice/login (render hook AUTH_LOGIN_FORM_AFTER du panneau admin). --}}
<nav class="kp-bo-login__links" aria-label="Autres espaces">
    <a href="{{ route('home') }}">← Retour au site</a>
    <span>
        <a href="{{ filament()->getPanel('manager')->getLoginUrl() }}">Espace manager</a>
        <a href="{{ route('connexion') }}">Espace client</a>
    </span>
</nav>
