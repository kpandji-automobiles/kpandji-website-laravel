<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['published' => 'boolean', 'in_nav' => 'boolean', 'is_system' => 'boolean'];

    /** Slugs réservés (routes existantes). */
    public const RESERVED = ['admin', 'modeles', 'showroom', 'essai', 'demande-essai', 'page', 'storage', 'images', 'login', 'logout', 'up', 'api', 'build', 'video.mp4'];

    public function url(): string
    {
        return url('/'.$this->slug);
    }
}
