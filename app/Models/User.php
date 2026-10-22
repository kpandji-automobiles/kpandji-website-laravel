<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'account_status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public const ROLES = ['admin', 'manager', 'client', 'user'];

    public function isAdmin(): bool { return (bool) $this->is_admin || $this->role === 'admin'; }
    /** Manager OU admin (l'admin a accès à tout l'espace manager). */
    public function isActive(): bool { return $this->isAdmin() || ($this->account_status ?? 'active') === 'active'; }
    public function isManager(): bool { return $this->isAdmin() || ($this->role === 'manager' && $this->isActive()); }
    public function isClient(): bool { return ! $this->isAdmin() && $this->role === 'client' && $this->isActive(); }
    public function investments() { return $this->hasMany(Investment::class)->orderByDesc('start_date')->orderByDesc('id'); }

    /** Espace de destination après connexion (null = simple compte). */
    public function workspaceUrl(): ?string
    {
        if ($this->isAdmin()) return route('admin.dashboard');
        if (in_array($this->role, ['manager', 'client'], true) && ! $this->isActive()) return route('space.waiting');
        if ($this->role === 'manager') return route('manager.dashboard');
        if ($this->role === 'client') return route('client.dashboard');
        return null;
    }

    /** Clé du libellé du menu compte (header). */
    public function menuKey(): string
    {
        return $this->isAdmin() ? 'account_menu_admin' : ($this->role === 'manager' ? 'account_menu_manager' : 'account_menu_user');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }
}
