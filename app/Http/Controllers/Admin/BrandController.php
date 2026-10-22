<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Support\Cms;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
/** Admin › Kpandji Automobiles (/kpandji-automobiles) : clés CMS « brand.* ». */
class BrandController extends Controller
{
    public function index()
    {
        $fields = Cms::fields('brand'); $values = [];
        foreach ($fields as $key => $f) $values[$key] = content($key);
        return view('admin.brand', compact('fields', 'values'));
    }

    public function save(Request $r)
    {
        $errors = app(ContentController::class)->persist($r, 'brand');
        if ($errors) throw ValidationException::withMessages($errors);
        $anchor = preg_replace('/[^a-z]/', '', (string) $r->input('_anchor', 'hero'));
        return redirect()->to(route('admin.brand').'#'.$anchor)->with('status', 'Page Kpandji Automobiles enregistrée.');
    }
}
