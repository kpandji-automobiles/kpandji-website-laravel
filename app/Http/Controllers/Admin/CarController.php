<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class CarController extends Controller
{
    public function index() { return view('admin.cars.index', ['cars' => Car::orderBy('sort_order')->orderBy('id')->get()]); }
    public function create() { return view('admin.cars.form', ['car' => new Car(['is_active' => true, 'sort_order' => (Car::max('sort_order') ?? 0) + 1])]); }
    public function edit(Car $car) { return view('admin.cars.form', compact('car')); }
    public function store(Request $r) { $car = new Car(); $this->save($r, $car); return redirect()->route('admin.cars.index')->with('status', "« {$car->name} » a été créé."); }
    public function update(Request $r, Car $car) { $this->save($r, $car); return redirect()->route('admin.cars.index')->with('status', "« {$car->name} » a été mis à jour."); }
    public function destroy(Car $car)
    {
        foreach (['image', 'hero_image', 'brochure'] as $f) $this->deleteUpload($car->$f);
        $name = $car->name; $car->delete();
        return redirect()->route('admin.cars.index')->with('status', "« $name » a été supprimé.");
    }
    public function toggle(Car $car) { $car->forceFill(['is_active' => !$car->is_active])->save(); return back()->with('status', $car->name . ($car->is_active ? ' est visible.' : ' est masqué.')); }
    public function reorder(Request $r)
    {
        foreach ((array) $r->input('order', []) as $id => $pos) Car::whereKey($id)->update(['sort_order' => (int) $pos]);
        return back()->with('status', 'Ordre enregistré.');
    }

    private function save(Request $r, Car $car): void
    {
        $r->merge(['slug' => Str::slug($r->input('slug') ?: $r->input('name'))]);
        $d = $r->validate([
            'name' => 'required|string|max:120',
            'slug' => ['required', 'alpha_dash', 'max:120', Rule::unique('cars', 'slug')->ignore($car->id)],
            'category' => 'nullable|string|max:120',
            'description' => 'nullable|string|max:5000',
            'highlights_text' => 'nullable|string|max:2000',
            'specs_text' => 'nullable|string|max:20000',
            'price' => 'nullable|integer|min:0',
            'promo_price' => 'nullable|integer|min:0',
            'campaign_price' => 'nullable|integer|min:0',
            'maintenance_cost' => 'nullable|integer|min:0',
            'warranty' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'versions' => 'nullable|array',
            'versions.*.version' => 'nullable|string|max:150',
            'versions.*.motorisation' => 'nullable|string|max:200',
            'versions.*.boite' => 'nullable|string|max:80',
            'versions.*.transmission' => 'nullable|string|max:80',
            'versions.*.promo_price' => 'nullable|integer|min:0',
            'versions.*.maintenance_cost' => 'nullable|integer|min:0',
            'versions.*.campaign_price' => 'nullable|integer|min:0',
            'image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'brochure_file' => 'nullable|file|mimes:pdf|max:20480',
        ], [], ['name' => 'nom', 'image_file' => 'photo', 'brochure_file' => 'fiche PDF', 'promo_price' => 'prix promo', 'price' => 'prix catalogue']);

        $car->fill(collect($d)->only(['name', 'slug', 'category', 'description', 'price', 'promo_price', 'campaign_price', 'maintenance_cost', 'warranty'])->all());
        $car->sort_order = $d['sort_order'] ?? 0;
        $car->is_active = $r->boolean('is_active');
        $car->highlights = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $d['highlights_text'] ?? ''))));
        $car->specs = $this->parseSpecs($d['specs_text'] ?? '');
        $versions = collect($d['versions'] ?? [])->filter(fn ($v) => trim($v['version'] ?? '') !== '')
            ->map(fn ($v) => [
                'version' => $v['version'], 'motorisation' => $v['motorisation'] ?? '', 'boite' => $v['boite'] ?? '', 'transmission' => $v['transmission'] ?? '',
                'promo_price' => isset($v['promo_price']) ? (int) $v['promo_price'] : null,
                'maintenance_cost' => isset($v['maintenance_cost']) ? (int) $v['maintenance_cost'] : null,
                'campaign_price' => isset($v['campaign_price']) ? (int) $v['campaign_price'] : null,
            ])->values()->all();
        $car->versions = $versions ?: null;

        if ($r->hasFile('image_file')) {
            $this->deleteUpload($car->image);
            if ($car->hero_image !== $car->image) $this->deleteUpload($car->hero_image);
            $path = 'storage/' . $r->file('image_file')->store('cars', 'public');
            $car->image = $path; $car->hero_image = $path;
        }
        if ($r->boolean('remove_brochure')) { $this->deleteUpload($car->brochure); $car->brochure = null; }
        if ($r->hasFile('brochure_file')) {
            $this->deleteUpload($car->brochure);
            $car->brochure = 'storage/' . $r->file('brochure_file')->store('fiches', 'public');
        }
        if (!$car->image) { $car->image = 'images/cars/placeholder.svg'; $car->hero_image = $car->hero_image ?: $car->image; }
        $car->position = $car->sort_order;
        $car->save();
    }

    /** Format texte : "## Groupe" puis "Libellé : valeur" par ligne. */
    private function parseSpecs(string $text): ?array
    {
        $out = []; $group = 'Caractéristiques';
        foreach (preg_split('/\r?\n/', $text) as $line) {
            $line = trim($line); if ($line === '') continue;
            if (str_starts_with($line, '##')) { $group = trim(ltrim($line, '#')) ?: $group; continue; }
            [$k, $v] = array_pad(array_map('trim', explode(':', $line, 2)), 2, '');
            $out[$group][$k] = $v;
        }
        return $out ?: null;
    }
    private function deleteUpload(?string $path): void
    {
        if ($path && str_starts_with($path, 'storage/')) Storage::disk('public')->delete(substr($path, 8));
    }
}
