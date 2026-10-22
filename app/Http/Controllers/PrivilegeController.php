<?php
namespace App\Http\Controllers;
use App\Models\PrivilegeRequest;
use Illuminate\Http\Request;
/** /privilege — port de components/kp/PrestigePrivilege.tsx ; POST = app/api/privilege-contact/route.ts */
class PrivilegeController extends Controller
{
    public function show() { return view('privilege'); }

    public function store(Request $r)
    {
        $en = app()->getLocale() === 'en';
        $msg = fn ($k) => $en ? (content("priv.{$k}_en") ?: content("priv.$k")) : content("priv.$k");
        $in = collect($r->only(['name', 'country', 'city', 'phone', 'email']))->map(fn ($v) => trim((string) $v));
        foreach (['name' => 'e_name', 'country' => 'e_country', 'city' => 'e_city', 'phone' => 'e_phone', 'email' => 'e_email'] as $f => $k)
            if ($in->get($f, '') === '') return response()->json(['error' => $msg($k)], 422);
        if (! filter_var($in['email'], FILTER_VALIDATE_EMAIL)) return response()->json(['error' => $msg('e_email_bad')], 422);
        $v = validator($in->all(), ['name' => 'max:120', 'country' => 'max:120', 'city' => 'max:120', 'phone' => 'max:40', 'email' => 'max:160']);
        if ($v->fails()) return response()->json(['error' => $v->errors()->first()], 422);
        $source = $r->input('source') === 'advisor' ? 'advisor' : 'contact';
        $row = PrivilegeRequest::create($in->all() + ['source' => $source, 'locale' => app()->getLocale(), 'user_id' => $r->user()?->id]);
        return response()->json(['id' => $row->id], 201);
    }
}
