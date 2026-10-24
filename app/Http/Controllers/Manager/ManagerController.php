<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use App\Models\ClientInvitation;
use App\Models\InboxReply;
use App\Models\Investment;
use App\Models\RevenueEntry;
use App\Models\User;
use App\Support\Inbox;
use App\Support\SpaceMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
/** Espace manager (/espace-manager) — accessible au rôle manager et à l'admin. */
class ManagerController extends Controller
{
    public function dashboard()
    {
        $all = Inbox::all();
        return view('spaces.manager.dashboard', [
            'open' => $all->where('status', '!=', 'traite')->count(),
            'pending' => ClientInvitation::whereNull('accepted_at')->whereNull('revoked_at')->where('expires_at', '>', now())->count(),
            'clients' => User::where('role', 'client')->count(),
            'latest' => $all->take(6),
        ]);
    }

    // ---------- Invitations ----------
    public function invitations()
    {
        return view('spaces.manager.invitations', [
            'items' => ClientInvitation::with('inviter')->latest()->get(),
            'fake' => SpaceMail::isFake(),
            'toValidate' => User::whereIn('role', ['client', 'manager'])->where('account_status', 'pending')->latest()->get(),
            'refused' => User::whereIn('role', ['client', 'manager'])->where('account_status', 'refused')->latest()->get()
        ]);
    }

    public function invite(Request $r)
    {
        $d = $r->validate(['name' => 'required|string|max:120', 'email' => 'required|email|max:190', 'phone' => 'nullable|string|max:40', 'role' => ['required', Rule::in(['client', 'manager'])]]);
        $d['email'] = strtolower(trim($d['email']));
        if (User::where('email', $d['email'])->exists())
            return back()->withInput()->withErrors(['email' => sp_t('inv_exists')]);
        // Une invitation ouverte par e-mail : on révoque les précédentes non acceptées
        ClientInvitation::where('email', $d['email'])->whereNull('accepted_at')->whereNull('revoked_at')->update(['revoked_at' => now()]);
        $inv = ClientInvitation::create($d + ['invited_by' => $r->user()->id, 'token_hash' => '', 'expires_at' => now()->addDays(ClientInvitation::DAYS)]);
        return $this->deliver($inv);
    }

    public function resend(ClientInvitation $inv)
    {
        abort_if($inv->accepted_at, 422);
        $inv->forceFill(['revoked_at' => null, 'send_count' => $inv->send_count + 1])->save();
        return $this->deliver($inv);
    }

    private function deliver(ClientInvitation $inv)
    {
        $link = $inv->refreshLink();
        $body = str_replace([':name', ':link'], [$inv->name, $link], sp_t('inv_mail_body'));
        $status = SpaceMail::send($inv->email, sp_t('inv_mail_subject'), $body);
        $inv->forceFill(['mail_status' => $status])->save();
        return redirect()->route('manager.invitations')->with('inv_link', ['id' => $inv->id, 'url' => $link, 'status' => $status, 'name' => $inv->name, 'phone' => $inv->phone]);
    }

    // ---------- Validation des comptes ----------
    private function pendingAccount(User $user): void
    {
        abort_unless(in_array($user->role, ['client', 'manager'], true) && !$user->isAdmin() && $user->account_status !== 'active', 404);
    }

    public function approve(User $user)
    {
        $this->pendingAccount($user);
        $user->forceFill(['account_status' => 'active'])->save();
        if ($user->role === 'client')
            \App\Models\ClientNotice::create(['user_id' => $user->id, 'subject' => sp_t('val_mail_subject'), 'body' => str_replace([':name', ':link'], [$user->name, route('home')], sp_t('val_mail_body'))]);
        SpaceMail::send($user->email, sp_t('val_mail_subject'), str_replace([':name', ':link'], [$user->name, route('home')], sp_t('val_mail_body')));
        return back()->with('ok', sp_t('val_done'));
    }

    public function refuse(User $user)
    {
        $this->pendingAccount($user);
        $user->forceFill(['account_status' => 'refused'])->save();
        return back()->with('ok', sp_t('val_refused_done'));
    }

    public function revoke(ClientInvitation $inv)
    {
        if (!$inv->accepted_at)
            $inv->forceFill(['revoked_at' => now()])->save();
        return back()->with('ok', sp_t('saved'));
    }

    // ---------- Messages ----------
    /** Destinataires possibles : comptes validés client/manager (hors expéditeur). */
    private function recipients(Request $r)
    {
        return User::whereIn('role', ['client', 'manager'])->where('account_status', 'active')->where('id', '!=', $r->user()->id)->orderBy('name')->get(['id', 'name', 'email', 'role']);
    }

