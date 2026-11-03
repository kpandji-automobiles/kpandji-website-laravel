<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Modele;
use App\Models\TestDriveRequest;
use App\Support\Catalogue;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function home()
    {
        $cars = Car::visible()->get();

        // Un disque « achat par type » par catégorie, illustré par le premier modèle de la catégorie.
        return view('home', ['cars' => $cars, 'types' => $cars->filter(fn (Car $car) => filled($car->category))->unique('category')->values()]);
    }

    public function index(Request $request)
    {
        $range = Car::visible()->get();
        $filters = Catalogue::filtersFrom($request, $range);

        return view('cars.index', ['range' => $range, 'cars' => Catalogue::apply($range, $filters), 'filters' => $filters]);
    }

    public function opportunities()
    {
        return view('opportunities');
    }

    public function showroom()
    {
        return view('showroom', ['models' => Modele::showroom()->get(), 'cars' => Car::visible()->get()->keyBy('slug')]);
    }

    public function show(Car $car)
    {
        abort_unless($car->is_active, 404);

        return view('cars.show', ['car' => $car, 'm' => Modele::where('car_slug', $car->slug)->first(), 'others' => Car::visible()->where('id', '!=', $car->id)->take(3)->get()]);
    }

    public function contact(Request $r)
    {
        return view('contact', ['cars' => Car::visible()->get(), 'selected' => $r->query('modele')]);
    }

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
