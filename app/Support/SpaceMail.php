<?php
namespace App\Support;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
/** Envoi via le mailer courant de l'application. Avec MAIL_MAILER=log/array, le message est seulement journalisé. */
class SpaceMail
{
    public static function isFake(): bool { return in_array(config('mail.default'), ['log', 'array'], true); }

    /** Renvoie 'logged' | 'sent' | 'failed' */
    public static function send(string $to, string $subject, string $body): string
    {
        try {
            Mail::raw($body, fn ($m) => $m->to($to)->subject($subject));
            return self::isFake() ? 'logged' : 'sent';
        } catch (\Throwable $e) {
            Log::warning('SpaceMail: '.$e->getMessage());
            return 'failed';
        }
    }
}