    public function inbox(Request $r, string $tab = 'messages')
    {
        $box = $tab === 'messages' ? $r->input('box') : null;
        if (in_array($box, ['sent', 'received'], true)) {
            $r->validate([
                'page' => ['nullable', 'integer', 'min:1'],
            ]);

            $me = $r->user();

            // Conversations de la page et compteurs calculés en base.
            $convs = \App\Models\Conversation::query()
                ->with([
                    'sender:id,name,email',
                    'recipient:id,name,email,role',
                ])
                ->withCount([
                    'replies',
                    'replies as unread_replies_count' => fn($query) => $query
                        ->where('user_id', '!=', $me->id)
                        ->whereNull('read_at'),
                ])
                ->where(
                    $box === 'sent' ? 'sender_id' : 'recipient_id',
                    $me->id
                )
                ->latest('id')
                ->paginate(10)
                ->withQueryString();

            $convs->getCollection()->each(function ($conversation) use ($me) {
                $initialUnread = (
                    $conversation->recipient_id === $me->id
                    && !$conversation->read_at
                ) ? 1 : 0;

                $conversation->unread = $initialUnread
                    + (int) $conversation->unread_replies_count;
            });
            return view('spaces.manager.inbox', [
                'tab' => $tab,
                'box' => $box,
                'convs' => $convs,
                'items' => collect(),
                'counts' => collect(),
                'sources' => ['contact'],
                'statuses' => Inbox::STATUSES,
                'recipients' => $this->recipients($r),
                'boxCounts' => $this->boxCounts($me),
            ]);
        }
        $src = Inbox::TABS[$tab] ?? abort(404);

        $statuses = $tab === 'reservations'
            ? Inbox::RES_STATUSES
            : Inbox::STATUSES;

        // Validation des filtres et de la page.
        $validated = $r->validate([
            'source' => ['nullable', 'string', Rule::in($src)],
            'status' => ['nullable', 'string', Rule::in($statuses)],
            'replied' => ['nullable', 'string', Rule::in(['0', '1'])],
            'q' => ['nullable', 'string', 'max:200'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => [
                'nullable',
                'date_format:Y-m-d',
                ...($r->filled('from') ? ['after_or_equal:from'] : []),
            ],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $filters = array_intersect_key(
            $validated,
            array_flip(['source', 'status', 'replied', 'q', 'from', 'to'])
        );

        return view('spaces.manager.inbox', [
            'items' => Inbox::paginate($src, $filters, 10),
            'counts' => Inbox::statusCounts($src),
            'tab' => $tab,
            'sources' => $src,
            'box' => null,
            'recipients' => $tab === 'messages'
                ? $this->recipients($r)
                : collect(),
            'boxCounts' => $tab === 'messages'
                ? $this->boxCounts($r->user())
                : [],
            'statuses' => $statuses,
        ]);

    }

    public function message(string $source, int $id)
    {
        $m = Inbox::find($source, $id) ?? abort(404);
        $replies = InboxReply::with('author')->where(['source' => $source, 'source_id' => $id])->oldest()->get();
        return view('spaces.manager.message', compact('m', 'replies'));
    }

    public function status(Request $r, string $source, int $id)
    {
        Inbox::find($source, $id) ?? abort(404);
        Inbox::setStatus($source, $id, $r->validate(['status' => ['required', Rule::in(Inbox::statuses($source))]])['status']);
        return back()->with('ok', sp_t('saved'));
    }

    public function reply(Request $r, string $source, int $id)
    {
        $m = Inbox::find($source, $id) ?? abort(404);
        $body = $r->validate(['body' => 'required|string|max:5000'])['body'];
        $status = $m['email'] ? SpaceMail::send($m['email'], sp_t('reply_subject') . ($m['subject'] && $m['subject'] !== '—' ? ' — ' . $m['subject'] : ''), $body) : null;
        InboxReply::create(['source' => $source, 'source_id' => $id, 'user_id' => $r->user()->id, 'to_email' => $m['email'], 'body' => $body, 'mail_status' => $status]);
        if ($m['status'] === 'nouveau' && $source !== 'essai')
            Inbox::setStatus($source, $id, 'en_cours');
        return back()->with('ok', sp_t('reply_done'));
    }

    private function boxCounts(User $me): array
    {
        $C = \App\Models\Conversation::class;
        return [
            'sent' => $C::where('sender_id', $me->id)->count(),
            'received' => $C::where('recipient_id', $me->id)->count(),
            'sent_unread' => $C::where('sender_id', $me->id)->get()->sum(fn($c) => $c->unreadFor($me)),
            'received_unread' => $C::where('recipient_id', $me->id)->get()->sum(fn($c) => $c->unreadFor($me))
        ];
    }

    // ---------- Messages directs (manager -> utilisateurs) ----------
    public function compose(Request $r)
    {
        $allowed = $this->recipients($r)->pluck('id')->all();
        $d = $r->validate([
            'recipients' => 'required|array|min:1|max:500',
            'recipients.*' => ['integer', Rule::in($allowed)],
            'subject' => 'required|string|max:160',
            'body' => 'required|string|max:5000',
        ], [
            'recipients.required' => sp_t('dm_e_recipients'),
            'recipients.min' => sp_t('dm_e_recipients'),
            'recipients.*.in' => sp_t('dm_e_recipients'),
            'subject.required' => sp_t('dm_e_subject'),
            'body.required' => sp_t('dm_e_body')
        ]);
        $batch = (string) \Illuminate\Support\Str::uuid();
        $users = User::whereIn('id', array_unique($d['recipients']))->get();
        foreach ($users as $u) {
            // Une conversation séparée par destinataire : personne ne voit les autres destinataires
            $c = \App\Models\Conversation::create(['sender_id' => $r->user()->id, 'recipient_id' => $u->id, 'subject' => $d['subject'], 'body' => $d['body'], 'batch' => $batch]);
            $link = $u->role === 'client' ? route('client.thread', 'dm-' . $c->id) : route('manager.conversation', $c);
            $c->forceFill(['mail_status' => SpaceMail::send($u->email, $d['subject'], $d['body'] . "\n\n— " . $r->user()->name . ", KPANDJI\n" . $link)])->save();
        }
        $msg = str_replace(':n', (string) $users->count(), sp_t('dm_sent_ok'));
        return $r->expectsJson() ? response()->json(['ok' => true, 'count' => $users->count(), 'message' => $msg, 'redirect' => route('manager.inbox', ['box' => 'sent'])])
            : redirect()->route('manager.inbox', ['box' => 'sent'])->with('ok', $msg);
    }

    public function conversation(Request $r, \App\Models\Conversation $conversation)
    {
        abort_unless($conversation->involves($r->user()) || $r->user()->isAdmin(), 403);
        $conversation->markReadFor($r->user());
        return view('spaces.manager.conversation', ['c' => $conversation->load(['sender', 'recipient']), 'replies' => $conversation->replies()->with('author')->get()]);
    }

    public function conversationReply(Request $r, \App\Models\Conversation $conversation)
    {
        abort_unless($conversation->involves($r->user()), 403);
        $body = $r->validate(['body' => 'required|string|max:5000'])['body'];
        $other = $conversation->sender_id === $r->user()->id ? $conversation->recipient : $conversation->sender;
        $status = $other ? SpaceMail::send($other->email, sp_t('reply_subject') . ' — ' . $conversation->subject, $body) : null;
        InboxReply::create(['source' => 'dm', 'source_id' => $conversation->id, 'user_id' => $r->user()->id, 'to_email' => $other?->email, 'body' => $body, 'mail_status' => $status]);
        return back()->with('ok', sp_t('reply_done'));
    }

    // ---------- Clients, investissements, écritures ----------
    public function clients()
    {
        return view('spaces.manager.clients', ['clients' => User::where('role', 'client')->withCount('investments')->withSum('investments', 'amount')->orderBy('name')->get()]);
    }

    public function client(User $user)
    {
        abort_unless($user->role === 'client', 404);
        return view('spaces.manager.client', ['client' => $user, 'investments' => $user->investments()->with('entries')->get()]);
    }

    private function invRules(): array
    {
        return [
            'label' => 'required|string|max:160',
            'program' => ['nullable', Rule::in(Investment::PROGRAMS)],
            'amount' => 'required|integer|min:0|max:999999999999',
            'start_date' => 'nullable|date',
            'status' => ['required', Rule::in(Investment::STATUSES)],
            'notes' => 'nullable|string|max:3000'
        ];
    }

    private function money(Request $r, string ...$keys): void
    {
        foreach ($keys as $k)
            if ($r->has($k))
                $r->merge([$k => preg_replace('/[^\d]/', '', (string) $r->input($k))]);
    }

    public function storeInvestment(Request $r, User $user)
    {
        abort_unless($user->role === 'client', 404);
        Gate::authorize('manage', Investment::class);
        $this->money($r, 'amount');
        $user->investments()->create($r->validate($this->invRules()));
        return back()->with('ok', sp_t('saved'));
    }

    public function updateInvestment(Request $r, Investment $investment)
    {
        Gate::authorize('manage', $investment);
        $this->money($r, 'amount');
        $investment->update($r->validate($this->invRules()));
        return back()->with('ok', sp_t('saved'));
    }

    public function destroyInvestment(Investment $investment)
    {
        Gate::authorize('manage', $investment);
        $investment->delete();
        return back()->with('ok', sp_t('saved'));
    }

    private function entryRules(): array
    {
        return ['entry_date' => 'required|date', 'amount' => 'required|integer|min:0|max:999999999999', 'type' => ['required', Rule::in(RevenueEntry::TYPES)], 'description' => 'nullable|string|max:255'];
    }

    public function storeEntry(Request $r, Investment $investment)
    {
        Gate::authorize('manage', $investment);
        $this->money($r, 'amount');
        $investment->entries()->create($r->validate($this->entryRules()));
        return back()->with('ok', sp_t('saved'));
    }

    public function updateEntry(Request $r, RevenueEntry $entry)
    {
        Gate::authorize('manage', $entry->investment);
        $this->money($r, 'amount');
        $entry->update($r->validate($this->entryRules()));
        return back()->with('ok', sp_t('saved'));
    }

    public function destroyEntry(RevenueEntry $entry)
    {
        Gate::authorize('manage', $entry->investment);
        $entry->delete();
        return back()->with('ok', sp_t('saved'));
    }
}
