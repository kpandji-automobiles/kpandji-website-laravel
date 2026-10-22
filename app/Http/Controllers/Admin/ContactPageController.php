<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\TestDriveRequest;
use App\Support\Cms;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
/** Admin › Contact (/contact) : clés CMS « contactp.* ». Messages : Admin › Demandes (type contact). */
class ContactPageController extends Controller
{
    public function index()
    {
        $fields = Cms::fields('contactp'); $values = [];
        foreach ($fields as $key => $f) $values[$key] = content($key);
        return view('admin.contact', compact('fields', 'values') + ['pending' => TestDriveRequest::where('type', 'contact')->whereNull('handled_at')->count()]);
    }

    public function save(Request $r)
    {
        $errors = app(ContentController::class)->persist($r, 'contactp');
        if ($errors) throw ValidationException::withMessages($errors);
        $anchor = preg_replace('/[^a-z]/', '', (string) $r->input('_anchor', 'entete'));
        return redirect()->to(route('admin.contactp').'#'.$anchor)->with('status', 'Page Contact enregistrée.');
    }
}
