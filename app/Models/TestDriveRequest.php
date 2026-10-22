<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TestDriveRequest extends Model
{
    protected $guarded = [];
    protected $casts = ['preferred_date' => 'date'];
    public function car() { return $this->belongsTo(Car::class); }
}
