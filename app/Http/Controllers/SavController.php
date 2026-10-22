<?php
namespace App\Http\Controllers;
use App\Models\Modele;
use App\Models\SavRequest;
use Illuminate\Http\Request;
/** /sav — port de components/kp/ServiceApresVente.tsx ; POST = app/api/ecrire-sav/route.ts */
class SavController extends Controller
{
    public function show() { return view('sav', ['models' => Modele::orderBy('sort')->orderBy('id')->pluck('name')]); }

    public function store(Request $r)
    {
        $en = app()->getLocale() === 'en';
        $msg = fn ($k) => $en ? (content("savp.{$k}_en") ?: content("savp.$k")) : content("savp.$k");
        $in = collect($r->only(['name', 'contact', 'model', 'issue', 'location']))->map(fn ($v) => trim((string) $v));
        foreach (['name' => 'e_name', 'contact' => 'e_contact', 'model' => 'e_model', 'issue' => 'e_issue', 'location' => 'e_loc'] as $f => $k)
            if ($in->get($f, '') === '') return response()->json(['error' => $msg($k)], 422);
        $v = validator($in->all(), ['name' => 'max:120', 'contact' => 'max:160', 'model' => 'max:80', 'issue' => 'max:3000', 'location' => 'max:160']);
        if ($v->fails()) return response()->json(['error' => $v->errors()->first()], 422);
        $row = SavRequest::create($in->all() + ['locale' => app()->getLocale()]);
        return response()->json(['id' => $row->id], 201);
    }
}
