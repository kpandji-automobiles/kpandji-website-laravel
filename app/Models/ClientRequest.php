<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ClientRequest extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['handled_at' => 'datetime'];
    public function user() { return $this->belongsTo(User::class); }
    public function investment() { return $this->belongsTo(Investment::class); }
}
