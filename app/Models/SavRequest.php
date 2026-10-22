<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/** Demandes « Écrire au S.A.V. » de /sav (port de EcrireSavModal.tsx / app/api/ecrire-sav). */
class SavRequest extends Model
{
    protected $fillable = ['name', 'contact', 'model', 'issue', 'location', 'locale'];
    protected $casts = ['handled_at' => 'datetime'];
}
