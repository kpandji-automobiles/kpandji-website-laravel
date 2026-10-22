<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Car extends Model
{
    protected $guarded = [];
    protected $casts = ['highlights' => 'array', 'specs' => 'array', 'versions' => 'array', 'is_active' => 'boolean'];
    public function scopeVisible($q) { return $q->where('is_active', true)->orderBy('sort_order')->orderBy('id'); }
    public function hasVersions(): bool { return count($this->versions ?? []) > 1; }
    public function getRouteKeyName(): string { return 'slug'; }

    public function discountedPrice(): ?int
    {
        if ($this->promo_price) return (int) $this->promo_price;
        $pct = (float) config('promo.reduction_percent');
        if ($this->price && $pct > 0) return (int) round($this->price * (1 - $pct / 100));
        return null;
    }
    public static function fcfa(?int $v): string
    {
        return $v ? number_format($v, 0, ',', ' ') . ' ' . config('promo.currency') : 'Prix sur demande';
    }
}
