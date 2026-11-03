<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ClientInvitation;
use App\Models\Conversation;
use App\Models\InboxReply;
use App\Models\Investment;
use App\Models\RevenueEntry;
use App\Models\User;
use App\Support\AccountValidation;
use App\Support\DirectMessages;
use App\Support\Inbox;
use App\Support\InboxReplies;
use App\Support\Invitations;
use App\Support\SpaceMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/** Espace manager (/espace-manager) — accessible au rôle manager et à l'admin. */
class ManagerController extends Controller
{
    /** Dernières demandes et compteurs calculés en base. */
    public function dashboard()
    {
        $counts = Inbox::statusCounts(Inbox::SOURCES);

        return view('spaces.manager.dashboard', [
            'open' => (int) $counts
                ->except(['traite'])
                ->sum(),

            'pending' => ClientInvitation::query()
                ->whereNull('accepted_at')
                ->whereNull('revoked_at')
                ->where('expires_at', '>', now())
                ->count(),

            'clients' => User::query()
                ->where('role', 'client')
                ->count(),

            'latest' => Inbox::paginate(
                Inbox::SOURCES,
                [],
                6,
                1
            )->getCollection(),
        ]);
    }

    // ---------- Invitations ----------
    public function invitations()
    {
        return view('spaces.manager.invitations', [
            'items' => ClientInvitation::with('inviter')->latest()->get(),
            'fake' => SpaceMail::isFake(),
            'toValidate' => User::whereIn('role', ['client', 'manager'])->where('account_status', 'pending')->latest()->get(),
            'refused' => User::whereIn('role', ['client', 'manager'])->where('account_status', 'refused')->latest()->get(),
        ]);
    }

    public function invite(Request $r)
    {
        $d = $r->validate(['name' => 'required|string|max:120', 'email' => 'required|email|max:190', 'phone' => 'nullable|string|max:40', 'role' => ['required', Rule::in(['client', 'manager'])]]);
        if (Invitations::emailTaken($d['email'])) {
            return back()->withInput()->withErrors(['email' => sp_t('inv_exists')]);
        }

        return $this->sent(Invitations::invite($d, $r->user()));
    }

    public function resend(ClientInvitation $inv)
    {
        abort_unless(Invitations::isOpen($inv), 422);

        return $this->sent(Invitations::resend($inv));
    }

    /** @param  array{invitation: ClientInvitation, link: string}  $sent */
    private function sent(array $sent)
    {
        $inv = $sent['invitation'];

        return redirect()->route('manager.invitations')->with('inv_link', ['id' => $inv->id, 'url' => $sent['link'], 'status' => $inv->mail_status, 'name' => $inv->name, 'phone' => $inv->phone]);
    }

    // ---------- Validation des comptes ----------
    public function approve(User $user)
    {
        abort_unless(AccountValidation::canDecide($user), 404);
        AccountValidation::approve($user);

        return back()->with('ok', sp_t('val_done'));
    }

    public function refuse(User $user)
    {
        abort_unless(AccountValidation::canDecide($user), 404);
        AccountValidation::refuse($user);

        return back()->with('ok', sp_t('val_refused_done'));
    }

    public function revoke(ClientInvitation $inv)
    {
        Invitations::revoke($inv);

        return back()->with('ok', sp_t('saved'));
    }

    // ---------- Messages ----------
    public function inbox(Request $r, string $tab = 'messages')
    {
        $box = $tab === 'messages' ? $r->input('box') : null;
        if (in_array($box, ['sent', 'received'], true)) {
            $r->validate([
                'page' => ['nullable', 'integer', 'min:1'],
            ]);

            $me = $r->user();

            // Conversations de la page et compteurs calculés en base.
            $convs = Conversation::query()
                ->with([
                    'sender:id,name,email',
                    'recipient:id,name,email,role',
                ])
                ->withCount([
                    'replies',
                    'replies as unread_replies_count' => fn ($query) => $query
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
                    && ! $conversation->read_at
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
                'recipients' => DirectMessages::recipients($r->user()),
                'boxCounts' => DirectMessages::boxCounts($me),
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
                ? DirectMessages::recipients($r->user())
                : collect(),
            'boxCounts' => $tab === 'messages'
                ? DirectMessages::boxCounts($r->user())
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
        InboxReplies::send($m, $r->user(), $r->validate(['body' => 'required|string|max:5000'])['body']);

        return back()->with('ok', sp_t('reply_done'));
    }

    // ---------- Messages directs (manager -> utilisateurs) ----------
    public function compose(Request $r)
    {
        $allowed = DirectMessages::recipients($r->user())->pluck('id')->all();
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
            'body.required' => sp_t('dm_e_body'),
        ]);
        $count = DirectMessages::compose($r->user(), array_unique($d['recipients']), $d['subject'], $d['body']);
        $msg = str_replace(':n', (string) $count, sp_t('dm_sent_ok'));

        return $r->expectsJson() ? response()->json(['ok' => true, 'count' => $count, 'message' => $msg, 'redirect' => route('manager.inbox', ['box' => 'sent'])])
            : redirect()->route('manager.inbox', ['box' => 'sent'])->with('ok', $msg);
    }

    public function conversation(Request $r, Conversation $conversation)
    {
        abort_unless($conversation->involves($r->user()) || $r->user()->isAdmin(), 403);
        $conversation->markReadFor($r->user());

        return view('spaces.manager.conversation', ['c' => $conversation->load(['sender', 'recipient']), 'replies' => $conversation->replies()->with('author')->get()]);
    }

    public function conversationReply(Request $r, Conversation $conversation)
    {
        abort_unless($conversation->involves($r->user()), 403);
        DirectMessages::reply($conversation, $r->user(), $r->validate(['body' => 'required|string|max:5000'])['body']);

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
            'notes' => 'nullable|string|max:3000',
        ];
    }

    private function money(Request $r, string ...$keys): void
    {
        foreach ($keys as $k) {
            if ($r->has($k)) {
                $r->merge([$k => preg_replace('/[^\d]/', '', (string) $r->input($k))]);
            }
        }
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
