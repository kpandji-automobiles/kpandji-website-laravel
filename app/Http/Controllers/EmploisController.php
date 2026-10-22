<?php
namespace App\Http\Controllers;
use App\Models\JobApplication;
use Illuminate\Http\Request;
/** /emplois — port de components/kp/EmploisPageContent.tsx. La source ouvrait un brouillon mailto ; ici la candidature est d'abord enregistrée (table job_applications), aucun e-mail n'est envoyé par le serveur. */
class EmploisController extends Controller
{
    public function store(Request $r)
    {
        $en = app()->getLocale() === 'en';
        $m = fn ($k) => $en ? (content("emp.{$k}_en") ?: content("emp.$k")) : content("emp.$k");
        $fail = fn ($msg) => $r->expectsJson() ? response()->json(['error' => $msg], 422) : back()->withInput()->with('emp_error', $msg);
        if (filled($r->input('website'))) { // pot de miel : réponse « succès » silencieuse, rien n'est enregistré
            return $r->expectsJson() ? response()->json(['id' => 0], 201) : back()->with('emp_ok', true);
        }
        $d = collect($r->only(['full_name', 'email', 'phone', 'position', 'cv_summary', 'education', 'skills', 'languages', 'extras']))->map(fn ($v) => trim((string) $v));
        $err = match (true) {
            $d['full_name'] === '' => $m('e_name'),
            $d['email'] === '' => $m('e_email'),
            ! preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $d['email']) => $m('e_email_invalid'),
            $d['position'] === '' => $m('e_position'),
            $d['cv_summary'] === '' => $m('e_cv'),
            $d['skills'] === '' => $m('e_skills'),
            default => null,
        };
        if ($err) return $fail($err);
        $v = validator($d->all(), ['full_name' => 'max:120', 'email' => 'max:160', 'phone' => 'max:40', 'position' => 'max:200', 'cv_summary' => 'max:6000', 'education' => 'max:3000', 'skills' => 'max:4000', 'languages' => 'max:300', 'extras' => 'max:3000']);
        if ($v->fails()) return $fail($v->errors()->first());
        if ($r->hasFile('cv') || $r->has('cv')) {
            $f = $r->file('cv');
            if (! $f || ! $f->isValid() || $f->getSize() > 2 * 1024 * 1024 || ! in_array(strtolower($f->getClientOriginalExtension()), ['pdf', 'doc', 'docx'], true)
                || ! in_array($f->getMimeType(), ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream', 'application/x-ole-storage', 'application/CDFV2'], true)) {
                return $fail($m('e_file'));
            }
            $cv = ['cv_path' => $f->store('candidatures', 'local'), 'cv_name' => mb_substr($f->getClientOriginalName(), 0, 200), 'cv_size' => $f->getSize()];
        }
        $row = JobApplication::create($d->map(fn ($x) => $x === '' ? null : $x)->all() + ($cv ?? []) + ['locale' => app()->getLocale(), 'ip' => $r->ip()]);
        return $r->expectsJson() ? response()->json(['id' => $row->id], 201) : back()->with('emp_ok', true);
    }
}
