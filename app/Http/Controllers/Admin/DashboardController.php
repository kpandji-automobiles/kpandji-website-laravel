<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Setting;
use App\Models\TestDriveRequest;
use Illuminate\Http\Request;
class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'carsTotal' => Car::count(), 'carsActive' => Car::where('is_active', true)->count(),
            'reqTotal' => TestDriveRequest::count(), 'reqPending' => TestDriveRequest::whereNull('handled_at')->count(),
            'latest' => TestDriveRequest::with('car')->latest()->take(5)->get(),
        ]);
    }
    public function promo() { return view('admin.promo'); }
    public function savePromo(Request $r)
    {
        $d = $r->validate([
            'title' => 'required|string|max:150', 'subtitle' => 'nullable|string|max:300',
            'reduction_percent' => 'nullable|numeric|min:0|max:90', 'ends_at' => 'nullable|date',
        ]);
        Setting::put('promo.title', $d['title']);
        Setting::put('promo.subtitle', $d['subtitle'] ?? '');
        Setting::put('promo.reduction_percent', (string) ($d['reduction_percent'] ?? 0));
        Setting::put('promo.ends_at', $d['ends_at'] ?? '');
        Setting::put('promo.active', $r->boolean('active') ? '1' : '0');
        return redirect()->route('admin.promo')->with('status', 'Promotion enregistrée.');
    }
    public function requests() { return view('admin.requests', ['items' => TestDriveRequest::with('car')->latest()->paginate(25)]); }
    public function destroyRequest(TestDriveRequest $item) { $item->delete(); return back()->with('status', 'Demande supprimée.'); }
    public function toggleHandled(TestDriveRequest $item)
    {
        $item->forceFill(['handled_at' => $item->handled_at ? null : now()])->save();
        return back()->with('status', $item->handled_at ? 'Demande marquée comme traitée.' : 'Demande remise en attente.');
    }
}
