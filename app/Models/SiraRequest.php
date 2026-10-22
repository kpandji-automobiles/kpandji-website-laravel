<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/** Inscriptions au projet Kpandji Sira (formulaire de /sira). */
class SiraRequest extends Model
{
    protected $fillable = ['name', 'country', 'city', 'phone', 'email', 'locale', 'user_id'];
    protected $casts = ['handled_at' => 'datetime'];
}
