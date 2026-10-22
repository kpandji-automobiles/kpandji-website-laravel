<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
/** Candidature envoyée depuis /emplois. Le CV est sur le disque privé « local ». */
class JobApplication extends Model
{
    protected $fillable = ['full_name', 'email', 'phone', 'position', 'cv_summary', 'education', 'skills', 'languages', 'extras', 'cv_path', 'cv_name', 'cv_size', 'locale', 'ip'];
    protected $casts = ['handled_at' => 'datetime'];
    protected static function booted(): void
    {
        static::deleting(fn (self $a) => $a->cv_path && Storage::disk('local')->delete($a->cv_path));
    }
}
