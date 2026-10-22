<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Modele;
use App\Support\Cms;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/** Admin › Showroom : tout le contenu de /showroom au même endroit (clés CMS « showroomv.* » + colonnes showroom de la table modeles). */
class ShowroomController extends Controller
{
    public function index()
    {
        $fields = Cms::fields('showroomv');
        $values = [];
        foreach ($fields as $key => $f) $values[$key] = content($key);
        return view('admin.showroom', [
            'fields' => $fields, 'values' => $values,
            'modeles' => Modele::orderByDesc('in_showroom')->orderBy('sr_sort')->orderBy('id')->get(),
        ]);
    }

    /** Admin › Opportunités (/opportunities) : clés CMS « opps.* ». */
    public function opps()
    {
        $fields = Cms::fields('opps'); $values = [];
        foreach ($fields as $key => $f) $values[$key] = content($key);
        return view('admin.opportunites', compact('fields', 'values'));
    }

    public function saveOpps(Request $r)
    {
        $errors = app(ContentController::class)->persist($r, 'opps');
        if ($errors) throw ValidationException::withMessages($errors);
        $anchor = preg_replace('/[^a-z]/', '', (string) $r->input('_anchor', 'textes'));
        return redirect()->to(route('admin.opps').'#'.$anchor)->with('status', 'Opportunités enregistrées.');
    }

    /** Admin › Kpandji Sira (/sira) : clés CMS « sira.* » + inscriptions. */
    public function sira()
    {
        $fields = Cms::fields('sira'); $values = [];
        foreach ($fields as $key => $f) $values[$key] = content($key);
        return view('admin.sira', compact('fields', 'values') + ['pending' => \App\Models\SiraRequest::whereNull('handled_at')->count()]);
    }

    public function saveSira(Request $r)
    {
        $errors = app(ContentController::class)->persist($r, 'sira');
        if ($errors) throw ValidationException::withMessages($errors);
        $anchor = preg_replace('/[^a-z]/', '', (string) $r->input('_anchor', 'hero'));
        return redirect()->to(route('admin.sira').'#'.$anchor)->with('status', 'Kpandji Sira enregistré.');
    }

    public function siraRequests() { return view('admin.sira-requests', ['items' => \App\Models\SiraRequest::latest()->paginate(25)]); }

    public function siraToggle(\App\Models\SiraRequest $item)
    {
        $item->forceFill(['handled_at' => $item->handled_at ? null : now()])->save();
        return back()->with('status', $item->handled_at ? 'Inscription marquée comme traitée.' : 'Inscription remise en attente.');
    }

    public function siraDelete(\App\Models\SiraRequest $item) { $item->delete(); return back()->with('status', 'Inscription supprimée.'); }

    public function saveTexts(Request $r)
    {
        $errors = app(ContentController::class)->persist($r, 'showroomv');
        if ($errors) throw ValidationException::withMessages($errors);
        return redirect()->to(route('admin.showroom').'#textes')->with('status', 'Textes du showroom enregistrés.');
    }

    public function saveOrder(Request $r)
    {
        $d = $r->validate(['sr_sort' => 'array', 'sr_sort.*' => 'nullable|integer|min:0|max:999', 'in_showroom' => 'array']);
        foreach (Modele::all() as $m) {
            $m->update(['sr_sort' => (int) ($d['sr_sort'][$m->id] ?? $m->sr_sort), 'in_showroom' => ! empty($d['in_showroom'][$m->id])]);
        }
        return redirect()->to(route('admin.showroom').'#onglets')->with('status', 'Onglets du showroom enregistrés.');
    }

    public function saveModel(Request $r, Modele $modele)
    {
        $d = $r->validate([
            'badge_fr' => 'nullable|string|max:60', 'badge_en' => 'nullable|string|max:60',
            'tagline_fr' => 'nullable|string|max:200', 'tagline_en' => 'nullable|string|max:200',
            'sr_description_fr' => 'nullable|string|max:3000', 'sr_description_en' => 'nullable|string|max:3000',
            'brochure' => 'nullable|string|max:255', 'brochure_file' => 'nullable|file|mimes:pdf|max:51200',
            'highlights_json' => 'nullable|string|max:20000',
            'gallery_json' => 'nullable|string|max:50000',
            'gf' => 'array', 'gf.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:20480',
        ]);
        $safe = fn ($v) => preg_match('~^(https?://\S+|[A-Za-z0-9_\-./ ()]+)$~', $v) && ! str_contains($v, '..');

        $hl = [];
        foreach ((array) json_decode($d['highlights_json'] ?? '[]', true) as $h) {
            $fr = trim(mb_substr((string) ($h['fr'] ?? ''), 0, 200)); $en = trim(mb_substr((string) ($h['en'] ?? ''), 0, 200));
            if ($fr !== '' || $en !== '') $hl[] = ['fr' => $fr, 'en' => $en];
        }

        $gal = [];
        foreach ((array) json_decode($d['gallery_json'] ?? '[]', true) as $g) {
            $uid = preg_replace('/[^a-z0-9]/i', '', (string) ($g['uid'] ?? ''));
            $src = ltrim(trim((string) ($g['src'] ?? '')), '/');
            if ($uid && $r->hasFile("gf.$uid")) $src = 'storage/'.$r->file("gf.$uid")->store('showroom', 'public');
            if ($src === '' || $src === 'upload') continue;
            if (! $safe($src)) throw ValidationException::withMessages(['gallery_json' => "Galerie : chemin invalide « $src »."]);
            $tag = in_array($g['tag'] ?? '', ['ext', 'int'], true) ? $g['tag'] : '';
            $gal[] = ['src' => $src, 'alt' => trim(mb_substr((string) ($g['alt'] ?? ''), 0, 160)), 'tag' => $tag];
        }

        $brochure = $r->hasFile('brochure_file') ? 'storage/'.$r->file('brochure_file')->store('fiches', 'public') : (filled($d['brochure'] ?? null) ? ltrim(trim($d['brochure']), '/') : null);
        if ($brochure && ! $safe($brochure)) throw ValidationException::withMessages(['brochure' => 'Fiche technique : chemin invalide.']);

        $modele->update([
            'badge_fr' => $d['badge_fr'] ?? null, 'badge_en' => $d['badge_en'] ?? null,
            'tagline_fr' => $d['tagline_fr'] ?? null, 'tagline_en' => $d['tagline_en'] ?? null,
            'sr_description_fr' => $d['sr_description_fr'] ?? null, 'sr_description_en' => $d['sr_description_en'] ?? null,
            'brochure' => $brochure, 'highlights' => $hl, 'gallery' => $gal,
        ]);
        return redirect()->to(route('admin.showroom').'#modele-'.$modele->id)->with('status', $modele->name.' : showroom enregistré.');
    }
}
