<?php
namespace App\Http\Controllers;
use App\Models\SiraRequest;
use Illuminate\Http\Request;
/** /sira — port de components/kp/Sira.tsx ; POST = app/api/sira-contact/route.ts */
class SiraController extends Controller
{
    public function show() { return view('sira'); }

    public function store(Request $r)
    {
        $en = app()->getLocale() === 'en';
        $msg = fn ($k) => $en ? (content("sira.{$k}_en") ?: content("sira.$k")) : content("sira.$k");
        $in = collect($r->only(['name', 'country', 'city', 'phone', 'email']))->map(fn ($v) => trim((string) $v));
        $checks = ['name' => 'e_name', 'country' => 'e_country', 'city' => 'e_city', 'phone' => 'e_phone', 'email' => 'e_email'];
        foreach ($checks as $f => $k) if ($in->get($f, '') === '') return response()->json(['error' => $msg($k)], 422);
        if (! filter_var($in['email'], FILTER_VALIDATE_EMAIL)) return response()->json(['error' => $msg('e_email_bad')], 422);
        $v = validator($in->all(), ['name' => 'max:120', 'country' => 'max:120', 'city' => 'max:120', 'phone' => 'max:40', 'email' => 'max:160']);
        if ($v->fails()) return response()->json(['error' => $v->errors()->first()], 422);
        $row = SiraRequest::create($in->all() + ['locale' => app()->getLocale(), 'user_id' => $r->user()?->id]);
        return response()->json(['id' => $row->id], 201);
    }
}
