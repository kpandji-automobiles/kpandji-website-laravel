<?php

namespace App\Support;

use App\Models\ClientNotice;
use App\Models\User;
use InvalidArgumentException;

/** Validation des comptes client / manager créés sur invitation (espace manager). */
final class AccountValidation
{
    /** Un compte se valide ou se refuse tant qu'il n'est pas actif ; jamais un admin. */
    public static function canDecide(User $user): bool
    {
        return in_array($user->role, ['client', 'manager'], true) && ! $user->isAdmin() && $user->account_status !== 'active';
    }

    public static function approve(User $user): void
    {
        self::guard($user);
        $user->forceFill(['account_status' => 'active'])->save();

        $subject = sp_t('val_mail_subject');
        $body = str_replace([':name', ':link'], [$user->name, route('home')], sp_t('val_mail_body'));
        if ($user->role === 'client') {
            ClientNotice::create(['user_id' => $user->id, 'subject' => $subject, 'body' => $body]);
        }
        SpaceMail::send($user->email, $subject, $body);
    }

    public static function refuse(User $user): void
    {
        self::guard($user);
        $user->forceFill(['account_status' => 'refused'])->save();
    }

    private static function guard(User $user): void
    {
        if (! self::canDecide($user)) {
            throw new InvalidArgumentException("Le compte {$user->id} ne peut pas être validé ou refusé.");
        }
    }
}
