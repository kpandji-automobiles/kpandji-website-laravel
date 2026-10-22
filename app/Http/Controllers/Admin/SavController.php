<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SavRequest;
use App\Support\Cms;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
/** Admin › S.A.V. (/sav) : clés CMS « savp.* » + demandes « Écrire au S.A.V. ». */
class SavController extends Controller
{
    public function index()
    {
        $fields = Cms::fields('savp'); $values = [];
        foreach ($fields as $key => $f) $values[$key] = content($key);
        return view('admin.sav', compact('fields', 'values') + ['pending' => SavRequest::whereNull('handled_at')->count()]);
    }

    public function save(Request $r)
    {
        $errors = app(ContentController::class)->persist($r, 'savp');
        if ($errors) throw ValidationException::withMessages($errors);
        $anchor = preg_replace('/[^a-z]/', '', (string) $r->input('_anchor', 'hero'));
        return redirect()->to(route('admin.sav').'#'.$anchor)->with('status', 'Page S.A.V. enregistrée.');
    }

    public function requests() { return view('admin.sav-requests', ['items' => SavRequest::latest()->paginate(25)]); }

    public function toggle(SavRequest $item)
    {
        $item->forceFill(['handled_at' => $item->handled_at ? null : now()])->save();
        return back()->with('status', $item->handled_at ? 'Demande marquée comme traitée.' : 'Demande remise en attente.');
    }

    public function destroy(SavRequest $item) { $item->delete(); return back()->with('status', 'Demande supprimée.'); }
}
