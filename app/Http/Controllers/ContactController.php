<?php
namespace App\Http\Controllers;
use App\Models\TestDriveRequest;
use Illuminate\Http\Request;
/** /contact — port de components/kp/ContactPageContent.tsx ; POST = app/api/contact/route.ts (enregistré dans test_drive_requests, type « contact »). */
class ContactController extends Controller
{
    public function show() { return view('contact-page'); }

    public function store(Request $r)
    {
        $en = app()->getLocale() === 'en';
        $msg = fn ($k) => $en ? (content("contactp.{$k}_en") ?: content("contactp.$k")) : content("contactp.$k");
        $name = trim((string) $r->input('name')); $email = strtolower(trim((string) $r->input('email')));
        $phone = trim((string) $r->input('phone')); $subject = trim((string) $r->input('subject')); $message = trim((string) $r->input('message'));
        $err = match (true) {
            $name === '' => $msg('e_name'),
            $email === '' => $msg('e_email'),
            ! preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email) => $msg('e_email_invalid'),
            $subject === '' => $msg('e_subject'),
            $message === '' => $msg('e_message'),
            mb_strlen($name) > 120 || mb_strlen($email) > 160 || mb_strlen($phone) > 40 || mb_strlen($subject) > 200 || mb_strlen($message) > 5000 => $msg('e_server'),
            default => null,
        };
        if ($err) return $r->expectsJson() ? response()->json(['error' => $err], 422) : back()->withInput()->with('contact_error', $err);
        $row = TestDriveRequest::create(['type' => 'contact', 'name' => $name, 'email' => $email, 'phone' => $phone, 'subject' => $subject, 'message' => $message]);
        return $r->expectsJson() ? response()->json(['id' => $row->id], 201) : back()->with('contact_ok', true);
    }
}
