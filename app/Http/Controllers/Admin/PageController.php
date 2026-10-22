<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\Cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index()
    {
        return view('admin.pages.index', ['pages' => Page::orderByDesc('is_system')->orderBy('title')->get()]);
    }

    public function create() { return view('admin.pages.form', ['page' => new Page(['published' => true])]); }
    public function edit(Page $page) { return view('admin.pages.form', compact('page')); }

    public function store(Request $r)
    {
        $page = Page::create($this->data($r, new Page));
        return redirect()->route('admin.pages.edit', $page)->with('status', 'Page créée : '.$page->url());
    }

    public function update(Request $r, Page $page)
    {
        $page->update($this->data($r, $page));
        return redirect()->route('admin.pages.edit', $page)->with('status', 'Page enregistrée.');
    }

    public function destroy(Page $page)
    {
        abort_if($page->is_system, 403, 'Les pages du site ne peuvent pas être supprimées (dépubliez-les plutôt).');
        $page->delete();
        return redirect()->route('admin.pages.index')->with('status', 'Page supprimée.');
    }

    private function data(Request $r, Page $page): array
    {
        $r->merge(['slug' => Str::slug((string) ($r->input('slug') ?: $r->input('title')))]);
        $taken = collect(Route::getRoutes())->map(fn ($rt) => explode('/', trim($rt->uri(), '/'))[0])->filter(fn ($s) => $s && ! str_contains($s, '{'))->unique()->all();
        $reserved = array_unique(array_merge(Page::RESERVED, $page->is_system ? [] : $taken));
        $d = $r->validate([
            'title' => 'required|string|max:160',
            'slug' => $page->is_system ? 'nullable' : ['required', 'max:80', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('pages', 'slug')->ignore($page->id), Rule::notIn($reserved)],
            'kicker' => 'nullable|string|max:160',
            'intro' => 'nullable|string|max:2000',
            'body' => 'nullable|string|max:100000',
            'seo_title' => 'nullable|string|max:160',
            'seo_description' => 'nullable|string|max:300',
            'nav_order' => 'nullable|integer|min:0|max:999',
            'hero' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:8192',
        ], ['slug.not_in' => 'Ce slug est déjà utilisé par une page du site.', 'slug.regex' => 'Slug : lettres minuscules, chiffres et tirets uniquement.']);

        $out = collect($d)->except(['hero', 'slug'])->all();
        if (! $page->is_system) $out['slug'] = $d['slug'];
        $out['body'] = Cms::cleanHtml($d['body'] ?? '');
        $out['nav_order'] = (int) ($d['nav_order'] ?? 0);
        $out['published'] = $r->boolean('published');
        $out['in_nav'] = $r->boolean('in_nav');
        if ($r->hasFile('hero')) $out['hero_image'] = 'storage/'.$r->file('hero')->store('cms', 'public');
        elseif ($r->boolean('remove_hero')) $out['hero_image'] = null;
        return $out;
    }
}
