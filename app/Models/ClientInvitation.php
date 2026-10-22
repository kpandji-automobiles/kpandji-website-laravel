<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
class ClientInvitation extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['expires_at' => 'datetime', 'accepted_at' => 'datetime', 'revoked_at' => 'datetime', 'last_sent_at' => 'datetime'];
    public const DAYS = 7;

    public function inviter() { return $this->belongsTo(User::class, 'invited_by'); }
    public function user() { return $this->belongsTo(User::class); }

    /** pending | accepted | expired | revoked */
    public function state(): string
    {
        if ($this->accepted_at) return 'accepted';
        if ($this->revoked_at) return 'revoked';
        return $this->expires_at->isPast() ? 'expired' : 'pending';
    }

    /** Génère un nouveau jeton (l'ancien lien devient invalide) et renvoie le lien signé, valable 7 jours. Seul le hash est stocké. */
    public function refreshLink(): string
    {
        $plain = Str::random(48);
        $this->forceFill(['token_hash' => hash('sha256', $plain), 'expires_at' => now()->addDays(self::DAYS), 'last_sent_at' => now()])->save();
        return URL::temporarySignedRoute('invitation.show', $this->expires_at, ['token' => $plain]);
    }

    public static function findByToken(string $plain): ?self
    {
        return static::where('token_hash', hash('sha256', $plain))->first();
    }
}
