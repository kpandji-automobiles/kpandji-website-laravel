<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Section « Modèles » de l'accueil (port de components/kp/Modeles.tsx + data/modeles.ts). */
class Modele extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['characteristics' => 'array', 'is_active' => 'boolean', 'in_showroom' => 'boolean', 'highlights' => 'array', 'gallery' => 'array'];

    public function scopeVisible($q) { return $q->where('is_active', true)->orderBy('sort')->orderBy('id'); }

    public function scopeShowroom($q) { return $q->where('in_showroom', true)->orderBy('sr_sort')->orderBy('id'); }

    public function tr(string $field): ?string
    {
        $en = app()->getLocale() === 'en';
        return $en ? ($this->{$field.'_en'} ?: $this->{$field.'_fr'}) : $this->{$field.'_fr'};
    }

    /** Lien « Découvrir » : page du modèle si elle existe et est visible, sinon le ShowRoom. */
    public function link(): string
    {
        if ($this->car_slug && Car::where('slug', $this->car_slug)->where('is_active', true)->exists()) return route('cars.show', $this->car_slug);
        return route('showroom');
    }
}
