<?php
namespace App\Http\Controllers\Client;
use App\Http\Controllers\Controller;
use App\Models\ClientRequest;
use App\Models\InboxReply;
use App\Models\RevenueEntry;
use App\Support\ClientThreads;
use App\Support\Inbox;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
/** Mon espace (/mon-espace) — un client ne voit que ses propres données (requêtes toujours limitées à auth()->user()). */
class ClientController extends Controller
{
    /** Écritures du client, filtrées (période, investissement, types). */
    private function entries(Request $r, ?array $types = null)
    {
        $u = $r->user();
        $q = RevenueEntry::with('investment')->whereIn('investment_id', $u->investments()->select('id'));
        if ($r->filled('investment')) {
            $inv = $u->investments()->findOrFail((int) $r->investment);
            Gate::authorize('view', $inv);
            $q->where('investment_id', $inv->id);
        }
        if ($types) $q->whereIn('type', $types);
        if ($r->filled('type') && in_array($r->type, $types ?? RevenueEntry::TYPES, true)) $q->where('type', $r->type);
        if ($r->filled('from')) $q->whereDate('entry_date', '>=', Carbon::parse($r->from));
        if ($r->filled('to')) $q->whereDate('entry_date', '<=', Carbon::parse($r->to));
        return $q->orderByDesc('entry_date')->orderByDesc('id');
    }

    private function months(Request $r, $entries): array
    {
        $end = $r->filled('to') ? Carbon::parse($r->to)->startOfMonth() : now()->startOfMonth();
        $start = $r->filled('from') ? Carbon::parse($r->from)->startOfMonth() : $end->copy()->subMonths(11);
        if ($start->diffInMonths($end) > 35) $start = $end->copy()->subMonths(35);
        $months = [];
        for ($m = $start->copy(); $m <= $end; $m->addMonth()) $months[$m->format('Y-m')] = 0;
        foreach ($entries as $e) { $k = $e->entry_date->format('Y-m'); if (isset($months[$k])) $months[$k] += $e->amount; }
        return $months;
    }

    public function dashboard(Request $r)
    {
        $u = $r->user();
        $investments = $u->investments()->withSum(['entries as rev' => fn ($q) => $q->where('type', 'revenu')], 'amount')
            ->withSum(['entries as fees' => fn ($q) => $q->where('type', 'frais')], 'amount')
            ->withSum(['entries as paid' => fn ($q) => $q->where('type', 'versement')], 'amount')->get();
        $all = RevenueEntry::whereIn('investment_id', $investments->pluck('id'))->get();
        $sum = fn ($t) => (int) $all->where('type', $t)->sum('amount');
        return view('spaces.client.dashboard', [
            'investments' => $investments, 'months' => $this->months($r, $all->where('type', 'revenu')),
            'totals' => ['invested' => (int) $investments->sum('amount'), 'revenue' => $sum('revenu'), 'fees' => $sum('frais'), 'net' => $sum('revenu') - $sum('frais'), 'paid' => $sum('versement')],
            'threads' => ClientThreads::all($u)->take(3),
        ]);
    }

    // ---------- Gains ----------
    public function gains(Request $r)
    {
        $r->validate(['from' => 'nullable|date', 'to' => 'nullable|date', 'investment' => 'nullable|integer']);
        $entries = $this->entries($r, ['revenu', 'versement'])->get();
        $byMonth = $entries->groupBy(fn ($e) => $e->entry_date->format('Y-m'))->map(fn ($g) => ['revenu' => (int) $g->where('type', 'revenu')->sum('amount'), 'versement' => (int) $g->where('type', 'versement')->sum('amount'), 'total' => (int) $g->sum('amount')]);
        $byYear = $entries->groupBy(fn ($e) => $e->entry_date->format('Y'))->map(fn ($g) => ['revenu' => (int) $g->where('type', 'revenu')->sum('amount'), 'versement' => (int) $g->where('type', 'versement')->sum('amount'), 'total' => (int) $g->sum('amount')]);
        $fees = (int) $this->entries($r, ['frais'])->sum('amount');
        return view('spaces.client.gains', ['entries' => $entries, 'byMonth' => $byMonth, 'byYear' => $byYear, 'months' => $this->months($r, $entries),
            'investments' => $r->user()->investments, 'total' => (int) $entries->sum('amount'), 'rev' => (int) $entries->where('type', 'revenu')->sum('amount'),
            'paid' => (int) $entries->where('type', 'versement')->sum('amount'), 'fees' => $fees]);
    }

    public function csv(Request $r)
    {
        $rows = $this->entries($r, ['revenu', 'versement'])->get();
        return response()->streamDownload(function () use ($rows) {
            $o = fopen('php://output', 'w'); fwrite($o, "\xEF\xBB\xBF");
            fputcsv($o, [sp_t('date'), sp_t('investment'), sp_t('type'), sp_t('description'), 'FCFA'], ';');
            foreach ($rows as $e) fputcsv($o, [$e->entry_date->format('Y-m-d'), $e->investment->label, sp_t('type_'.$e->type), $e->description, $e->amount], ';');
            fclose($o);
        }, 'kpandji-gains-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ---------- Opportunités ----------
    public function opportunities(Request $r)
    {
        return view('spaces.client.opportunities', ['items' => ClientThreads::items($r->user(), ['sira', 'privilege'])]);
    }

    // ---------- Messages ----------
    public function messages(Request $r)
    {
        return view('spaces.client.messages', ['threads' => ClientThreads::all($r->user()), 'investments' => $r->user()->investments]);
    }

    public function thread(Request $r, string $key)
    {
        $t = ClientThreads::find($r->user(), $key) ?? abort(404);
        ClientThreads::markRead($r->user(), $key);
        return view('spaces.client.thread', ['t' => $t]);
    }

    public function readAll(Request $r)
    {
        ClientThreads::markRead($r->user());
        return back()->with('ok', sp_t('saved'));
    }

    public function request(Request $r)
    {
        $u = $r->user();
        $d = $r->validate(['subject' => 'required|string|max:160', 'message' => 'required|string|max:3000',
            'investment_id' => ['nullable', 'integer', Rule::exists('investments', 'id')->where('user_id', $u->id)]]);
        $row = ClientRequest::create($d + ['user_id' => $u->id]);
        return redirect()->route('client.thread', 'client-'.$row->id)->with('ok', sp_t('req_done'));
    }

    /** Relance dans un fil de demande client (stockée dans l'historique, la demande repasse en « nouveau »). */
    public function followUp(Request $r, string $key)
    {
        $t = ClientThreads::find($r->user(), $key) ?? abort(404);
        abort_unless(in_array($t['source'], ['client', 'dm'], true), 403);
        $body = $r->validate(['body' => 'required|string|max:3000'])['body'];
        if ($t['source'] === 'dm') {
            $c = $t['conversation'];
            InboxReply::create(['source' => 'dm', 'source_id' => $c->id, 'user_id' => $r->user()->id, 'body' => $body, 'to_email' => $c->sender?->email]);
            return back()->with('ok', sp_t('req_done'));
        }
        InboxReply::create(['source' => 'client', 'source_id' => $t['opener']['id'], 'user_id' => $r->user()->id, 'body' => $body, 'read_at' => now()]);
        Inbox::setStatus('client', $t['opener']['id'], 'nouveau');
        return back()->with('ok', sp_t('req_done'));
    }
}
