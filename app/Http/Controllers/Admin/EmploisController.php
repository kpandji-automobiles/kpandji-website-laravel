<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Support\Cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
/** Admin › Emplois : contenu de /emplois (clés « emp.* ») + candidatures (job_applications). */
class EmploisController extends Controller
{
    public function index()
    {
        $fields = Cms::fields('emp'); $values = [];
        foreach ($fields as $key => $f) $values[$key] = content($key);
        return view('admin.emplois', compact('fields', 'values') + ['pending' => JobApplication::whereNull('handled_at')->count()]);
    }

    public function save(Request $r)
    {
        $errors = app(ContentController::class)->persist($r, 'emp');
        if ($errors) throw ValidationException::withMessages($errors);
        $anchor = preg_replace('/[^a-z]/', '', (string) $r->input('_anchor', 'entete'));
        return redirect()->to(route('admin.emplois').'#'.$anchor)->with('status', 'Page Emplois enregistrée.');
    }

    public function applications() { return view('admin.emplois-candidatures', ['items' => JobApplication::latest()->paginate(20)]); }

    public function show(JobApplication $item) { return view('admin.emplois-candidature', ['q' => $item]); }

    public function cv(JobApplication $item)
    {
        abort_unless($item->cv_path && Storage::disk('local')->exists($item->cv_path), 404);
        return Storage::disk('local')->download($item->cv_path, $item->cv_name ?: basename($item->cv_path));
    }

    public function toggle(JobApplication $item)
    {
        $item->forceFill(['handled_at' => $item->handled_at ? null : now()])->save();
        return back()->with('status', $item->handled_at ? 'Candidature marquée comme traitée.' : 'Candidature remise en attente.');
    }

    public function destroy(JobApplication $item)
    {
        $item->delete();
        return redirect()->route('admin.emplois.applications')->with('status', 'Candidature et CV supprimés.');
    }
}
