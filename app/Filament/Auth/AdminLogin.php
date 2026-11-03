<?php

namespace App\Filament\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Connexion au backoffice (/backoffice/login), aux couleurs KPANDJI.
 * Habillage : public/css/kpandji/backoffice-login.css (render hook du panneau admin).
 */
class AdminLogin extends Login
{
    public function getTitle(): string|Htmlable
    {
        return 'Connexion — Espace administration';
    }

    /** Remplacé par l'en-tête KPANDJI (filament.admin.login-brand). */
    public function hasLogo(): bool
    {
        return false;
    }

    public function getHeading(): string|Htmlable|null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getHeading();
        }

        return 'Espace administration';
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getSubheading();
        }

        return 'Accès réservé à l’équipe KPANDJI.';
    }

    protected function getEmailFormComponent(): Component
    {
        /** @var TextInput $email */
        $email = parent::getEmailFormComponent();

        return $email->label('Adresse e-mail professionnelle')->placeholder('prenom.nom@kpandji.com');
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()->label('Se connecter');
    }
}
