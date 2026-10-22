<?php
namespace App\Http\Controllers;
use App\Models\Car;
use App\Models\TestDriveRequest;
use Illuminate\Http\Request;
class SiteController extends Controller
{
    public function home() { return view('home', ['cars' => Car::visible()->get()]); }
    public function index() { return view('cars.index', ['cars' => Car::visible()->get()]); }
    public function opportunities() { return view('opportunities'); }
    public function showroom() { return view('showroom', ['models' => \App\Models\Modele::showroom()->get()]); }
    public function show(Car $car) { abort_unless($car->is_active, 404); return view('cars.show', ['car' => $car, 'm' => \App\Models\Modele::where('car_slug', $car->slug)->first(), 'others' => Car::visible()->where('id', '!=', $car->id)->take(3)->get()]); }
    public function contact(Request $r) { return view('contact', ['cars' => Car::visible()->get(), 'selected' => $r->query('modele')]); }
    public function store(Request $r)
    {
        $data = $r->validate([
            'car_id' => 'nullable|exists:cars,id',
            'type' => 'required|in:essai,information',
            'name' => 'required|string|max:120',
            'email' => 'nullable|email|max:160',
            'phone' => 'required|string|max:40',
            'preferred_date' => 'nullable|date|after_or_equal:today',
            'message' => 'nullable|string|max:2000',
        ]);
        TestDriveRequest::create($data);
        return redirect()->route('contact')->with('success', content('form.success'));
    }
}
