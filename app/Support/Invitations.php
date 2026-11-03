<?php

namespace App\Support;

use App\Models\ClientInvitation;
use App\Models\User;
use InvalidArgumentException;

/** Invitations client / manager de l'espace manager : envoi, renvoi et révocation. */
final class Invitations
{
    public static function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    /** Un compte existe déjà avec cette adresse : on n'invite pas. */
    public static function emailTaken(string $email): bool
    {
        return User::where('email', self::normalizeEmail($email))->exists();
    }

    /**
     * Une seule invitation ouverte par e-mail : les précédentes non acceptées sont révoquées.
     *
     * @param  array{name: string, email: string, phone?: ?string, role: string}  $data
     * @return array{invitation: ClientInvitation, link: string}
     */
    public static function invite(array $data, User $inviter): array
    {
        $data['email'] = self::normalizeEmail($data['email']);

        if (self::emailTaken($data['email'])) {
            throw new InvalidArgumentException("Un compte existe déjà pour {$data['email']}.");
        }

        ClientInvitation::where('email', $data['email'])->whereNull('accepted_at')->whereNull('revoked_at')->update(['revoked_at' => now()]);

        $invitation = ClientInvitation::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'invited_by' => $inviter->id,
            'token_hash' => '',
            'expires_at' => now()->addDays(ClientInvitation::DAYS),
        ]);

        return self::deliver($invitation);
    }

    /** Renvoi et révocation restent possibles tant que l'invitation n'est pas acceptée. */
    public static function isOpen(ClientInvitation $invitation): bool
    {
        return ! $invitation->accepted_at;
    }

    /**
     * Nouveau lien (l'ancien devient invalide), invitation rouverte si elle était révoquée.
     *
     * @return array{invitation: ClientInvitation, link: string}
     */
    public static function resend(ClientInvitation $invitation): array
    {
        if (! self::isOpen($invitation)) {
            throw new InvalidArgumentException("L'invitation {$invitation->id} est déjà acceptée.");
        }

        $invitation->forceFill(['revoked_at' => null, 'send_count' => $invitation->send_count + 1])->save();

        return self::deliver($invitation);
    }

    /** Sans effet sur une invitation déjà acceptée. */
    public static function revoke(ClientInvitation $invitation): void
    {
        if (self::isOpen($invitation)) {
            $invitation->forceFill(['revoked_at' => now()])->save();
        }
    }

    /** Lien WhatsApp pré-rempli avec le message d'invitation, si un téléphone est connu. */
    public static function whatsappUrl(ClientInvitation $invitation, string $link): ?string
    {
        $phone = preg_replace('/\D/', '', (string) $invitation->phone);

        if ($phone === '') {
            return null;
        }

        return 'https://wa.me/'.$phone.'?text='.rawurlencode(self::mailBody($invitation, $link));
    }

    /**
     * @return array{invitation: ClientInvitation, link: string}
     */
    private static function deliver(ClientInvitation $invitation): array
    {
        $link = $invitation->refreshLink();
        $status = SpaceMail::send($invitation->email, sp_t('inv_mail_subject'), self::mailBody($invitation, $link));
        $invitation->forceFill(['mail_status' => $status])->save();

        return ['invitation' => $invitation, 'link' => $link];
    }

    private static function mailBody(ClientInvitation $invitation, string $link): string
    {
        return str_replace([':name', ':link'], [$invitation->name, $link], sp_t('inv_mail_body'));
    }
}
