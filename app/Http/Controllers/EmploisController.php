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
        $m = fn($k) => $en ? (content("emp.{$k}_en") ?: content("emp.$k")) : content("emp.$k");
        $fail = fn($msg) => $r->expectsJson() ? response()->json(['error' => $msg], 422) : back()->withInput()->with('emp_error', $msg);
        if (filled($r->input('website'))) { // pot de miel : réponse « succès » silencieuse, rien n'est enregistré
            return $r->expectsJson() ? response()->json(['id' => 0], 201) : back()->with('emp_ok', true);
        }
        $d = collect($r->only(['full_name', 'email', 'phone', 'position', 'cv_summary', 'education', 'skills', 'languages', 'extras']))->map(fn($v) => trim((string) $v));
        $err = match (true) {
            $d['full_name'] === '' => $m('e_name'),
            $d['email'] === '' => $m('e_email'),
            !preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $d['email']) => $m('e_email_invalid'),
            $d['position'] === '' => $m('e_position'),
            $d['cv_summary'] === '' => $m('e_cv'),
            $d['skills'] === '' => $m('e_skills'),
            default => null,
        };
        if ($err)
            return $fail($err);
        $v = validator($d->all(), ['full_name' => 'max:120', 'email' => 'max:160', 'phone' => 'max:40', 'position' => 'max:200', 'cv_summary' => 'max:6000', 'education' => 'max:3000', 'skills' => 'max:4000', 'languages' => 'max:300', 'extras' => 'max:3000']);
        if ($v->fails())
            return $fail($v->errors()->first());
        if ($r->hasFile('cv') || $r->has('cv')) {
            // Validation du fichier et de sa taille.
            $validation = validator(
                ['cv' => $r->file('cv')],
                ['cv' => 'required|file|max:2048|extensions:pdf,doc,docx']
            );

            if ($validation->fails()) {
                return $fail($m('e_file'));
            }

            $file = $r->file('cv');

            // Correspondance entre extension et type détecté.
            $allowedTypes = [
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];

            $extension = strtolower($file->getClientOriginalExtension());

            if ($file->getMimeType() !== ($allowedTypes[$extension] ?? null)) {
                return $fail($m('e_file'));
            }

            // Stockage privé après validation.
            $cv = [
                'cv_path' => $file->store('candidatures', 'local'),
                'cv_name' => mb_substr($file->getClientOriginalName(), 0, 200),
                'cv_size' => $file->getSize(),
            ];
        }
        $row = JobApplication::create($d->map(fn($x) => $x === '' ? null : $x)->all() + ($cv ?? []) + ['locale' => app()->getLocale(), 'ip' => $r->ip()]);
        return $r->expectsJson() ? response()->json(['id' => $row->id], 201) : back()->with('emp_ok', true);
    }
}
