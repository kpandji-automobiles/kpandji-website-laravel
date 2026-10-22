<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RevenueEntry extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['entry_date' => 'date', 'amount' => 'integer'];
    public const TYPES = ['revenu', 'versement', 'frais'];
    public function investment() { return $this->belongsTo(Investment::class); }
}
