<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Investment extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['start_date' => 'date', 'amount' => 'integer'];
    public const PROGRAMS = ['vehicule', 'sira', 'privilege', 'autre'];
    public const STATUSES = ['actif', 'en_attente', 'cloture'];
    public function user() { return $this->belongsTo(User::class); }
    public function entries() { return $this->hasMany(RevenueEntry::class)->orderByDesc('entry_date')->orderByDesc('id'); }
}
