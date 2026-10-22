<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Support\Cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class ContentController extends Controller
{
    public function index(Request $r)
    {
        $tabs = config('cms.tabs');
        $tab = array_key_exists($r->query('tab'), $tabs) ? $r->query('tab') : array_key_first($tabs);
        if ($r->query('tab') === 'pages') return redirect()->route('admin.pages.index');
        $fields = Cms::fields($tab);
        $values = [];
        foreach ($fields as $key => $f) $values[$key] = content($key);
        return view('admin.content', compact('tabs', 'tab', 'fields', 'values'));
    }

    public function save(Request $r, string $tab)
    {
        abort_unless(array_key_exists($tab, config('cms.tabs')), 404);
        $errors = $this->persist($r, $tab);
        if ($errors) throw ValidationException::withMessages($errors);
        return redirect()->route('admin.content', ['tab' => $tab])->with('status', 'Contenu enregistré. Le site est à jour.');
    }

    /** Enregistre les champs d'un onglet du registre CMS ; renvoie les erreurs (réutilisé par Admin › Showroom). */
    public function persist(Request $r, string $tab): array
    {
        $fields = Cms::fields($tab);
        $in = (array) $r->input('c', []);
        $reset = (array) $r->input('reset', []);
        $errors = [];

        foreach ($fields as $key => [$g, $type, $label, $default]) {
            $k = str_replace('.', '__', $key);
            if (! empty($reset[$k])) { Content::where('key', $key)->delete(); continue; }

            if (in_array($type, ['image', 'video'], true)) {
                if ($r->hasFile("f.$k")) {
                    $rules = $type === 'image' ? 'image|mimes:jpg,jpeg,png,webp,gif|max:8192' : 'mimetypes:video/mp4,video/webm|max:204800';
                    $v = validator(['file' => $r->file("f.$k")], ['file' => $rules]);
                    if ($v->fails()) { $errors["f.$k"] = "$label : ".$v->errors()->first('file'); continue; }
                    $value = 'storage/'.$r->file("f.$k")->store('cms', 'public');
                } else {
                    $value = trim((string) ($in[$k] ?? ''));
                    if ($value === '') continue; // garde le fichier existant
                    if (! preg_match('~^(https?://\S+|[A-Za-z0-9_\-./]+)$~', $value) || str_contains($value, '..')) { $errors["c.$k"] = "$label : chemin ou URL invalide."; continue; }
                }
            } elseif ($type === 'list') {
                if (! array_key_exists($k, $in)) continue;
                $rows = json_decode((string) ($in[$k] ?? '[]'), true);
                if (! is_array($rows)) { $errors["c.$k"] = "$label : liste invalide."; continue; }
                $cols = array_keys(config('cms.fields')[$key][4] ?? ['label' => '']);
                $clean = [];
                foreach ($rows as $row) {
                    if (! is_array($row)) continue;
                    $item = [];
                    foreach ($cols as $c) $item[$c] = trim(mb_substr((string) ($row[$c] ?? ''), 0, 500));
                    if (implode('', $item) === '') continue;
                    if (isset($item['url']) && ! Cms::validUrl($item['url'])) { $errors["c.$k"] = "$label : lien invalide « {$item['url']} »."; continue 2; }
                    if (isset($item['visible'])) $item['visible'] = $item['visible'] === '0' ? '0' : '1';
                    $clean[] = $item;
                }
                $value = json_encode($clean, JSON_UNESCAPED_UNICODE);
            } else {
                if (! array_key_exists($k, $in)) continue;
                $value = (string) ($in[$k] ?? '');
                $value = $type === 'richtext' ? Cms::cleanHtml($value) : trim(mb_substr($value, 0, 5000));
                if ($type === 'url' && ! Cms::validUrl($value)) { $errors["c.$k"] = "$label : lien invalide (utilisez /page, #ancre ou https://…)."; continue; }
            }

            $row = Content::firstOrNew(['key' => $key]);
            $row->fill(['group' => $g, 'type' => $type, 'label' => $label, 'value' => $value])->save();
        }
        Cache::forget(Content::CACHE);
        return $errors;
    }
}
