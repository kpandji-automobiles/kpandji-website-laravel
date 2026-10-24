<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Conversation extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['read_at' => 'datetime'];
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
    public function replies()
    {
        return $this->hasMany(InboxReply::class, 'source_id')->where('source', 'dm')->oldest('id');
    }

    /** Dernière réponse de la conversation. */
    public function latestReply()
    {
        return $this->hasOne(InboxReply::class, 'source_id')
            ->where('source', 'dm')
            ->ofMany(['id' => 'max'], function ($query) {
                $query->where('source', 'dm');
            });
    }

    public function involves(User $u): bool
    {
        return $u->id === $this->sender_id || $u->id === $this->recipient_id;
    }

    /** Non lus pour $u : le message lui-même (s'il est destinataire) + réponses de l'autre partie. */
    public function unreadFor(User $u): int
    {
        $n = ($this->recipient_id === $u->id && !$this->read_at) ? 1 : 0;
        return $n + InboxReply::where(['source' => 'dm', 'source_id' => $this->id])->where('user_id', '!=', $u->id)->whereNull('read_at')->count();
    }

    public function markReadFor(User $u): void
    {
        if ($this->recipient_id === $u->id && !$this->read_at)
            $this->forceFill(['read_at' => now()])->save();
        InboxReply::where(['source' => 'dm', 'source_id' => $this->id])->where('user_id', '!=', $u->id)->whereNull('read_at')->update(['read_at' => now()]);
    }

    /** Total des non lus (messages internes) d'un utilisateur. */
    /** Compte les non-lus sans charger les conversations. */
    public static function unreadTotal(User $u): int
    {
        $userId = (int) $u->id;

        if ($userId <= 0) {
            return 0;
        }

        $initialMessages = static::query()
            ->where('recipient_id', $userId)
            ->whereNull('read_at')
            ->count();

        $conversations = static::query()
            ->select('id')
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('recipient_id', $userId);
            });

        $replies = InboxReply::query()
            ->where('source', 'dm')
            ->whereIn('source_id', $conversations)
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();

        return (int) ($initialMessages + $replies);
    }
}
