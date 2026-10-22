<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Modele;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Admin › Modèles (accueil) : section « Modèles » au-dessus du S.A.V. */
class ModeleController extends Controller
{
    public const MEDIA = [
        'top_image' => ['Image du haut (1/3)', 'image'],
        'top_video' => ['Vidéo du haut (2/3)', 'video'],
        'bottom_left_image' => ['Image bas gauche (16/10)', 'image'],
        'bottom_right_image' => ['Image bas droite (16/10)', 'image'],
    ];

    public function index() { return view('admin.modeles.index', ['modeles' => Modele::orderBy('sort')->orderBy('id')->get()]); }
    public function create() { return view('admin.modeles.form', ['modele' => new Modele(['is_active' => true, 'sort' => (int) Modele::max('sort') + 1]), 'cars' => Car::orderBy('name')->get()]); }
    public function edit(Modele $modele) { return view('admin.modeles.form', ['modele' => $modele, 'cars' => Car::orderBy('name')->get()]); }

    public function store(Request $r)
    {
        $m = Modele::create($this->data($r, new Modele));
        return redirect()->route('admin.modeles.edit', $m)->with('status', 'Modèle ajouté.');
    }

    public function update(Request $r, Modele $modele)
    {
        $modele->update($this->data($r, $modele));
        return redirect()->route('admin.modeles.edit', $modele)->with('status', 'Modèle enregistré.');
    }

    public function destroy(Modele $modele)
    {
        $modele->delete();
        return redirect()->route('admin.modeles.index')->with('status', 'Modèle supprimé de la section.');
    }

    private function data(Request $r, Modele $m): array
    {
        $rules = [
            'name' => 'required|string|max:120',
            'car_slug' => ['nullable', Rule::exists('cars', 'slug')],
            'description_fr' => 'nullable|string|max:3000',
            'description_en' => 'nullable|string|max:3000',
            'char_fr' => 'array', 'char_fr.*' => 'nullable|string|max:200',
            'char_en' => 'array', 'char_en.*' => 'nullable|string|max:200',
            'sort' => 'nullable|integer|min:0|max:999',
        ];
        foreach (self::MEDIA as $f => [, $type]) {
            $rules[$f] = 'nullable|string|max:255';
            $rules[$f.'_file'] = $type === 'video' ? 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime|max:204800' : 'nullable|image|mimes:jpg,jpeg,png,webp|max:20480';
        }
        $d = $r->validate($rules);

        $chars = [];
        foreach (($d['char_fr'] ?? []) as $i => $fr) {
            $en = $d['char_en'][$i] ?? '';
            if (filled($fr) || filled($en)) $chars[] = ['fr' => (string) $fr, 'en' => (string) $en];
        }
        $out = [
            'name' => $d['name'],
            'car_slug' => $d['car_slug'] ?? null,
            'description_fr' => $d['description_fr'] ?? null,
            'description_en' => $d['description_en'] ?? null,
            'characteristics' => $chars,
            'sort' => (int) ($d['sort'] ?? 0),
            'is_active' => $r->boolean('is_active'),
        ];
        if (! $m->exists) $out['key'] = Str::slug($d['name']).'-'.Str::lower(Str::random(4));
        foreach (array_keys(self::MEDIA) as $f) {
            $out[$f] = $r->hasFile($f.'_file') ? 'storage/'.$r->file($f.'_file')->store('modeles', 'public') : (filled($d[$f] ?? null) ? ltrim($d[$f], '/') : null);
        }
        return $out;
    }
}
