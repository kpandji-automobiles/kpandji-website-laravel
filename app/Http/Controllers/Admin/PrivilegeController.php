<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\PrivilegeRequest;
use App\Support\Cms;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
/** Admin › Kpandji Privilège (/privilege) : clés CMS « priv.* » + demandes. */
class PrivilegeController extends Controller
{
    public function index()
    {
        $fields = Cms::fields('priv'); $values = [];
        foreach ($fields as $key => $f) $values[$key] = content($key);
        return view('admin.privilege', compact('fields', 'values') + ['pending' => PrivilegeRequest::whereNull('handled_at')->count()]);
    }

    public function save(Request $r)
    {
        $errors = app(ContentController::class)->persist($r, 'priv');
        if ($errors) throw ValidationException::withMessages($errors);
        $anchor = preg_replace('/[^a-z]/', '', (string) $r->input('_anchor', 'hero'));
        return redirect()->to(route('admin.privilege').'#'.$anchor)->with('status', 'Kpandji Privilège enregistré.');
    }

    public function requests() { return view('admin.privilege-requests', ['items' => PrivilegeRequest::latest()->paginate(25)]); }

    public function toggle(PrivilegeRequest $item)
    {
        $item->forceFill(['handled_at' => $item->handled_at ? null : now()])->save();
        return back()->with('status', $item->handled_at ? 'Demande marquée comme traitée.' : 'Demande remise en attente.');
    }

    public function destroy(PrivilegeRequest $item) { $item->delete(); return back()->with('status', 'Demande supprimée.'); }
}
