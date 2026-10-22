<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Support\Cms;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
/** Admin › Eco-Kpandji (/ecologie) : clés CMS « eco.* ». */
class EcologieController extends Controller
{
    public function index()
    {
        $fields = Cms::fields('eco'); $values = [];
        foreach ($fields as $key => $f) $values[$key] = content($key);
        return view('admin.ecologie', compact('fields', 'values'));
    }

    public function save(Request $r)
    {
        $errors = app(ContentController::class)->persist($r, 'eco');
        if ($errors) throw ValidationException::withMessages($errors);
        $anchor = preg_replace('/[^a-z]/', '', (string) $r->input('_anchor', 'hero'));
        return redirect()->to(route('admin.eco').'#'.$anchor)->with('status', 'Page Eco-Kpandji enregistrée.');
    }
}
