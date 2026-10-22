<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/** Demandes Kpandji Privilège (formulaires « Parler à un conseiller » = advisor / « Nous contacter » = contact de /privilege). */
class PrivilegeRequest extends Model
{
    protected $fillable = ['source', 'name', 'country', 'city', 'phone', 'email', 'locale', 'user_id'];
    protected $casts = ['handled_at' => 'datetime'];
}
